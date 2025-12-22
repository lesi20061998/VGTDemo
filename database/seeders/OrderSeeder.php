<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductCategory;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->seedForProject('hd001');
    }

    /**
     * Seed orders for a specific project
     */
    public function seedForProject(string $projectCode): void
    {
        $projectDbName = 'project_'.$projectCode;

        // Switch to project database
        config(['database.connections.project.database' => $projectDbName]);
        DB::purge('project');

        $this->command->info("Creating orders for project {$projectCode}...");

        // Get some products to use in orders
        $products = Product::on('project')->take(10)->get();

        if ($products->isEmpty()) {
            $this->command->warn('No products found. Creating some sample products first...');
            $this->createSampleProducts();
            $products = Product::on('project')->take(10)->get();
        }

        $statuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled', 'refunded'];
        $paymentStatuses = ['pending', 'paid', 'failed', 'refunded'];
        $paymentMethods = ['credit_card', 'bank_transfer', 'cash_on_delivery', 'paypal', 'stripe'];

        // Create orders for the last 6 months
        $startDate = Carbon::now()->subMonths(6);
        $endDate = Carbon::now();

        for ($i = 0; $i < 150; $i++) {
            $orderDate = Carbon::createFromTimestamp(
                rand($startDate->timestamp, $endDate->timestamp)
            );

            $status = $statuses[array_rand($statuses)];
            $paymentStatus = $paymentStatuses[array_rand($paymentStatuses)];

            // Adjust payment status based on order status
            if (in_array($status, ['delivered', 'shipped'])) {
                $paymentStatus = 'paid';
            } elseif ($status === 'cancelled') {
                $paymentStatus = rand(0, 1) ? 'failed' : 'refunded';
            }

            $order = Order::on('project')->create([
                'order_number' => 'HD'.str_pad($i + 1, 6, '0', STR_PAD_LEFT),
                'status' => $status,
                'customer_name' => $this->generateCustomerName(),
                'customer_email' => $this->generateEmail(),
                'customer_phone' => $this->generatePhone(),
                'billing_address' => $this->generateAddress(),
                'shipping_address' => $this->generateAddress(),
                'payment_method' => $paymentMethods[array_rand($paymentMethods)],
                'payment_status' => $paymentStatus,
                'paid_at' => $paymentStatus === 'paid' ? $orderDate->addHours(rand(1, 24)) : null,
                'currency' => 'VND',
                'subtotal' => 0, // Will be updated later
                'tax_amount' => 0,
                'shipping_amount' => 0,
                'discount_amount' => 0,
                'total_amount' => 0,
                'customer_notes' => rand(0, 3) === 0 ? $this->generateCustomerNotes() : null,
                'internal_notes' => rand(0, 4) === 0 ? $this->generateInternalNotes() : null,
                'created_at' => $orderDate,
                'updated_at' => $orderDate->copy()->addHours(rand(1, 48)),
            ]);

            // Add order items
            $itemCount = rand(1, 5);
            $subtotal = 0;

            for ($j = 0; $j < $itemCount; $j++) {
                $product = $products->random();
                $quantity = rand(1, 3);
                $unitPrice = rand(100000, 2000000); // 100k to 2M VND
                $totalPrice = $unitPrice * $quantity;

                OrderItem::on('project')->create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_sku' => $product->sku ?? 'SKU-'.$product->id,
                    'product_attributes' => $this->generateProductAttributes(),
                    'unit_price' => $unitPrice,
                    'quantity' => $quantity,
                    'total_price' => $totalPrice,
                ]);

                $subtotal += $totalPrice;
            }

            // Calculate totals
            $taxRate = 0.1; // 10% tax
            $taxAmount = $subtotal * $taxRate;
            $shippingAmount = $subtotal > 500000 ? 0 : rand(20000, 50000); // Free shipping over 500k
            $discountAmount = rand(0, 2) === 0 ? rand(10000, 100000) : 0; // Random discount
            $totalAmount = $subtotal + $taxAmount + $shippingAmount - $discountAmount;

            // Update order totals
            $order->update([
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'shipping_amount' => $shippingAmount,
                'discount_amount' => $discountAmount,
                'total_amount' => $totalAmount,
            ]);

            if ($i % 20 === 0) {
                $this->command->info("Created {$i} orders...");
            }
        }

        $this->command->info('✅ Successfully created 150 orders for project hd001');
    }

    private function createSampleProducts(): void
    {
        $categories = ProductCategory::on('project')->take(5)->get();

        if ($categories->isEmpty()) {
            // Create some categories first
            $categoryNames = ['Điện tử', 'Thời trang', 'Gia dụng', 'Sách', 'Thể thao'];
            foreach ($categoryNames as $name) {
                ProductCategory::on('project')->create([
                    'name' => $name,
                    'slug' => \Str::slug($name),
                    'level' => 0,
                    'path' => \Str::slug($name),
                    'is_active' => true,
                ]);
            }
            $categories = ProductCategory::on('project')->get();
        }

        $productNames = [
            'iPhone 15 Pro Max', 'Samsung Galaxy S24', 'MacBook Air M3', 'Dell XPS 13',
            'Áo thun nam', 'Quần jeans nữ', 'Giày sneaker', 'Túi xách da',
            'Nồi cơm điện', 'Máy xay sinh tố', 'Bàn làm việc', 'Ghế văn phòng',
            'Sách lập trình', 'Tiểu thuyết hay', 'Sách kinh doanh', 'Từ điển',
            'Bóng đá', 'Vợt cầu lông', 'Giày chạy bộ', 'Áo thể thao',
        ];

        foreach ($productNames as $index => $name) {
            Product::on('project')->create([
                'name' => $name,
                'slug' => \Str::slug($name),
                'sku' => 'PRD-'.str_pad($index + 1, 4, '0', STR_PAD_LEFT),
                'description' => "Mô tả chi tiết cho sản phẩm {$name}",
                'price' => rand(100000, 5000000),
                'compare_price' => rand(5100000, 6000000),
                'cost_price' => rand(50000, 2000000),
                'stock_quantity' => rand(10, 100),
                'product_category_id' => $categories->random()->id,
                'is_active' => true,
                'is_featured' => rand(0, 1),
            ]);
        }
    }

    private function generateCustomerName(): string
    {
        $firstNames = ['Nguyễn', 'Trần', 'Lê', 'Phạm', 'Hoàng', 'Huỳnh', 'Phan', 'Vũ', 'Võ', 'Đặng'];
        $lastNames = ['Văn Anh', 'Thị Bình', 'Minh Châu', 'Hoàng Dũng', 'Thị Hoa', 'Văn Khoa', 'Thị Lan', 'Minh Nam', 'Thị Oanh', 'Văn Phúc'];

        return $firstNames[array_rand($firstNames)].' '.$lastNames[array_rand($lastNames)];
    }

    private function generateEmail(): string
    {
        $domains = ['gmail.com', 'yahoo.com', 'hotmail.com', 'outlook.com'];
        $username = 'user'.rand(1000, 9999);

        return $username.'@'.$domains[array_rand($domains)];
    }

    private function generatePhone(): string
    {
        $prefixes = ['090', '091', '094', '083', '084', '085', '081', '082'];

        return $prefixes[array_rand($prefixes)].rand(1000000, 9999999);
    }

    private function generateAddress(): array
    {
        $streets = ['Nguyễn Huệ', 'Lê Lợi', 'Trần Hưng Đạo', 'Hai Bà Trưng', 'Điện Biên Phủ'];
        $districts = ['Quận 1', 'Quận 3', 'Quận 5', 'Quận 7', 'Quận Bình Thạnh'];
        $cities = ['TP. Hồ Chí Minh', 'Hà Nội', 'Đà Nẵng', 'Cần Thơ'];

        return [
            'street' => rand(1, 999).' '.$streets[array_rand($streets)],
            'district' => $districts[array_rand($districts)],
            'city' => $cities[array_rand($cities)],
            'postal_code' => rand(10000, 99999),
        ];
    }

    private function generateCustomerNotes(): string
    {
        $notes = [
            'Giao hàng ngoài giờ hành chính',
            'Gọi trước khi giao',
            'Để hàng tại bảo vệ',
            'Giao hàng cuối tuần',
            'Kiểm tra hàng trước khi thanh toán',
        ];

        return $notes[array_rand($notes)];
    }

    private function generateInternalNotes(): string
    {
        $notes = [
            'Khách hàng VIP',
            'Đã xác nhận đơn hàng qua điện thoại',
            'Yêu cầu đóng gói cẩn thận',
            'Khách hàng thường xuyên',
            'Cần xác nhận lại địa chỉ',
        ];

        return $notes[array_rand($notes)];
    }

    private function generateProductAttributes(): array
    {
        $attributes = [
            ['name' => 'Màu sắc', 'value' => ['Đỏ', 'Xanh', 'Vàng', 'Đen', 'Trắng'][array_rand(['Đỏ', 'Xanh', 'Vàng', 'Đen', 'Trắng'])]],
            ['name' => 'Kích thước', 'value' => ['S', 'M', 'L', 'XL'][array_rand(['S', 'M', 'L', 'XL'])]],
        ];

        return rand(0, 1) ? [$attributes[array_rand($attributes)]] : [];
    }
}
