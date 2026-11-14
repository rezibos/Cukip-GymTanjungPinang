<?php

namespace Tests\Feature;

use App\Filament\Resources\ClassBookings\Pages\ListClassBookings;
use App\Filament\Resources\ClassSessions\Pages\ListClassSessions;
use App\Filament\Resources\Facilities\Pages\ListFacilities;
use App\Filament\Resources\GymClasses\Pages\ListGymClasses;
use App\Filament\Resources\MembershipPlans\Pages\ListMembershipPlans;
use App\Filament\Resources\Memberships\Pages\ListMemberships;
use App\Filament\Resources\Reviews\Pages\ListReviews;
use App\Filament\Resources\Transactions\Pages\ListTransactions;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Filament\Widgets\LatestTransactions;
use App\Filament\Widgets\RevenueChart;
use App\Filament\Widgets\StatsOverview;
use App\Filament\Widgets\TodayClasses;
use App\Models\GymClass;
use App\Models\Membership;
use App\Models\MembershipPlan;
use App\Models\User;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\Width;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_from_admin_to_the_shared_login(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
        $this->get('/admin/login')->assertRedirect(route('login'))
            ->assertSessionHas('url.intended', url('/admin'));
        $this->get(route('login'))->assertOk()->assertSee('Masuk akun');
    }

    #[TestWith(['admin', 'filament.admin.pages.dashboard'])]
    #[TestWith(['user', 'filament.member.pages.dashboard'])]
    public function test_shared_login_redirects_each_role_to_its_default_page(string $role, string $route): void
    {
        $user = User::factory()->create(['role' => $role]);

        $response = $this->post(route('login'), ['email' => $user->email, 'password' => 'password']);

        $response->assertRedirect(route($route));
        $this->assertAuthenticatedAs($user);
    }

    public function test_shared_login_returns_admins_to_the_requested_resource(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->get('/admin/transactions')->assertRedirect('/admin/login');
        $this->get('/admin/login')->assertRedirect(route('login'));

        $response = $this->post(route('login'), ['email' => $admin->email, 'password' => 'password']);

        $response->assertRedirect(url('/admin/transactions'));
        $this->assertAuthenticatedAs($admin);
        $this->get('/admin/transactions')->assertOk();
    }

    public function test_invalid_admin_credentials_keep_the_shared_login_and_intended_destination(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->withSession(['url.intended' => url('/admin/transactions')])
            ->from(route('login'))
            ->post(route('login'), ['email' => $admin->email, 'password' => 'incorrect-password']);

        $response->assertRedirect(route('login'))
            ->assertSessionHasErrors(['email' => 'Email atau password salah.'])
            ->assertSessionHas('url.intended', url('/admin/transactions'));
        $this->assertGuest();
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
            ->assertSee('/admin/class-sessions?action=create', false)->assertSee('/admin/transactions', false)
            ->assertSee('data-admin-sidebar-toggle', false)
            ->assertSee('aria-controls="fi-main-sidebar"', false)
            ->assertSee('Tutup menu navigasi')->assertSee('Buka menu navigasi');
    }

    /**
     * @param  class-string<ListRecords>  $pageClass
     */
    #[DataProvider('modalCreatePages')]
    public function test_admin_create_actions_open_indonesian_modals(string $pageClass, string $heading): void
    {
        $this->signInAdmin();

        $component = Livewire::test($pageClass)->assertActionExists('create');
        $action = $component->instance()->getAction('create');

        $this->assertInstanceOf(CreateAction::class, $action);
        $this->assertSame('Tambah', $action->getLabel());
        $this->assertTrue($action->hasModal());
        $this->assertSame($heading, $action->getModalHeading());
        $this->assertNull($action->getModalIcon());
        $this->assertSame('Simpan', $action->getModalSubmitActionLabel());
        $this->assertSame('Batal', $action->getModalCancelActionLabel());
        $this->assertSame(Alignment::End, $action->getModalFooterActionsAlignment());
        $this->assertSame(Width::FourExtraLarge, $action->getModalWidth());
        $this->assertTrue($action->isModalHeaderSticky());
        $this->assertTrue($action->isModalFooterSticky());
        $this->assertFalse($action->canCreateAnother());

        $component->assertSee('wire:click="mountAction(\'create\')"', false);
        $component->mountAction('create')
            ->assertActionMounted('create');
    }

    public function test_membership_creation_dispatches_sweetalert_success_instead_of_a_filament_notification(): void
    {
        $this->signInAdmin();
        $member = User::factory()->create();
        $plan = MembershipPlan::factory()->create();

        Livewire::test(ListMemberships::class)
            ->callAction('create', [
                'user_id' => $member->id,
                'membership_plan_id' => $plan->id,
                'status' => 'active',
                'start_date' => '2026-11-09',
                'end_date' => '2026-12-09',
            ])
            ->assertDispatched('admin-record-created')
            ->assertNotNotified();

        $this->assertDatabaseHas('memberships', [
            'user_id' => $member->id,
            'membership_plan_id' => $plan->id,
            'status' => 'active',
        ]);
    }

    public function test_admin_can_create_a_membership_package_with_promotional_benefits(): void
    {
        $this->signInAdmin();

        Livewire::test(ListMembershipPlans::class)
            ->callAction('create', [
                'name' => '6 Month',
                'duration_days' => 180,
                'price' => 2400000,
                'original_price' => 3000000,
                'discount_label' => '20% OFF',
                'badge' => 'Pilihan Hemat',
                'description' => 'Bangun rutinitas latihan selama enam bulan.',
                'benefits' => [
                    ['benefit' => '2 sesi personal trainer'],
                    ['benefit' => 'Gratis kaos CukipGym'],
                ],
                'is_active' => true,
            ])
            ->assertHasNoFormErrors();

        $plan = MembershipPlan::query()->where('name', '6 Month')->firstOrFail();

        $this->assertSame(3000000, $plan->original_price);
        $this->assertSame('20% OFF', $plan->discount_label);
        $this->assertSame('Pilihan Hemat', $plan->badge);
        $this->assertSame(['2 sesi personal trainer', 'Gratis kaos CukipGym'], $plan->benefits);
    }

    public function test_selecting_a_membership_plan_automatically_fills_its_dates(): void
    {
        Carbon::setTestNow('2026-11-09 12:00:00');

        try {
            $this->signInAdmin();
            $plan = MembershipPlan::factory()->create(['duration_days' => 7]);

            $component = Livewire::test(ListMemberships::class)
                ->mountAction('create')
                ->fillForm(['membership_plan_id' => $plan->id])
                ->assertActionDataSet([
                    'start_date' => '2026-11-09',
                    'end_date' => '2026-11-16',
                ]);

            $component
                ->fillForm(['start_date' => '2026-12-01'])
                ->assertActionDataSet(['end_date' => '2026-12-08']);
        } finally {
            Carbon::setTestNow();
        }
    }

    #[TestWith(['2026-11-01', '2026-11-01'])]
    #[TestWith(['2026-11-01', '2026-10-31'])]
    public function test_membership_end_date_must_be_after_the_start_date(string $startDate, string $endDate): void
    {
        $this->signInAdmin();
        $member = User::factory()->create();
        $plan = MembershipPlan::factory()->create();

        Livewire::test(ListMemberships::class)
            ->callAction('create', [
                'user_id' => $member->id,
                'membership_plan_id' => $plan->id,
                'status' => 'active',
                'start_date' => $startDate,
                'end_date' => $endDate,
            ])
            ->assertHasFormErrors(['end_date' => 'after'])
            ->assertNotDispatched('admin-record-created');

        $this->assertDatabaseCount('memberships', 0);
    }

    #[TestWith(['2026-11-02'])]
    #[TestWith(['2027-01-01'])]
    public function test_membership_end_date_accepts_the_next_day_or_a_later_year(string $endDate): void
    {
        $this->signInAdmin();
        $member = User::factory()->create();
        $plan = MembershipPlan::factory()->create();

        Livewire::test(ListMemberships::class)
            ->callAction('create', [
                'user_id' => $member->id,
                'membership_plan_id' => $plan->id,
                'status' => 'active',
                'start_date' => '2026-11-01',
                'end_date' => $endDate,
            ])
            ->assertHasNoFormErrors()
            ->assertDispatched('admin-record-created');

        $this->assertDatabaseHas('memberships', [
            'user_id' => $member->id,
            'start_date' => '2026-11-01 00:00:00',
            'end_date' => $endDate.' 00:00:00',
        ]);
    }

    public function test_membership_deletion_uses_sweetalert_confirmation_and_success_event(): void
    {
        $admin = $this->signInAdmin();
        $membership = $this->createMembership($admin);
        $component = Livewire::test(ListMemberships::class);
        $action = $component->instance()->getTable()->getAction('delete')->record($membership);

        $this->assertFalse($action->isConfirmationRequired());
        $this->assertFalse($action->hasModal());
        $this->assertNull($action->getKeyBindings());
        $this->assertStringContainsString('confirmAdminDelete', $action->getAlpineClickHandler());
        $this->assertStringContainsString("mountAction('delete'", $action->getAlpineClickHandler());

        $component
            ->callAction(TestAction::make('delete')->table($membership))
            ->assertDispatched('admin-record-deleted')
            ->assertNotNotified();

        $this->assertModelMissing($membership);
    }

    /**
     * @param  class-string<ListRecords>  $pageClass
     */
    #[DataProvider('modalEditPages')]
    public function test_admin_edit_actions_open_indonesian_modals(string $pageClass, string $heading, string $label): void
    {
        $this->signInAdmin();

        $action = Livewire::test($pageClass)->instance()->getTable()->getAction('edit');

        $this->assertInstanceOf(EditAction::class, $action);
        $this->assertSame($label, $action->getLabel());
        $this->assertTrue($action->hasModal());
        $this->assertNull($action->getUrl());
        $this->assertSame($heading, $action->getModalHeading());
        $this->assertNull($action->getModalIcon());
        $this->assertSame('Simpan', $action->getModalSubmitActionLabel());
        $this->assertSame('Batal', $action->getModalCancelActionLabel());
        $this->assertSame(Alignment::End, $action->getModalFooterActionsAlignment());
        $this->assertSame(Width::FourExtraLarge, $action->getModalWidth());
        $this->assertTrue($action->isModalHeaderSticky());
        $this->assertTrue($action->isModalFooterSticky());
    }

    /**
     * @param  class-string<ListRecords>  $pageClass
     */
    #[DataProvider('nonClickableAdminTables')]
    public function test_admin_table_rows_are_not_clickable(string $pageClass): void
    {
        $this->signInAdmin();

        $table = Livewire::test($pageClass)->instance()->getTable();

        $this->assertTrue($table->hasCustomRecordUrl());
        $this->assertNull($table->getRecordUrl([]));
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

    public function test_manual_transaction_creation_route_is_not_exposed(): void
    {
        $this->signInAdmin();

        $this->get('/admin/transactions/create')->assertNotFound();
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

    /**
     * @return array<string, array{class-string<ListRecords>, string}>
     */
    public static function modalCreatePages(): array
    {
        return [
            'membership' => [ListMemberships::class, 'Tambah Membership'],
            'pengguna' => [ListUsers::class, 'Tambah Pengguna'],
            'sesi kelas' => [ListClassSessions::class, 'Tambah Sesi Kelas'],
            'booking kelas' => [ListClassBookings::class, 'Tambah Booking Kelas'],
            'jenis kelas' => [ListGymClasses::class, 'Tambah Jenis Kelas'],
            'fasilitas' => [ListFacilities::class, 'Tambah Fasilitas'],
            'paket membership' => [ListMembershipPlans::class, 'Tambah Paket Membership'],
        ];
    }

    /**
     * @return array<string, array{class-string<ListRecords>, string, string}>
     */
    public static function modalEditPages(): array
    {
        return [
            'membership' => [ListMemberships::class, 'Ubah Membership', 'Ubah'],
            'pengguna' => [ListUsers::class, 'Ubah Pengguna', 'Ubah'],
            'sesi kelas' => [ListClassSessions::class, 'Ubah Sesi Kelas', 'Ubah'],
            'jenis kelas' => [ListGymClasses::class, 'Ubah Jenis Kelas', 'Ubah'],
            'fasilitas' => [ListFacilities::class, 'Ubah Fasilitas', 'Ubah'],
            'paket membership' => [ListMembershipPlans::class, 'Ubah Paket Membership', 'Ubah'],
            'review' => [ListReviews::class, 'Ubah Review', 'Ubah'],
            'transaksi' => [ListTransactions::class, 'Detail Transaksi', 'Detail'],
        ];
    }

    /**
     * @return array<string, array{class-string<ListRecords>}>
     */
    public static function nonClickableAdminTables(): array
    {
        return [
            'transaksi' => [ListTransactions::class],
            'membership' => [ListMemberships::class],
            'pengguna' => [ListUsers::class],
            'sesi kelas' => [ListClassSessions::class],
            'booking kelas' => [ListClassBookings::class],
            'jenis kelas' => [ListGymClasses::class],
            'fasilitas' => [ListFacilities::class],
            'paket membership' => [ListMembershipPlans::class],
            'review' => [ListReviews::class],
        ];
    }
}
