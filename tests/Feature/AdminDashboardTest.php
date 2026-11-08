<?php

namespace Tests\Feature;

use App\Filament\Resources\Transactions\Pages\ListTransactions;
use App\Filament\Widgets\LatestTransactions;
use App\Filament\Widgets\RevenueChart;
use App\Filament\Widgets\StatsOverview;
use App\Filament\Widgets\TodayClasses;
use App\Models\GymClass;
use App\Models\Membership;
use App\Models\MembershipPlan;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_the_admin_login(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
    }

    public function test_regular_users_cannot_access_admin_data(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($user)->get('/admin')->assertForbidden();
        $this->get('/admin/transactions')->assertForbidden();
    }

    public function test_admin_dashboard_renders_the_brand_navigation_and_operational_shortcuts(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'name' => 'Admin Pinang']);

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertOk()->assertSee('CukipGym')->assertSee('Ringkasan operasional.')
            ->assertSee('Admin Pinang')->assertSee('Operasional')->assertSee('Konten website')
            ->assertSee('Periksa transaksi')->assertSee('Tambah sesi')->assertSee('Lihat website')
            ->assertSee('/admin/class-sessions/create', false)->assertSee('/admin/transactions', false)
            ->assertSee('data-admin-sidebar-toggle', false)
            ->assertSee('aria-controls="fi-main-sidebar"', false)
            ->assertSee('Tutup menu navigasi')->assertSee('Buka menu navigasi');
    }

    #[TestWith(['transactions'])]
    #[TestWith(['memberships'])]
    #[TestWith(['users'])]
    #[TestWith(['class-sessions'])]
    #[TestWith(['class-bookings'])]
    #[TestWith(['gym-classes'])]
    #[TestWith(['facilities'])]
    #[TestWith(['membership-plans'])]
    #[TestWith(['reviews'])]
    #[TestWith(['manage-gym-profile'])]
    public function test_admin_resource_pages_render_with_the_shared_theme(string $path): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->get('/admin/'.$path)->assertOk()->assertSee('CukipGym');
    }

    public function test_stat_cards_show_actual_paid_revenue_and_current_operational_counts(): void
    {
        $this->travelTo(Carbon::parse('2026-11-08 10:00:00'));
        $admin = $this->signInAdmin();
        $membership = $this->createMembership($admin);
        $membership->transactions()->create(['user_id' => $admin->id, 'amount' => 250000, 'status' => 'paid', 'paid_at' => now()]);
        $membership->transactions()->create(['user_id' => $admin->id, 'amount' => 999999, 'status' => 'pending']);
        $class = GymClass::factory()->create();
        $session = $class->sessions()->create(['date' => '2026-11-08', 'capacity' => 10]);
        $admin->classBookings()->create(['class_session_id' => $session->id, 'status' => 'booked']);

        Livewire::test(StatsOverview::class)->assertSee('Rp250.000')->assertSee('Member Aktif')
            ->assertSee('Perlu Konfirmasi')->assertSee('Booking Kelas Hari Ini')
            ->assertSee('/admin/memberships', false)->assertSee('/admin/transactions', false);
    }

    public function test_revenue_chart_has_fourteen_days_and_excludes_unpaid_and_out_of_range_payments(): void
    {
        $this->travelTo(Carbon::parse('2026-11-08 10:00:00'));
        $admin = $this->signInAdmin();
        $membership = $this->createMembership($admin);
        foreach ([
            ['paid', '2026-11-08 08:00:00', 250000],
            ['paid', '2026-11-07 08:00:00', 500000],
            ['pending', '2026-11-08 08:00:00', 999999],
            ['rejected', '2026-11-08 08:00:00', 888888],
            ['paid', '2026-10-24 08:00:00', 777777],
            ['paid', '2026-11-09 08:00:00', 666666],
        ] as [$status, $paidAt, $amount]) {
            $membership->transactions()->create(['user_id' => $admin->id, 'status' => $status, 'paid_at' => $paidAt, 'amount' => $amount]);
        }

        $chart = new class extends RevenueChart
        {
            public function chartData(): array
            {
                return $this->getData();
            }
        };
        $data = $chart->chartData();

        $this->assertCount(14, $data['labels']);
        $this->assertSame('26/10', $data['labels'][0]);
        $this->assertSame('08/11', $data['labels'][13]);
        $this->assertSame([0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 500000, 250000], $data['datasets'][0]['data']);
        Livewire::test(RevenueChart::class)->assertSee('Arus pendapatan');
    }

    public function test_today_widget_shows_only_todays_sessions_and_counts_active_bookings(): void
    {
        $this->travelTo(Carbon::parse('2026-11-08 10:00:00'));
        $admin = $this->signInAdmin();
        $class = GymClass::factory()->create(['name' => 'Kelas Hari Ini']);
        $session = $class->sessions()->create(['date' => '2026-11-08', 'capacity' => 10]);
        $admin->classBookings()->create(['class_session_id' => $session->id, 'status' => 'booked']);
        $otherUser = User::factory()->create();
        $otherUser->classBookings()->create(['class_session_id' => $session->id, 'status' => 'cancelled']);
        $tomorrowClass = GymClass::factory()->create(['name' => 'Kelas Besok']);
        $tomorrowSession = $tomorrowClass->sessions()->create(['date' => '2026-11-09', 'capacity' => 10]);

        Livewire::test(TodayClasses::class)->assertSee('Kelas Hari Ini')->assertSee('1 / 10')
            ->assertCanSeeTableRecords([$session])->assertCanNotSeeTableRecords([$tomorrowSession]);
    }

    public function test_recent_transactions_use_readable_statuses_and_link_to_the_record(): void
    {
        $this->travelTo(Carbon::parse('2026-11-08 10:00:00'));
        $admin = $this->signInAdmin();
        $membership = $this->createMembership($admin);
        $transaction = $membership->transactions()->create(['user_id' => $admin->id, 'amount' => 250000, 'status' => 'paid', 'paid_at' => now()]);

        Livewire::test(LatestTransactions::class)->assertCanSeeTableRecords([$transaction])
            ->assertSee('Lunas')->assertSee('Rp250.000')
            ->assertSee('/admin/transactions/'.$transaction->id.'/edit', false);
    }

    public function test_admin_name_is_escaped_in_the_dashboard_header(): void
    {
        $name = '<script>alert("admin")</script>';
        $admin = User::factory()->create(['role' => 'admin', 'name' => $name]);

        $this->actingAs($admin)->get('/admin')->assertOk()->assertSee($name)->assertDontSee($name, false);
    }

    public function test_payment_proof_is_explained_and_manual_transaction_creation_is_not_offered(): void
    {
        $admin = $this->signInAdmin();
        $membership = $this->createMembership($admin);
        $missingProof = $membership->transactions()->create(['user_id' => $admin->id, 'amount' => 250000, 'status' => 'pending']);
        $uploadedProof = $membership->transactions()->create(['user_id' => $admin->id, 'amount' => 250000, 'status' => 'pending', 'payment_proof' => 'payment-proofs/contoh.jpg']);

        Livewire::test(ListTransactions::class)
            ->assertTableColumnStateSet('payment_proof', 'Belum ada', $missingProof)
            ->assertTableColumnStateSet('payment_proof', 'Diunggah', $uploadedProof)
            ->assertSee('Cari invoice atau member...')
            ->assertSee('Hapus')
            ->assertActionDoesNotExist('create');
    }

    private function signInAdmin(): User
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);
        Filament::setCurrentPanel(Filament::getPanel('admin'));
        Filament::bootCurrentPanel();
        Livewire::withoutLazyLoading();

        return $admin;
    }

    private function createMembership(User $user): Membership
    {
        $plan = MembershipPlan::factory()->create();

        return $user->memberships()->create(['membership_plan_id' => $plan->id, 'status' => 'active', 'start_date' => '2026-11-01', 'end_date' => '2026-11-30']);
    }
}
