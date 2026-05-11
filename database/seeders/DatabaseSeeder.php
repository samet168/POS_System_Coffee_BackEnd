<?php

namespace Database\Seeders;

use App\Models\Discount;
use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\ItemSizePrice;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentStatus;
use App\Models\PaymentType;
use App\Models\Size;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // ==================== 1. Users ====================
        User::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('123456'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Staff',
            'email' => 'staff@gmail.com',
            'password' => Hash::make('123456'),
            'role' => 'user',
        ]);

        // ==================== 2. Item Categories ====================
        $categories = [
            ['name' => 'Coffee'],
            ['name' => 'Tea'],
            ['name' => 'Smoothie'],
            ['name' => 'Milkshake'],
            ['name' => 'Juice'],
        ];

        foreach ($categories as $cat) {
            ItemCategory::create($cat);
        }

        // ==================== 3. Sizes ====================
        $sizes = [
            ['size_name' => 'Small',  'size_code' => 'S'],
            ['size_name' => 'Medium', 'size_code' => 'M'],
            ['size_name' => 'Large',  'size_code' => 'L'],
        ];

        foreach ($sizes as $size) {
            Size::create($size);
        }

        // ==================== 4. Payment Statuses ====================
        $paymentStatuses = ['Paid', 'Unpaid', 'Refunded'];
        foreach ($paymentStatuses as $status) {
            PaymentStatus::create(['status_name' => $status]);
        }

        // ==================== 5. Payment Types ====================
        $paymentTypes = ['Cash', 'ABA', 'Wing', 'Card', 'QR Code'];
        foreach ($paymentTypes as $type) {
            PaymentType::create(['type_name' => $type]);
        }

        // ==================== 6. Items ====================
        $items = [
            ['item_category_id' => 1, 'name' => 'Americano', 'status' => 'In Stock'],
            ['item_category_id' => 1, 'name' => 'Latte', 'status' => 'In Stock'],
            ['item_category_id' => 1, 'name' => 'Cappuccino', 'status' => 'In Stock'],
            ['item_category_id' => 2, 'name' => 'Thai Tea', 'status' => 'In Stock'],
            ['item_category_id' => 2, 'name' => 'Green Tea', 'status' => 'In Stock'],
            ['item_category_id' => 3, 'name' => 'Strawberry Smoothie', 'status' => 'In Stock'],
            ['item_category_id' => 5, 'name' => 'Orange Juice', 'status' => 'In Stock'],
        ];

        foreach ($items as $itemData) {
            Item::create($itemData);
        }

        // ==================== 7. Item Size Prices ====================
        $itemSizePrices = [
            // Americano
            ['item_id' => 1, 'size_id' => 1, 'price' => 2.50],
            ['item_id' => 1, 'size_id' => 2, 'price' => 3.00],
            ['item_id' => 1, 'size_id' => 3, 'price' => 3.50],
            // Latte
            ['item_id' => 2, 'size_id' => 1, 'price' => 3.00],
            ['item_id' => 2, 'size_id' => 2, 'price' => 3.50],
            ['item_id' => 2, 'size_id' => 3, 'price' => 4.00],
            // Thai Tea
            ['item_id' => 4, 'size_id' => 1, 'price' => 2.00],
            ['item_id' => 4, 'size_id' => 2, 'price' => 2.50],
            ['item_id' => 4, 'size_id' => 3, 'price' => 3.00],
        ];

        foreach ($itemSizePrices as $price) {
            ItemSizePrice::create($price);
        }

        // ==================== 8. Discounts ====================
        Discount::create([
            'name' => 'Happy Hour 10%',
            'type' => 'percentage',
            'value' => 10,
            'start_date' => '2026-05-01',
            'end_date' => '2026-05-31',
        ]);

        Discount::create([
            'name' => 'Special Discount $1',
            'type' => 'fixed',
            'value' => 1.00,
            'start_date' => '2026-05-01',
            'end_date' => null,
        ]);

        echo "✅ Seeding completed successfully!\n";
    }
}