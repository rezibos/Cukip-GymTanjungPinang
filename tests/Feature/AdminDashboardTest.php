<?php

namespace Tests\Feature;

use App\Enums\FacilityCategory;
use App\Filament\Pages\Dashboard as AdminDashboard;
use App\Filament\Resources\ClassBookings\Pages\ListClassBookings;
use App\Filament\Resources\ClassSessions\Pages\ListClassSessions;
use App\Filament\Resources\Facilities\Pages\ListFacilities;
use App\Filament\Resources\GymClasses\Pages\ListGymClasses;
use App\Filament\Resources\GymLocations\Pages\ListGymLocations;
use App\Filament\Resources\MembershipPlans\Pages\ListMembershipPlans;
use App\Filament\Resources\Memberships\Pages\ListMemberships;
use App\Filament\Resources\PersonalTrainers\Pages\ListPersonalTrainers;
use App\Filament\Resources\Reviews\Pages\ListReviews;
use App\Filament\Resources\TrainerBookings\Pages\ListTrainerBookings;
use App\Filament\Resources\TrainerSlots\Pages\ListTrainerSlots;
use App\Filament\Resources\TrainerSlots\TrainerSlotResource;
use App\Filament\Resources\Transactions\Pages\ListTransactions;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Filament\Widgets\LatestTransactions;
use App\Filament\Widgets\RevenueChart;
use App\Filament\Widgets\StatsOverview;
use App\Filament\Widgets\TodayClasses;
use App\Models\Facility;
use App\Models\GymClass;
use App\Models\Membership;
use App\Models\MembershipPlan;
use App\Models\Payment;
use App\Models\PersonalTrainer;
use App\Models\TrainerBooking;
use App\Models\TrainerSlot;
use App\Models\Transaction;
use App\Models\User;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\Testing\TestAction;
use Filament\Actions\ViewAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\Width;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
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

    public function test_admin_dashboard_renders_the_operational_summary_layout(): void
    {
        $this->travelTo(Carbon::parse('2026-11-21 10:00:00'));
        $admin = User::factory()->create(['role' => 'admin', 'name' => 'Admin Pinang']);
        $member = User::factory()->create();
        $membership = $this->createMembership($member);
        $membership->transactions()->create([
            'user_id' => $member->id,
            'amount' => 1_700_000,
            'status' => 'paid',
            'paid_at' => '2026-11-15 10:00:00',
        ]);

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertOk()->assertSee('CukipGym')->assertSee('Ringkasan operasional.')
            ->assertSee('fi-breadcrumbs', false)
            ->assertSee('Dashboard admin')
            ->assertSee('Operasional')->assertSee('Konten website')
            ->assertSee('Member Aktif')->assertSee('Instruktur Aktif')
            ->assertSee('Income Periode')->assertSee('Booking Kelas Periode')
            ->assertSee('Periode ringkasan')->assertSee('Bulan ini')
            ->assertSee('Rangkuman membership')->assertSee('Membership chart')
            ->assertSee('Instruktur chart')->assertSee('Total income')->assertSee('Kelas hari ini')
            ->assertDontSee('Pilih warna atau nama untuk melihat detail.')
            ->assertSee('aria-label="November 2026: Rp1.700.000"', false)
            ->assertSee('income-chart-tooltip', false)
            ->assertSee('cx="30"', false)
            ->assertSee('cx="690"', false)
            ->assertSee('selectItem(0)', false)
            ->assertDontSee('Halo, Admin Pinang.')->assertDontSee('Transaksi Terbaru')
            ->assertSee('data-admin-sidebar-toggle', false)
            ->assertSee('aria-controls="fi-main-sidebar"', false)
            ->assertSee('Tutup menu navigasi')->assertSee('Buka menu navigasi');
    }

    public function test_collapsed_active_navigation_group_keeps_the_current_page_visible(): void
    {
        $theme = file_get_contents(resource_path('css/filament/admin/theme.css'));

        $this->assertStringContainsString('.fi-sidebar-group.admin-sidebar-current-only > .fi-sidebar-group-items', $theme);
        $this->assertStringContainsString('.fi-sidebar-group.admin-sidebar-collapsed > .fi-sidebar-group-items', $theme);
        $this->assertStringContainsString('.fi-sidebar-item:not(.fi-active):not(.fi-sidebar-item-has-active-child-items)', $theme);

        $script = file_get_contents(resource_path('js/filament/admin.js'));

        $this->assertStringContainsString('adminCurrentOnlySidebarGroups', $script);
        $this->assertStringContainsString('.fi-sidebar-item-btn[aria-current="page"]', $script);
        $this->assertStringContainsString('event.stopImmediatePropagation();', $script);
        $this->assertStringContainsString("document.addEventListener('livewire:navigated', syncSidebarCurrentOnlyGroups)", $script);
        $this->assertStringNotContainsString('sidebarStore.collapsedGroups = [...sidebarStore.collapsedGroups, label]', $script);
    }

    public function test_admin_dashboard_period_controls_cross_month_and_year_boundaries(): void
    {
        Carbon::setTestNow('2026-01-15 12:00:00');

        try {
            $this->signInAdmin();

            Livewire::test(AdminDashboard::class)
                ->assertSet('summaryMonth', 1)
                ->assertSet('summaryYear', 2026)
                ->assertSet('incomeYear', 2026)
                ->call('previousSummaryMonth')
                ->assertSet('summaryMonth', 12)
                ->assertSet('summaryYear', 2025)
                ->call('nextSummaryMonth')
                ->assertSet('summaryMonth', 1)
                ->assertSet('summaryYear', 2026)
                ->call('previousIncomeYear')
                ->assertSet('incomeYear', 2025)
                ->call('nextIncomeYear')
                ->assertSet('incomeYear', 2026);
        } finally {
            Carbon::setTestNow();
        }
    }

    public function test_admin_dashboard_summary_can_be_filtered_by_week_month_year_or_custom_dates(): void
    {
        $this->travelTo(Carbon::parse('2026-11-21 10:00:00'));
        $admin = $this->signInAdmin();
        $membership = $this->createMembership($admin);

        foreach ([
            ['amount' => 100000, 'paid_at' => '2026-11-18 10:00:00'],
            ['amount' => 200000, 'paid_at' => '2026-11-03 10:00:00'],
            ['amount' => 300000, 'paid_at' => '2026-04-10 10:00:00'],
            ['amount' => 400000, 'paid_at' => '2025-12-20 10:00:00'],
        ] as $transaction) {
            $membership->transactions()->create([
                'user_id' => $admin->id,
                'amount' => $transaction['amount'],
                'status' => 'paid',
                'paid_at' => $transaction['paid_at'],
            ]);
        }

        $component = Livewire::test(AdminDashboard::class);

        $component->set('dashboardPeriod', 'week');
        $this->assertSame(100000, $component->instance()->dashboardData()['stats']['monthlyIncome']);

        $component->set('dashboardPeriod', 'month');
        $this->assertSame(300000, $component->instance()->dashboardData()['stats']['monthlyIncome']);

        $component->set('dashboardPeriod', 'year');
        $this->assertSame(600000, $component->instance()->dashboardData()['stats']['monthlyIncome']);

        $component
            ->set('dashboardPeriod', 'custom')
            ->set('dashboardStartDate', '2026-11-01')
            ->set('dashboardEndDate', '2026-11-10');

        $this->assertSame(200000, $component->instance()->dashboardData()['stats']['monthlyIncome']);
    }

    public function test_admin_can_add_a_personal_trainer_without_certificate_photos(): void
    {
        $this->signInAdmin();

        Livewire::test(ListPersonalTrainers::class)
            ->callAction('create', [
                'name' => 'Kak Adi',
                'specialization' => 'Strength Training',
                'description' => 'Pendampingan teknik latihan kekuatan untuk pemula.',
                'experience_years' => 4,
                'certifications' => [
                    ['name' => 'Strength Coach', 'document' => null],
                    ['name' => 'Certified Personal Trainer', 'document' => null],
                ],
                'session_price' => 200000,
                'session_minutes' => 120,
                'is_active' => true,
            ])
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('personal_trainers', [
            'name' => 'Kak Adi',
            'session_price' => 200000,
            'session_minutes' => 120,
        ]);
        $this->assertSame(
            ['Strength Coach', 'Certified Personal Trainer'],
            collect(PersonalTrainer::where('name', 'Kak Adi')->firstOrFail()->certificationItems())
                ->pluck('name')
                ->all(),
        );
        $this->get(route('classes.index'))->assertOk()->assertSee('Kak Adi');
    }

    public function test_personal_trainer_create_modal_uses_the_wide_styled_layout(): void
    {
        $this->signInAdmin();

        $component = Livewire::test(ListPersonalTrainers::class);
        $action = $component->instance()->getAction('create');

        $this->assertSame(Alignment::End, $action->getModalFooterActionsAlignment());
        $this->assertSame(Width::FourExtraLarge, $action->getModalWidth());
        $this->assertTrue($action->isModalHeaderSticky());
        $this->assertTrue($action->isModalFooterSticky());
        $this->assertSame(
            ['class' => 'personal-trainer-form-modal'],
            $action->getExtraModalWindowAttributes(),
        );
    }

    public function test_personal_trainer_edit_modal_hydrates_legacy_certificates(): void
    {
        $this->signInAdmin();
        $trainer = PersonalTrainer::factory()->create([
            'certifications' => ['Strength Coach', 'Mobility Coach'],
            'certification_documents' => ['personal-trainers/certificates/strength.jpg'],
        ]);

        Livewire::test(ListPersonalTrainers::class)
            ->mountAction(TestAction::make('edit')->table($trainer))
            ->assertActionDataSet(function (array $state): array {
                $this->assertSame(
                    ['Strength Coach', 'Mobility Coach'],
                    collect($state['certifications'])->pluck('name')->values()->all(),
                );

                return [];
            });
    }

    public function test_admin_can_add_a_facility_to_a_selected_category(): void
    {
        Storage::fake('public');
        $this->signInAdmin();

        Livewire::test(ListFacilities::class)
            ->callAction('create', [
                'name' => 'Cable Crossover',
                'category' => FacilityCategory::Strength->value,
                'description' => 'Mesin kabel untuk variasi latihan kekuatan.',
                'image_path' => UploadedFile::fake()->image('cable-crossover.jpg', 1200, 900),
            ])
            ->assertHasNoFormErrors();

        $facility = Facility::query()->where('name', 'Cable Crossover')->firstOrFail();

        $this->assertSame(FacilityCategory::Strength, $facility->category);
        $this->assertNotNull($facility->image_path);
        Storage::disk('public')->assertExists($facility->image_path);
        $this->get(route('facilities.index'))
            ->assertOk()
            ->assertSeeInOrder(['id="fasilitas-kekuatan"', 'Cable Crossover'], false);
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

    public function test_user_create_modal_shows_helpful_input_placeholders(): void
    {
        $form = file_get_contents(app_path('Filament/Resources/Users/Schemas/UserForm.php'));

        $this->assertStringContainsString("->placeholder('Contoh: Fachrezi Bachri')", $form);
        $this->assertStringContainsString("->placeholder('Contoh: fachrezi@email.com')", $form);
        $this->assertStringContainsString("->placeholder('Contoh: 081234567890')", $form);
        $this->assertStringContainsString("->placeholder('Minimal 8 karakter')", $form);
    }

    public function test_admin_text_fields_receive_contextual_placeholders(): void
    {
        $this->assertSame('Contoh: Kak Bayu', TextInput::make('name')->label('Nama pelatih')->getPlaceholder());
        $this->assertSame('Contoh: Functional Training', TextInput::make('specialization')->getPlaceholder());
        $this->assertSame('Contoh: 150000', TextInput::make('session_price')->getPlaceholder());
        $this->assertSame(
            'Jelaskan informasi yang perlu diketahui pengunjung.',
            Textarea::make('description')->getPlaceholder(),
        );
        $this->assertSame(
            'Placeholder khusus',
            TextInput::make('name')->placeholder('Placeholder khusus')->getPlaceholder(),
        );
    }

    public function test_membership_creation_dispatches_sweetalert_success_instead_of_a_filament_notification(): void
    {
        $this->signInAdmin();
        $member = User::factory()->create();
        $plan = MembershipPlan::factory()->create();

        Livewire::test(ListMemberships::class)
            ->assertTableActionExists('create')
            ->callTableAction('create', data: [
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

    #[TestWith(['active', '2026-11-21', '2026-12-21'])]
    #[TestWith(['upcoming', '2026-11-25', '2026-12-25'])]
    public function test_manual_active_or_upcoming_membership_creates_paid_transaction_and_income(
        string $status,
        string $startDate,
        string $endDate,
    ): void {
        $this->travelTo(Carbon::parse('2026-11-21 10:00:00'));
        $this->signInAdmin();
        $member = User::factory()->create();
        $plan = MembershipPlan::factory()->create(['price' => 475000]);

        Livewire::test(ListMemberships::class)
            ->callAction('create', [
                'user_id' => $member->id,
                'membership_plan_id' => $plan->id,
                'status' => $status,
                'payment_method' => 'manual',
                'payment_channel' => 'qris',
                'start_date' => $startDate,
                'end_date' => $endDate,
            ])
            ->assertHasNoFormErrors();

        $membership = Membership::query()->whereBelongsTo($member)->sole();

        $this->assertDatabaseHas('transactions', [
            'user_id' => $member->id,
            'membership_id' => $membership->id,
            'quantity' => 1,
            'amount' => 475000,
            'payment_method' => 'manual',
            'payment_channel' => 'qris',
            'status' => 'paid',
            'paid_at' => '2026-11-21 10:00:00',
        ]);

        $this->assertSame(
            475000,
            Livewire::test(AdminDashboard::class)->instance()->dashboardData()['stats']['monthlyIncome'],
        );
    }

    public function test_manual_pending_membership_is_recorded_without_counting_as_income(): void
    {
        $this->travelTo(Carbon::parse('2026-11-21 10:00:00'));
        $this->signInAdmin();
        $member = User::factory()->create();
        $plan = MembershipPlan::factory()->create(['price' => 325000]);

        Livewire::test(ListMemberships::class)
            ->callAction('create', [
                'user_id' => $member->id,
                'membership_plan_id' => $plan->id,
                'status' => 'pending_payment',
                'payment_method' => 'midtrans',
                'start_date' => '2026-11-21',
                'end_date' => '2026-12-21',
            ])
            ->assertHasNoFormErrors();

        $membership = Membership::query()->whereBelongsTo($member)->sole();

        $this->assertDatabaseHas('transactions', [
            'user_id' => $member->id,
            'membership_id' => $membership->id,
            'amount' => 325000,
            'payment_method' => 'midtrans',
            'payment_channel' => null,
            'status' => 'pending',
            'paid_at' => null,
        ]);
        $this->assertSame(
            0,
            Livewire::test(AdminDashboard::class)->instance()->dashboardData()['stats']['monthlyIncome'],
        );
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
        $this->assertSame('Hapus', $action->getLabel());
        $this->assertNull($action->getKeyBindings());
        $this->assertStringContainsString('confirmAdminDelete', $action->getAlpineClickHandler());
        $this->assertStringContainsString("mountAction('delete'", $action->getAlpineClickHandler());

        $component
            ->callAction(TestAction::make('delete')->table($membership))
            ->assertDispatched('admin-record-deleted')
            ->assertNotNotified();

        $this->assertModelMissing($membership);
    }

    public function test_admin_table_selection_controls_use_indonesian_labels(): void
    {
        $this->assertSame(
            'Pilih semua 34',
            __('filament-tables::table.selection_indicator.actions.select_all.label', ['count' => 34]),
        );
        $this->assertSame(
            'Batalkan semua pilihan',
            __('filament-tables::table.selection_indicator.actions.deselect_all.label'),
        );
    }

    public function test_admin_can_delete_multiple_selected_records(): void
    {
        $this->signInAdmin();
        $firstMembership = $this->createMembership(User::factory()->create());
        $secondMembership = $this->createMembership(User::factory()->create());
        $component = Livewire::test(ListMemberships::class);
        $action = collect($component->instance()->getTable()->getToolbarActions())
            ->first(fn ($action): bool => $action->getName() === 'delete');

        $this->assertInstanceOf(DeleteBulkAction::class, $action);

        $this->assertFalse($action->isConfirmationRequired());
        $this->assertFalse($action->hasModal());
        $this->assertNull($action->getKeyBindings());
        $this->assertStringContainsString('confirmAdminDelete', $action->getAlpineClickHandler());
        $this->assertStringContainsString("mountAction('delete'", $action->getAlpineClickHandler());

        $component
            ->callTableBulkAction('delete', [$firstMembership, $secondMembership])
            ->assertDispatched('admin-record-deleted')
            ->assertNotNotified();

        $this->assertModelMissing($firstMembership);
        $this->assertModelMissing($secondMembership);
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

    public function test_transaction_detail_action_is_view_only(): void
    {
        $admin = $this->signInAdmin();
        $membership = $this->createMembership($admin);
        $transaction = $membership->transactions()->create([
            'user_id' => $admin->id,
            'amount' => 250000,
            'quantity' => 1,
            'status' => 'rejected',
            'admin_note' => 'Pembayaran tidak sesuai.',
        ]);

        $component = Livewire::test(ListTransactions::class);
        $action = $component->instance()->getTable()->getAction('view');

        $this->assertInstanceOf(ViewAction::class, $action);
        $this->assertSame('Detail', $action->getLabel());
        $this->assertSame('Detail Transaksi', $action->getModalHeading());
        $this->assertSame('Periksa detail transaksi di bawah ini.', $action->getModalDescription());
        $this->assertNull($action->getModalSubmitAction());
        $this->assertSame('Tutup', $action->getModalCancelAction()?->getLabel());
        $this->assertSame(Alignment::End, $action->getModalFooterActionsAlignment());
        $this->assertSame(Width::FourExtraLarge, $action->getModalWidth());
        $this->assertTrue($action->isModalHeaderSticky());
        $this->assertTrue($action->isModalFooterSticky());

        $this->assertSame('Membership', $transaction->purchase_type);
        $this->assertSame($membership->plan->name, $transaction->purchase_name);
        $this->assertSame(
            ['class' => 'transaction-view-modal'],
            $action->getExtraModalWindowAttributes(),
        );
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
    #[TestWith(['manage-homepage'])]
    #[TestWith(['manage-gym-profile'])]
    public function test_admin_resource_pages_render_with_the_shared_theme(string $path): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->get('/admin/'.$path)->assertOk()->assertSee('CukipGym');
    }

    public function test_about_page_focuses_on_about_gallery_footer_and_sponsor_sections(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get('/admin/manage-gym-profile')
            ->assertOk()
            ->assertSee('gym-profile-page', false)
            ->assertSee('gym-profile-form', false)
            ->assertSeeInOrder(['Tentang CukipGym', 'Edit', 'Tentang CukipGym'])
            ->assertSee('Halaman Awal')
            ->assertDontSee('Profil dan lokasi gym')
            ->assertDontSee('Nama Gym')
            ->assertDontSee('Jam Operasional')
            ->assertDontSee('Tempel Lokasi dari Google Maps')
            ->assertSee('Konten Tentang')
            ->assertSee('Galeri Tentang')
            ->assertSee('Footer')
            ->assertSee('Sponsor &amp; partner', false)
            ->assertSee('gym-sponsor-field', false)
            ->assertSee('gym-sponsor-repeater', false)
            ->assertSee('Tutup semua')
            ->assertSee('Buka semua');
    }

    public function test_gym_class_toolbar_keeps_generate_and_create_actions_available(): void
    {
        $this->signInAdmin();

        Livewire::test(ListGymClasses::class)
            ->assertTableActionExists('generateSessions')
            ->assertTableActionExists('create');

        Livewire::test(ListClassSessions::class)
            ->assertTableActionExists('create');

        Livewire::test(ListClassBookings::class)
            ->assertTableActionExists('create');

        Livewire::test(ListFacilities::class)
            ->assertTableActionExists('create')
            ->assertTableFilterExists('category');

        Livewire::test(ListMembershipPlans::class)
            ->assertTableActionExists('create')
            ->assertTableFilterExists('is_active');

        Livewire::test(ListReviews::class)
            ->assertTableActionExists('create')
            ->assertTableFilterExists('status')
            ->assertTableFilterExists('name')
            ->assertTableFilterExists('rating');
    }

    public function test_admin_can_open_and_monitor_personal_trainer_bookings(): void
    {
        $this->signInAdmin();
        $booking = TrainerBooking::factory()->create();

        Livewire::test(ListTrainerBookings::class)
            ->assertCanSeeTableRecords([$booking])
            ->assertTableFilterExists('status')
            ->assertTableFilterExists('payment_status')
            ->assertTableActionExists('view', record: $booking);

        $this->get('/admin/trainer-bookings')
            ->assertOk()
            ->assertSee('Booking Personal Trainer');
    }

    public function test_admin_can_create_a_paid_personal_trainer_booking_for_multiple_slots(): void
    {
        $this->travelTo(Carbon::parse('2026-11-21 10:00:00'));
        $this->signInAdmin();
        $member = User::factory()->create();
        $trainer = PersonalTrainer::factory()->create(['session_price' => 150000]);
        $slots = collect([
            TrainerSlot::factory()->create([
                'personal_trainer_id' => $trainer->id,
                'starts_at' => '2026-11-25 09:00:00',
                'ends_at' => '2026-11-25 11:00:00',
            ]),
            TrainerSlot::factory()->create([
                'personal_trainer_id' => $trainer->id,
                'starts_at' => '2026-11-26 09:00:00',
                'ends_at' => '2026-11-26 11:00:00',
            ]),
        ]);

        Livewire::test(ListTrainerBookings::class)
            ->callAction('create', [
                'user_id' => $member->id,
                'personal_trainer_id' => $trainer->id,
                'slot_ids' => $slots->pluck('id')->all(),
                'payment_status' => 'paid',
                'payment_method' => 'manual',
                'payment_channel' => 'cash',
            ])
            ->assertHasNoFormErrors();

        $this->assertDatabaseCount('trainer_bookings', 2);
        $this->assertDatabaseHas('payments', [
            'user_id' => $member->id,
            'gross_amount' => 300000,
            'status' => 'paid',
        ]);
        $this->assertDatabaseHas('transactions', [
            'user_id' => $member->id,
            'amount' => 150000,
            'payment_method' => 'manual',
            'payment_channel' => 'cash',
            'status' => 'paid',
        ]);
        $this->assertSame(2, Transaction::query()->where('user_id', $member->id)->count());
        $this->assertSame(2, TrainerSlot::query()->whereKey($slots)->where('status', 'booked')->count());
        $this->assertSame(
            300000,
            Livewire::test(AdminDashboard::class)->instance()->dashboardData()['stats']['monthlyIncome'],
        );
    }

    public function test_pending_manual_personal_trainer_booking_is_not_counted_as_income(): void
    {
        $this->travelTo(Carbon::parse('2026-11-21 10:00:00'));
        $this->signInAdmin();
        $member = User::factory()->create();
        $trainer = PersonalTrainer::factory()->create(['session_price' => 175000]);
        $slot = TrainerSlot::factory()->create([
            'personal_trainer_id' => $trainer->id,
            'starts_at' => '2026-11-25 09:00:00',
            'ends_at' => '2026-11-25 11:00:00',
        ]);

        Livewire::test(ListTrainerBookings::class)
            ->callAction('create', [
                'user_id' => $member->id,
                'personal_trainer_id' => $trainer->id,
                'slot_ids' => [$slot->id],
                'payment_status' => 'pending',
                'payment_method' => 'midtrans',
            ])
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('trainer_bookings', [
            'user_id' => $member->id,
            'trainer_slot_id' => $slot->id,
            'status' => 'pending_payment',
        ]);
        $this->assertDatabaseHas('transactions', [
            'user_id' => $member->id,
            'amount' => 175000,
            'payment_method' => 'midtrans',
            'payment_channel' => null,
            'status' => 'pending',
        ]);
        $this->assertSame(
            0,
            Livewire::test(AdminDashboard::class)->instance()->dashboardData()['stats']['monthlyIncome'],
        );
    }

    public function test_admin_can_create_a_class_booking_from_the_modal(): void
    {
        $this->signInAdmin();
        $member = User::factory()->create();
        $gymClass = GymClass::factory()->create([
            'name' => 'Yoga Pagi',
            'start_time' => '07:00',
            'end_time' => '08:00',
        ]);
        $session = $gymClass->sessions()->create([
            'date' => '2026-11-25',
            'capacity' => 15,
        ]);

        Livewire::test(ListClassBookings::class)
            ->callAction('create', [
                'user_id' => $member->id,
                'class_session_id' => $session->id,
                'status' => 'booked',
            ])
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('class_bookings', [
            'user_id' => $member->id,
            'class_session_id' => $session->id,
            'status' => 'booked',
        ]);
    }

    public function test_generate_sessions_dispatches_sweetalert_success(): void
    {
        $this->signInAdmin();
        GymClass::factory()->create(['is_active' => true]);

        Livewire::test(ListGymClasses::class)
            ->callTableAction('generateSessions', data: ['weeks' => 1])
            ->assertDispatched('filament-sweetalert')
            ->assertNotNotified();
    }

    public function test_class_session_occupancy_uses_dash_when_there_are_no_bookings(): void
    {
        $admin = $this->signInAdmin();
        $gymClass = GymClass::factory()->create();
        $emptySession = $gymClass->sessions()->create(['date' => '2026-11-16', 'capacity' => 10]);
        $bookedSession = $gymClass->sessions()->create(['date' => '2026-11-17', 'capacity' => 10]);
        $admin->classBookings()->create(['class_session_id' => $bookedSession->id, 'status' => 'booked']);

        Livewire::test(ListClassSessions::class)
            ->assertTableColumnFormattedStateSet('active_bookings_count', '-', $emptySession)
            ->assertTableColumnFormattedStateSet('active_bookings_count', 1, $bookedSession);
    }

    public function test_reviews_can_be_filtered_by_status_name_and_rating(): void
    {
        $this->signInAdmin();
        $approvedReview = User::factory()->create()->reviews()->create([
            'name' => 'Maya Contoh',
            'rating' => 5,
            'review' => 'Pelatihnya ramah.',
            'status' => 'approved',
        ]);
        $rejectedReview = User::factory()->create()->reviews()->create([
            'name' => 'Rizky Contoh',
            'rating' => 3,
            'review' => 'Tempatnya nyaman.',
            'status' => 'rejected',
        ]);

        Livewire::test(ListReviews::class)
            ->filterTable('status', 'approved')
            ->assertCanSeeTableRecords([$approvedReview])
            ->assertCanNotSeeTableRecords([$rejectedReview]);

        Livewire::test(ListReviews::class)
            ->filterTable('name', 'Rizky Contoh')
            ->assertCanSeeTableRecords([$rejectedReview])
            ->assertCanNotSeeTableRecords([$approvedReview]);

        Livewire::test(ListReviews::class)
            ->filterTable('rating', '5')
            ->assertCanSeeTableRecords([$approvedReview])
            ->assertCanNotSeeTableRecords([$rejectedReview]);
    }

    public function test_admin_can_create_a_review_from_the_review_modal(): void
    {
        $this->signInAdmin();
        $member = User::factory()->create(['role' => 'user']);

        Livewire::test(ListReviews::class)
            ->callAction('create', [
                'user_id' => $member->id,
                'name' => 'Member Contoh',
                'rating' => 5,
                'review' => 'Pelayanan gym sangat baik dan pelatihnya ramah.',
                'status' => 'approved',
            ])
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('reviews', [
            'user_id' => $member->id,
            'name' => 'Member Contoh',
            'rating' => 5,
            'status' => 'approved',
        ]);
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

    public function test_payment_proof_is_not_shown_in_the_table_and_manual_transaction_creation_is_not_offered(): void
    {
        $admin = $this->signInAdmin();
        $membership = $this->createMembership($admin);
        $transaction = $membership->transactions()->create([
            'user_id' => $admin->id,
            'amount' => 250000,
            'status' => 'pending',
        ]);

        Livewire::test(ListTransactions::class)
            ->assertCanSeeTableRecords([$transaction])
            ->assertTableColumnDoesNotExist('payment_proof')
            ->assertSee('Cari invoice, member, atau pembelian...')
            ->assertSee('Hapus')
            ->assertActionDoesNotExist('create');
    }

    public function test_transaction_status_actions_dispatch_sweetalert_feedback(): void
    {
        $admin = $this->signInAdmin();
        $membership = $this->createMembership($admin);
        $paidTransaction = $membership->transactions()->create([
            'user_id' => $admin->id,
            'amount' => 250000,
            'status' => 'pending',
        ]);

        Livewire::test(ListTransactions::class)
            ->callAction(TestAction::make('markPaid')->table($paidTransaction))
            ->assertDispatched('filament-sweetalert')
            ->assertNotNotified();

        $rejectedMembership = $this->createMembership($admin);
        $rejectedTransaction = $rejectedMembership->transactions()->create([
            'user_id' => $admin->id,
            'amount' => 250000,
            'status' => 'pending',
        ]);

        Livewire::test(ListTransactions::class)
            ->callAction(
                TestAction::make('reject')->table($rejectedTransaction),
                data: ['admin_note' => 'Bukti pembayaran tidak valid.'],
            )
            ->assertDispatched('filament-sweetalert')
            ->assertNotNotified();
    }

    public function test_marking_a_personal_trainer_transaction_paid_confirms_its_booking(): void
    {
        $this->signInAdmin();
        $member = User::factory()->create();
        $trainer = PersonalTrainer::factory()->create(['session_price' => 150000]);
        $slot = TrainerSlot::factory()->for($trainer, 'trainer')->create([
            'status' => 'held',
            'held_until' => now()->addMinutes(30),
        ]);
        $payment = Payment::factory()->for($member)->create([
            'gross_amount' => 150000,
            'status' => 'pending',
        ]);
        $booking = $payment->trainerBookings()->create([
            'user_id' => $member->id,
            'trainer_slot_id' => $slot->id,
            'amount' => 150000,
            'status' => 'pending_payment',
        ]);
        $transaction = $booking->transaction()->create([
            'user_id' => $member->id,
            'payment_id' => $payment->id,
            'quantity' => 1,
            'checkout_batch' => $payment->checkout_batch,
            'amount' => 150000,
            'status' => 'pending',
        ]);

        Livewire::test(ListTransactions::class)
            ->callAction(TestAction::make('markPaid')->table($transaction))
            ->assertDispatched('filament-sweetalert');

        $this->assertDatabaseHas('transactions', ['id' => $transaction->id, 'status' => 'paid']);
        $this->assertDatabaseHas('payments', ['id' => $payment->id, 'status' => 'paid']);
        $this->assertDatabaseHas('trainer_bookings', ['id' => $booking->id, 'status' => 'confirmed']);
        $this->assertDatabaseHas('trainer_slots', [
            'id' => $slot->id,
            'status' => 'booked',
            'held_until' => null,
        ]);
    }

    public function test_transactions_can_be_filtered_by_membership_or_personal_trainer(): void
    {
        $admin = $this->signInAdmin();
        $membership = $this->createMembership($admin);
        $membershipTransaction = $membership->transactions()->create([
            'user_id' => $admin->id,
            'amount' => 250000,
            'status' => 'paid',
        ]);
        $member = User::factory()->create();
        $trainer = PersonalTrainer::factory()->create(['session_price' => 150000]);
        $slot = TrainerSlot::factory()->for($trainer, 'trainer')->create();
        $payment = Payment::factory()->for($member)->create(['gross_amount' => 150000]);
        $booking = $payment->trainerBookings()->create([
            'user_id' => $member->id,
            'trainer_slot_id' => $slot->id,
            'amount' => 150000,
            'status' => 'pending_payment',
        ]);
        $trainerTransaction = $booking->transaction()->create([
            'user_id' => $member->id,
            'payment_id' => $payment->id,
            'checkout_batch' => $payment->checkout_batch,
            'amount' => 150000,
            'status' => 'pending',
        ]);

        Livewire::test(ListTransactions::class)
            ->assertTableFilterExists('purchase_type')
            ->filterTable('purchase_type', 'membership')
            ->assertCanSeeTableRecords([$membershipTransaction])
            ->assertCanNotSeeTableRecords([$trainerTransaction])
            ->filterTable('purchase_type', 'trainer')
            ->assertCanSeeTableRecords([$trainerTransaction])
            ->assertCanNotSeeTableRecords([$membershipTransaction]);
    }

    public function test_transactions_can_be_filtered_by_week_month_year_or_custom_dates(): void
    {
        $this->travelTo(Carbon::parse('2026-11-21 10:00:00'));
        $admin = $this->signInAdmin();
        $membership = $this->createMembership($admin);
        $thisWeek = $membership->transactions()->create([
            'user_id' => $admin->id,
            'amount' => 150000,
            'status' => 'paid',
        ]);
        $thisWeek->forceFill(['created_at' => '2026-11-18 10:00:00'])->saveQuietly();
        $thisMonth = $membership->transactions()->create([
            'user_id' => $admin->id,
            'amount' => 250000,
            'status' => 'paid',
        ]);
        $thisMonth->forceFill(['created_at' => '2026-11-03 10:00:00'])->saveQuietly();
        $thisYear = $membership->transactions()->create([
            'user_id' => $admin->id,
            'amount' => 350000,
            'status' => 'paid',
        ]);
        $thisYear->forceFill(['created_at' => '2026-04-10 10:00:00'])->saveQuietly();
        $previousYear = $membership->transactions()->create([
            'user_id' => $admin->id,
            'amount' => 450000,
            'status' => 'paid',
        ]);
        $previousYear->forceFill(['created_at' => '2025-12-20 10:00:00'])->saveQuietly();

        Livewire::test(ListTransactions::class)
            ->assertTableFilterExists('period')
            ->filterTable('period', ['range' => 'week'])
            ->assertCanSeeTableRecords([$thisWeek])
            ->assertCanNotSeeTableRecords([$thisMonth, $thisYear, $previousYear])
            ->filterTable('period', ['range' => 'month'])
            ->assertCanSeeTableRecords([$thisWeek, $thisMonth])
            ->assertCanNotSeeTableRecords([$thisYear, $previousYear])
            ->filterTable('period', ['range' => 'year'])
            ->assertCanSeeTableRecords([$thisWeek, $thisMonth, $thisYear])
            ->assertCanNotSeeTableRecords([$previousYear])
            ->filterTable('period', [
                'range' => 'custom',
                'start_date' => '2026-11-01',
                'end_date' => '2026-11-10',
            ])
            ->assertCanSeeTableRecords([$thisMonth])
            ->assertCanNotSeeTableRecords([$thisWeek, $thisYear, $previousYear]);
    }

    public function test_manual_transaction_creation_route_is_not_exposed(): void
    {
        $this->signInAdmin();

        $this->get('/admin/transactions/create')->assertNotFound();
    }

    /**
     * @param  class-string<ListRecords>  $pageClass
     */
    #[DataProvider('selectableAdminTables')]
    public function test_key_admin_tables_support_multiple_selection(string $pageClass): void
    {
        $this->signInAdmin();

        $component = Livewire::test($pageClass);

        $this->assertTrue($component->instance()->getTable()->isSelectionEnabled());
        $this->assertFalse($component->instance()->getTable()->shouldDeselectAllRecordsWhenFiltered());
        $component
            ->assertTableBulkActionExists('delete')
            ->assertTableBulkActionHasLabel('delete', 'Hapus semua');
    }

    public function test_trainer_schedule_can_be_filtered_by_trainer_and_status(): void
    {
        $this->signInAdmin();
        $availableSlot = TrainerSlot::factory()->create(['status' => 'available']);
        $bookedSlot = TrainerSlot::factory()->create(['status' => 'booked']);

        Livewire::test(ListTrainerSlots::class)
            ->assertTableFilterExists('personal_trainer_id')
            ->assertTableFilterExists('status')
            ->assertTableColumnFormattedStateSet('status', 'Tersedia', $availableSlot)
            ->assertTableColumnFormattedStateSet('status', 'Sudah dibooking', $bookedSlot);
    }

    public function test_trainer_schedule_uses_complete_labels_and_supports_record_deletion(): void
    {
        $this->signInAdmin();
        $slot = TrainerSlot::factory()->create();
        $component = Livewire::test(ListTrainerSlots::class);

        $this->assertSame('Jadwal Personal Trainer', TrainerSlotResource::getNavigationLabel());
        $this->assertSame('Jadwal Personal Trainer', TrainerSlotResource::getModelLabel());
        $this->assertSame('Jadwal Personal Trainer', TrainerSlotResource::getPluralModelLabel());

        $component
            ->assertTableActionExists('delete', record: $slot)
            ->assertTableActionHasLabel('delete', 'Hapus', $slot)
            ->callTableAction('delete', $slot)
            ->assertDispatched('admin-record-deleted');

        $this->assertModelMissing($slot);
    }

    public function test_gym_class_form_does_not_show_inactive_generation_helper(): void
    {
        $this->signInAdmin();
        $gymClass = GymClass::factory()->create();
        $formSource = file_get_contents(app_path('Filament/Resources/GymClasses/Schemas/GymClassForm.php'));

        $this->assertStringNotContainsString(
            'Kelas nonaktif tidak ikut dibuatkan sesi baru saat generate.',
            $formSource,
        );

        Livewire::test(ListGymClasses::class)
            ->mountAction(TestAction::make('edit')->table($gymClass))
            ->assertActionMounted(TestAction::make('edit')->table($gymClass))
            ->assertDontSee('Kelas nonaktif tidak ikut dibuatkan sesi baru saat generate.');
    }

    public function test_searchable_dropdowns_use_a_consistent_internal_scroll_area(): void
    {
        $theme = file_get_contents(resource_path('css/filament/admin/theme.css'));

        $this->assertStringContainsString(
            '.fi-dropdown-panel.fi-scrollable:has(.fi-select-input-search-ctn)',
            $theme,
        );
        $this->assertStringContainsString(
            'overflow-y: scroll !important',
            $theme,
        );
        $this->assertStringContainsString(
            '.fi-modal .fi-dropdown-panel.fi-scrollable:has(.fi-select-input-search-ctn)',
            $theme,
        );
        $this->assertMatchesRegularExpression(
            '/\.personal-trainer-form-section > \.fi-section\s*\{[^}]*overflow: visible;/s',
            $theme,
        );
    }

    public function test_admin_selects_use_the_same_searchable_non_native_dropdown(): void
    {
        $select = Select::make('example');
        $filter = SelectFilter::make('example');

        $this->assertFalse($select->isNative());
        $this->assertTrue($select->isSearchable());
        $this->assertSame('bottom', $select->getPosition());
        $this->assertStringContainsString(
            'fi-fixed-positioning-context',
            $select->getExtraFieldWrapperAttributes()['class'],
        );
        $this->assertFalse($filter->isNative());
        $this->assertTrue($filter->getSearchable());
        $this->assertTrue($filter->isPreloaded());
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
            'jadwal trainer' => [ListTrainerSlots::class, 'Tambah Jadwal Personal Trainer'],
            'sesi kelas' => [ListClassSessions::class, 'Tambah Sesi Kelas'],
            'booking kelas' => [ListClassBookings::class, 'Tambah Booking Kelas'],
            'jenis kelas' => [ListGymClasses::class, 'Tambah Jenis Kelas'],
            'fasilitas' => [ListFacilities::class, 'Tambah Fasilitas'],
            'paket membership' => [ListMembershipPlans::class, 'Tambah Paket Membership'],
            'review' => [ListReviews::class, 'Tambah Review Member'],
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
            'personal trainer' => [ListPersonalTrainers::class, 'Ubah Personal Trainer', 'Ubah'],
            'jadwal trainer' => [ListTrainerSlots::class, 'Ubah Jadwal Personal Trainer', 'Ubah'],
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
            'lokasi gym' => [ListGymLocations::class],
            'paket membership' => [ListMembershipPlans::class],
            'review' => [ListReviews::class],
            'personal trainer' => [ListPersonalTrainers::class],
            'jadwal trainer' => [ListTrainerSlots::class],
        ];
    }

    /**
     * @return array<string, array{class-string<ListRecords>}>
     */
    public static function selectableAdminTables(): array
    {
        return [
            'transaksi' => [ListTransactions::class],
            'membership' => [ListMemberships::class],
            'pengguna' => [ListUsers::class],
            'personal trainer' => [ListPersonalTrainers::class],
            'jadwal trainer' => [ListTrainerSlots::class],
            'jadwal sesi' => [ListClassSessions::class],
            'booking kelas' => [ListClassBookings::class],
            'fasilitas' => [ListFacilities::class],
            'paket membership' => [ListMembershipPlans::class],
            'review member' => [ListReviews::class],
        ];
    }
}
