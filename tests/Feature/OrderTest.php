<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_view_orders_list()
    {
        Order::factory(5)->create();

        $response = $this->actingAs($this->user)->get(route('admin.orders.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.orders.index');
    }

    public function test_can_view_order_details()
    {
        $order = Order::factory()->create();

        $response = $this->actingAs($this->user)->get(route('admin.orders.show', $order));

        $response->assertStatus(200);
        $response->assertViewIs('admin.orders.show');
        $response->assertViewHas('order');
    }

    public function test_can_update_order_status()
    {
        $order = Order::factory()->pending()->create();

        $response = $this->actingAs($this->user)->post(route('admin.orders.updateStatus', $order), [
            'status' => 'processing',
            'notes' => 'Processing order',
        ]);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'processing',
        ]);

        $this->assertDatabaseHas('order_status_histories', [
            'order_id' => $order->id,
            'to_status' => 'processing',
        ]);
    }

    public function test_can_update_payment_status()
    {
        $order = Order::factory()->pending()->create();

        $response = $this->actingAs($this->user)->post(route('admin.orders.updatePaymentStatus', $order), [
            'payment_status' => 'paid',
        ]);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'payment_status' => 'paid',
        ]);

        $this->assertNotNull($order->fresh()->paid_at);
    }

    public function test_can_add_internal_note()
    {
        $order = Order::factory()->create();
        $originalNotes = $order->internal_notes;

        $response = $this->actingAs($this->user)->post(route('admin.orders.addNote', $order), [
            'internal_notes' => 'Test note',
        ]);

        $order->refresh();
        $this->assertStringContainsString('Test note', $order->internal_notes);
    }

    public function test_can_delete_order()
    {
        $order = Order::factory()->create();

        $response = $this->actingAs($this->user)->delete(route('admin.orders.destroy', $order));

        $this->assertDatabaseMissing('orders', ['id' => $order->id]);
    }

    public function test_can_view_order_reports()
    {
        Order::factory(10)->paid()->create();

        $response = $this->actingAs($this->user)->get(route('admin.orders.reports'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.orders.reports');
        $response->assertViewHas('total_sales');
        $response->assertViewHas('total_orders');
    }
}
