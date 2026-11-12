<?php

namespace Tests\Feature;

use App\Models\Bill;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleProtectionTest extends TestCase
{
    use RefreshDatabase;

    private function makeTenantWithBill(string $roomNumber, string $phone): array
    {
        $room = Room::create([
            'room_number' => $roomNumber,
            'default_rent' => 400000,
            'status' => 'occupied',
        ]);
        $user = User::factory()->create(['phone' => $phone]);
        $tenant = Tenant::create([
            'user_id' => $user->id,
            'room_id' => $room->id,
            'move_in_date' => '2026-01-01',
        ]);
        $bill = Bill::create([
            'room_id' => $room->id,
            'tenant_id' => $tenant->id,
            'month' => '2026-11',
            'rent' => 400000,
            'electricity' => 50000,
            'water' => 20000,
            'total' => 470000,
            'due_date' => '2026-11-10',
        ]);

        return [$user, $bill];
    }

    public function test_guest_is_sent_to_login(): void
    {
        $this->get('/admin/rooms')->assertRedirect('/login');
        $this->get('/tenant')->assertRedirect('/login');
    }

    public function test_tenant_cannot_open_admin_pages(): void
    {
        $tenant = User::factory()->create();

        $this->actingAs($tenant)->get('/admin')->assertForbidden();
        $this->actingAs($tenant)->get('/admin/rooms')->assertForbidden();
        $this->actingAs($tenant)->get('/admin/tenants')->assertForbidden();
        $this->actingAs($tenant)
            ->post('/admin/rooms', ['room_number' => '999', 'default_rent' => 1])
            ->assertForbidden();
    }

    public function test_admin_cannot_open_tenant_pages(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get('/tenant')->assertForbidden();
    }

    public function test_admin_can_open_admin_pages(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get('/admin')->assertOk();
        $this->actingAs($admin)->get('/admin/rooms')->assertOk();
        $this->actingAs($admin)->get('/admin/tenants')->assertOk();
        $this->actingAs($admin)->get('/admin/tenants/create')->assertOk();
    }

    public function test_tenant_can_open_own_bill(): void
    {
        [$user, $bill] = $this->makeTenantWithBill('101', '011111111');

        $this->actingAs($user)->get("/tenant/bills/{$bill->id}")->assertOk();
    }

    public function test_tenant_cannot_open_another_rooms_bill(): void
    {
        [$userA, $billA] = $this->makeTenantWithBill('101', '011111111');
        [$userB, $billB] = $this->makeTenantWithBill('102', '022222222');

        $this->actingAs($userA)->get("/tenant/bills/{$billB->id}")->assertForbidden();
        $this->actingAs($userB)->get("/tenant/bills/{$billA->id}")->assertForbidden();
    }
}
