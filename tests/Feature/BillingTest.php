<?php

namespace Tests\Feature;

use App\Models\Bill;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BillingTest extends TestCase
{
    use RefreshDatabase;

    private function occupiedRoom(string $number, string $phone): array
    {
        $room = Room::create(['room_number' => $number, 'default_rent' => 400000, 'status' => 'occupied']);
        $user = User::factory()->create(['phone' => $phone]);
        $tenant = Tenant::create([
            'user_id' => $user->id,
            'room_id' => $room->id,
            'move_in_date' => '2026-01-01',
        ]);

        return [$room, $user, $tenant];
    }

    private function makeBill(Room $room, Tenant $tenant, int $total, string $month = '2026-11', string $status = 'unpaid'): Bill
    {
        return Bill::create([
            'room_id' => $room->id,
            'tenant_id' => $tenant->id,
            'month' => $month,
            'rent' => $total,
            'electricity' => 0,
            'water' => 0,
            'total' => $total,
            'due_date' => '2026-11-10',
            'status' => $status,
            'paid_at' => $status === 'paid' ? now() : null,
        ]);
    }

    private function payload(Room $room, array $values = []): array
    {
        return [
            'month' => '2026-11',
            'due_date' => '2026-11-10',
            'bills' => [
                $room->id => array_merge(['rent' => 400000, 'electricity' => 50000, 'water' => 20000], $values),
            ],
        ];
    }

    public function test_server_calculates_the_total(): void
    {
        $admin = User::factory()->admin()->create();
        [$room] = $this->occupiedRoom('101', '011111111');

        $data = $this->payload($room);
        $data['bills'][$room->id]['total'] = 1; // a fake total must be ignored

        $this->actingAs($admin)->post('/admin/billing', $data)->assertRedirect();

        $bill = Bill::where('room_id', $room->id)->where('month', '2026-11')->firstOrFail();
        $this->assertEquals(470000, $bill->total);
        $this->assertEquals('unpaid', $bill->status);
    }

    public function test_saving_twice_updates_instead_of_duplicating(): void
    {
        $admin = User::factory()->admin()->create();
        [$room] = $this->occupiedRoom('101', '011111111');

        $this->actingAs($admin)->post('/admin/billing', $this->payload($room));
        $this->actingAs($admin)->post('/admin/billing', $this->payload($room, ['electricity' => 60000]));

        $this->assertEquals(1, Bill::count());
        $this->assertEquals(60000, Bill::first()->electricity);
    }

    public function test_paid_bill_is_not_changed_by_saving(): void
    {
        $admin = User::factory()->admin()->create();
        [$room, , $tenant] = $this->occupiedRoom('101', '011111111');
        $bill = $this->makeBill($room, $tenant, 470000, '2026-11', 'paid');

        $this->actingAs($admin)->post('/admin/billing', $this->payload($room, ['electricity' => 99000]));

        $this->assertEquals(470000, $bill->fresh()->total);
        $this->assertEquals(0, $bill->fresh()->electricity);
    }

    public function test_invalid_numbers_are_rejected(): void
    {
        $admin = User::factory()->admin()->create();
        [$room] = $this->occupiedRoom('101', '011111111');

        $this->actingAs($admin)
            ->post('/admin/billing', $this->payload($room, ['rent' => -5]))
            ->assertSessionHasErrors("bills.{$room->id}.rent");

        $this->assertEquals(0, Bill::count());
    }

    public function test_vacant_room_cannot_be_billed(): void
    {
        $admin = User::factory()->admin()->create();
        $room = Room::create(['room_number' => '102', 'default_rent' => 400000, 'status' => 'vacant']);

        $this->actingAs($admin)->post('/admin/billing', $this->payload($room));

        $this->assertEquals(0, Bill::count());
    }

    public function test_tenant_cannot_save_bills(): void
    {
        [$room, $user] = $this->occupiedRoom('101', '011111111');

        $this->actingAs($user)->post('/admin/billing', $this->payload($room))->assertForbidden();

        $this->assertEquals(0, Bill::count());
    }

    public function test_admin_can_mark_a_bill_paid_and_unpaid(): void
    {
        $admin = User::factory()->admin()->create();
        [$room, , $tenant] = $this->occupiedRoom('101', '011111111');
        $bill = $this->makeBill($room, $tenant, 470000);

        $this->actingAs($admin)->patch("/admin/bills/{$bill->id}/paid")->assertRedirect();
        $this->assertEquals('paid', $bill->fresh()->status);
        $this->assertNotNull($bill->fresh()->paid_at);

        $this->actingAs($admin)->patch("/admin/bills/{$bill->id}/unpaid")->assertRedirect();
        $this->assertEquals('unpaid', $bill->fresh()->status);
        $this->assertNull($bill->fresh()->paid_at);
    }

    public function test_tenant_cannot_mark_a_bill_paid(): void
    {
        [$room, $user, $tenant] = $this->occupiedRoom('101', '011111111');
        $bill = $this->makeBill($room, $tenant, 470000);

        $this->actingAs($user)->patch("/admin/bills/{$bill->id}/paid")->assertForbidden();

        $this->assertEquals('unpaid', $bill->fresh()->status);
    }

    public function test_tenant_dashboard_shows_only_own_bills(): void
    {
        [$roomA, $userA, $tenantA] = $this->occupiedRoom('101', '011111111');
        [$roomB, , $tenantB] = $this->occupiedRoom('102', '022222222');

        $month = now()->format('Y-m');
        $this->makeBill($roomA, $tenantA, 470000, $month);
        $this->makeBill($roomB, $tenantB, 999000, $month);

        $this->actingAs($userA)->get('/tenant')
            ->assertOk()
            ->assertSee('470,000')
            ->assertDontSee('999,000');
    }
}