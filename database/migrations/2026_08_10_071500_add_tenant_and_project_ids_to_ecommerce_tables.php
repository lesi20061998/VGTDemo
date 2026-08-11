<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('orders')) {
            Schema::create('orders', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('tenant_id')->nullable();
                $table->unsignedBigInteger('project_id')->nullable();
                $table->string('order_number')->unique();
                $table->enum('status', ['pending', 'processing', 'shipped', 'delivered', 'cancelled', 'refunded'])->default('pending');
                $table->decimal('subtotal', 15, 2)->default(0);
                $table->decimal('tax_amount', 15, 2)->default(0);
                $table->decimal('shipping_amount', 15, 2)->default(0);
                $table->decimal('discount_amount', 15, 2)->default(0);
                $table->decimal('total_amount', 15, 2)->default(0);
                $table->string('currency', 3)->default('VND');
                $table->string('customer_name')->nullable();
                $table->string('customer_email')->nullable();
                $table->string('customer_phone')->nullable();
                $table->json('billing_address')->nullable();
                $table->json('shipping_address')->nullable();
                $table->string('payment_method')->nullable();
                $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');
                $table->timestamp('paid_at')->nullable();
                $table->text('customer_notes')->nullable();
                $table->text('internal_notes')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('order_items')) {
            Schema::create('order_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('tenant_id')->nullable();
                $table->unsignedBigInteger('project_id')->nullable();
                $table->unsignedBigInteger('order_id')->nullable();
                $table->unsignedBigInteger('product_id')->nullable();
                $table->unsignedBigInteger('product_variation_id')->nullable();
                $table->string('product_name')->nullable();
                $table->string('product_sku')->nullable();
                $table->json('product_attributes')->nullable();
                $table->decimal('unit_price', 15, 2)->default(0);
                $table->integer('quantity')->default(1);
                $table->decimal('total_price', 15, 2)->default(0);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('order_status_histories')) {
            Schema::create('order_status_histories', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('tenant_id')->nullable();
                $table->unsignedBigInteger('project_id')->nullable();
                $table->unsignedBigInteger('order_id')->nullable();
                $table->string('from_status')->nullable();
                $table->string('to_status')->nullable();
                $table->text('notes')->nullable();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('product_attribute_value_mappings')) {
            Schema::create('product_attribute_value_mappings', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('tenant_id')->nullable();
                $table->unsignedBigInteger('project_id')->nullable();
                $table->unsignedBigInteger('product_id');
                $table->unsignedBigInteger('product_attribute_id');
                $table->unsignedBigInteger('product_attribute_value_id');
                $table->timestamps();
            });
        }

        $tables = [
            'brands',
            'product_attributes',
            'product_attribute_values',
            'attribute_groups',
            'products_enhanced',
            'product_categories',
            'product_reviews',
            'product_variations',
            'orders',
            'order_items',
            'order_status_histories',
            'menu_items',
            'product_attribute_value_mappings',
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    if (! Schema::hasColumn($tableName, 'tenant_id')) {
                        $table->unsignedBigInteger('tenant_id')->nullable()->after('id');
                        $table->index('tenant_id');
                    }
                    if (! Schema::hasColumn($tableName, 'project_id')) {
                        $table->unsignedBigInteger('project_id')->nullable()->after('tenant_id');
                        $table->index('project_id');
                    }
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_status_histories');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('product_attribute_value_mappings');
    }
};
