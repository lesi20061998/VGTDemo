<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. Orders
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->enum('status', ['pending', 'processing', 'shipped', 'delivered', 'cancelled', 'refunded'])->default('pending');
            $table->decimal('subtotal', 15, 2);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('shipping_amount', 15, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2);
            $table->string('currency', 3)->default('VND');
            
            // Customer info
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone')->nullable();
            
            // Billing address
            $table->json('billing_address');
            
            // Shipping address
            $table->json('shipping_address');
            
            // Payment info
            $table->string('payment_method')->nullable();
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');
            $table->timestamp('paid_at')->nullable();
            
            // Notes
            $table->text('customer_notes')->nullable();
            $table->text('internal_notes')->nullable();
            
            $table->timestamps();
            
            $table->index(['status', 'created_at']);
            $table->index(['order_number']);
            $table->index(['customer_email']);
            $table->index(['payment_status']);
        });

        // 2. Order Items
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products_enhanced')->onDelete('cascade');
            $table->foreignId('product_variation_id')->nullable()->constrained()->onDelete('set null');
            
            // Product snapshot at time of order
            $table->string('product_name');
            $table->string('product_sku');
            $table->json('product_attributes')->nullable();
            $table->decimal('unit_price', 15, 2);
            $table->integer('quantity');
            $table->decimal('total_price', 15, 2);
            
            $table->timestamps();
            
            $table->index(['order_id']);
            $table->index(['product_id']);
        });

        // 3. Order Status History
        Schema::create('order_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->string('from_status')->nullable();
            $table->string('to_status');
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();
            
            $table->index(['order_id', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('order_status_histories');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};