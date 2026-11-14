<?php

namespace Tests\Feature;

use App\Filament\Member\Pages\AccountSettings;
use App\Models\MembershipPlan;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class MemberPanelTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login_from_the_member_panel(): void
    {
        $this->get('/member')->assertRedirect('/member/login');
        $this->get('/member/login')->assertRedirect(route('login'));
    }

    public function test_admins_cannot_access_the_member_panel(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->get('/member')->assertForbidden();
    }

    public function test_legacy_profile_route_redirects_members_to_the_member_panel(): void
    {
        $member = User::factory()->create(['role' => 'user']);

        $this->actingAs($member)
            ->get('/profile')
            ->assertRedirect('/member');
    }

    public function test_active_members_see_their_own_membership_and_active_access_code(): void
    {
        $this->travelTo(Carbon::parse('2026-11-14 10:00:00'));
        $member = User::factory()->create(['name' => 'Rani Member']);
        $otherMember = User::factory()->create();
        $plan = MembershipPlan::factory()->create(['name' => 'Regular Bulanan']);
        $otherPlan = MembershipPlan::factory()->create(['name' => 'Paket Rahasia']);
        $member->memberships()->create([
            'membership_plan_id' => $plan->id,
            'status' => 'active',
            'start_date' => '2026-11-01',
            'end_date' => '2026-11-30',
        ]);
        $otherMember->memberships()->create([
            'membership_plan_id' => $otherPlan->id,
            'status' => 'active',
            'start_date' => '2026-11-01',
            'end_date' => '2026-11-30',
        ]);

        $response = $this->actingAs($member)->get('/member');

        $response->assertOk()
            ->assertSee('Halo, Rani Member')
            ->assertSee('Regular Bulanan')
            ->assertSee('QR akses aktif')
            ->assertSee('30 Nov 2026')
            ->assertDontSee('Paket Rahasia');
    }

    public function test_expired_members_see_limited_dashboard_and_renewal_action(): void
    {
        $this->travelTo(Carbon::parse('2026-11-14 10:00:00'));
        $member = User::factory()->create();
        $plan = MembershipPlan::factory()->create(['name' => 'Paket Lama']);
        $member->memberships()->create([
            'membership_plan_id' => $plan->id,
            'status' => 'expired',
            'start_date' => '2026-10-01',
            'end_date' => '2026-10-31',
        ]);

        $response = $this->actingAs($member)->get('/member');

        $response->assertOk()
            ->assertSee('Membership kedaluwarsa')
            ->assertSee('QR akses dikunci')
            ->assertSee('Perpanjang membership')
            ->assertSee(route('membership.index'), false)
            ->assertDontSee('QR akses aktif');

        $this->get('/member/my-classes')->assertForbidden();
        $this->get('/member/transactions')->assertOk();
        $this->get('/member/account-settings')->assertOk();
    }

    public function test_members_can_update_name_whatsapp_and_password(): void
    {
        $member = $this->signInMember();
        $originalEmail = $member->email;

        Livewire::test(AccountSettings::class)
            ->fillForm([
                'name' => 'Nama Member Baru',
                'phone' => '6281234567890',
                'password' => 'Password-Baru-123',
                'passwordConfirmation' => 'Password-Baru-123',
                'currentPassword' => 'password',
            ])
            ->set('data.email', 'email-tidak-boleh-berubah@example.com')
            ->call('save')
            ->assertHasNoFormErrors();

        $member->refresh();

        $this->assertSame('Nama Member Baru', $member->name);
        $this->assertSame('6281234567890', $member->phone);
        $this->assertSame($originalEmail, $member->email);
        $this->assertTrue(Hash::check('Password-Baru-123', $member->password));
    }

    public function test_member_dashboard_escapes_member_and_membership_names(): void
    {
        $this->travelTo(Carbon::parse('2026-11-14 10:00:00'));
        $memberName = '<script>alert("member")</script>';
        $planName = '<img src=x onerror=alert(1)>';
        $member = User::factory()->create(['name' => $memberName]);
        $plan = MembershipPlan::factory()->create(['name' => $planName]);
        $member->memberships()->create([
            'membership_plan_id' => $plan->id,
            'status' => 'active',
            'start_date' => '2026-11-01',
            'end_date' => '2026-11-30',
        ]);

        $response = $this->actingAs($member)->get('/member');

        $response->assertOk()
            ->assertSee($memberName)
            ->assertSee($planName)
            ->assertDontSee($memberName, false)
            ->assertDontSee($planName, false);
    }

    public function test_member_settings_reject_an_incorrect_current_password(): void
    {
        $member = $this->signInMember();

        Livewire::test(AccountSettings::class)
            ->fillForm([
                'name' => 'Nama Tidak Boleh Tersimpan',
                'phone' => '6281234567890',
                'password' => 'Password-Baru-123',
                'passwordConfirmation' => 'Password-Baru-123',
                'currentPassword' => 'password-yang-salah',
            ])
            ->call('save')
            ->assertHasFormErrors(['currentPassword']);

        $this->assertNotSame('Nama Tidak Boleh Tersimpan', $member->fresh()->name);
        $this->assertTrue(Hash::check('password', $member->fresh()->password));
    }

    private function signInMember(): User
    {
        $member = User::factory()->create(['role' => 'user']);
        $this->actingAs($member);
        Filament::setCurrentPanel(Filament::getPanel('member'));
        Filament::bootCurrentPanel();

        return $member;
    }
}
