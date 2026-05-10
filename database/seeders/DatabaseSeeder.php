<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {


        // 1. Users
        DB::table('users')->truncate();
        DB::table('users')->insert([
            [
                'id' => 1,
                'name' => 'Admin User',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'image' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 2,
                'name' => 'Cashier Executive',
                'email' => 'cashier@gmail.com',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'image' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ]);

        // 2. Item Categories
        DB::table('item_categories')->truncate();
        DB::table('item_categories')->insert([
            ['id' => 1, 'name' => 'Coffee', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => 2, 'name' => 'Tea', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => 3, 'name' => 'Frappe', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => 4, 'name' => 'Bakery', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ]);

        // 3. Sizes
        DB::table('sizes')->truncate();
        DB::table('sizes')->insert([
            ['id' => 1, 'size_name' => 'Small', 'size_code' => 'S', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => 2, 'size_name' => 'Medium', 'size_code' => 'M', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => 3, 'size_name' => 'Large', 'size_code' => 'L', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ]);

        // 4. Ice Levels
        DB::table('ice_levels')->truncate();
        DB::table('ice_levels')->insert([
            ['id' => 1, 'level_name' => '0% (No Ice)', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => 2, 'level_name' => '50% (Less Ice)', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => 3, 'level_name' => '100% (Normal Ice)', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ]);

        // 5. Sugar Levels
        DB::table('sugar_levels')->truncate();
        DB::table('sugar_levels')->insert([
            ['id' => 1, 'level_name' => '0% (No Sugar)', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => 2, 'level_name' => '25%', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => 3, 'level_name' => '50%', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => 4, 'level_name' => '100% (Normal)', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ]);

        // 6. Item Statuses
        DB::table('item_statuses')->truncate();
        DB::table('item_statuses')->insert([
            ['id' => 1, 'status_name' => 'In Stock'],
            ['id' => 2, 'status_name' => 'Out of Stock'],
        ]);

        // 7. Order Statuses
        DB::table('order_statuses')->truncate();
        DB::table('order_statuses')->insert([
            ['id' => 1, 'status_name' => 'Pending'],
            ['id' => 2, 'status_name' => 'Completed'],
            ['id' => 3, 'status_name' => 'Cancelled'],
        ]);

        // 8. Payment Statuses
        DB::table('payment_statuses')->truncate();
        DB::table('payment_statuses')->insert([
            ['id' => 1, 'status_name' => 'Unpaid'],
            ['id' => 2, 'status_name' => 'Paid'],
            ['id' => 3, 'status_name' => 'Refunded'],
        ]);

        // 9. Payment Types
        DB::table('payment_types')->truncate();
        DB::table('payment_types')->insert([
            ['id' => 1, 'type_name' => 'Cash'],
            ['id' => 2, 'type_name' => 'ABA KHQR'],
            ['id' => 3, 'type_name' => 'Credit Card'],
        ]);




        // 10. Items
        DB::table('items')->truncate();
        DB::table('items')->insert([
            [
                'id' => 1,
                'item_category_id' => 1, // Coffee
                'item_status_id' => 1,   // In Stock
                'name' => 'Iced Latte',
                'image' => 'latte.png',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 2,
                'item_category_id' => 2, // Tea
                'item_status_id' => 1,   // In Stock
                'name' => 'Matcha Green Tea',
                'image' => 'matcha.png',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ]);

        // 11. Item Size Prices
        DB::table('item_size_prices')->truncate();
        DB::table('item_size_prices')->insert([
            // Iced Latte (S: $2.00, M: $2.50, L: $3.00)
            ['id' => 1, 'item_id' => 1, 'size_id' => 1, 'price' => 2.00, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => 2, 'item_id' => 1, 'size_id' => 2, 'price' => 2.50, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => 3, 'item_id' => 1, 'size_id' => 3, 'price' => 3.00, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            // Matcha Green Tea (M: $2.80, L: $3.30)
            ['id' => 4, 'item_id' => 2, 'size_id' => 2, 'price' => 2.80, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => 5, 'item_id' => 2, 'size_id' => 3, 'price' => 3.30, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ]);

        // 12. Item Ice Options
        DB::table('item_ice_options')->truncate();
        DB::table('item_ice_options')->insert([
            ['id' => 1, 'item_id' => 1, 'ice_level_id' => 1],
            ['id' => 2, 'item_id' => 1, 'ice_level_id' => 2],
            ['id' => 3, 'item_id' => 1, 'ice_level_id' => 3],
            ['id' => 4, 'item_id' => 2, 'ice_level_id' => 2],
            ['id' => 5, 'item_id' => 2, 'ice_level_id' => 3],
        ]);

        // 13. Item Sugar Options
        DB::table('item_sugar_options')->truncate();
        DB::table('item_sugar_options')->insert([
            ['id' => 1, 'item_id' => 1, 'sugar_level_id' => 1],
            ['id' => 2, 'item_id' => 1, 'sugar_level_id' => 3],
            ['id' => 3, 'item_id' => 1, 'sugar_level_id' => 4],
            ['id' => 4, 'item_id' => 2, 'sugar_level_id' => 2],
            ['id' => 5, 'item_id' => 2, 'sugar_level_id' => 3],
            ['id' => 6, 'item_id' => 2, 'sugar_level_id' => 4],
        ]);




        // 14. Discounts
        DB::table('discounts')->truncate();
        DB::table('discounts')->insert([
            [
                'id' => 1,
                'name' => 'Opening Promo 10%',
                'type' => 'percentage',
                'value' => 10.00,
                'start_date' => Carbon::now()->startOfMonth()->toDateString(),
                'end_date' => Carbon::now()->addMonths(2)->endOfMonth()->toDateString(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 2,
                'name' => 'Fixed $0.50 Off',
                'type' => 'fixed',
                'value' => 0.50,
                'start_date' => null,
                'end_date' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ]);

        // 15. Orders (ទិន្នន័យបញ្ជាទិញគំរូ)
        DB::table('orders')->truncate();
        DB::table('orders')->insert([
            [
                'id' => 1,
                'order_status_id' => 2,    // Completed
                'discount_id' => 1,        // Opening Promo 10%
                'total_amount' => 7.02,    // សរុបក្រោយបញ្ចុះតម្លៃ ១០% ពី $7.80
                'table_number' => 'Table-05',
                'user_id' => 2,            // Cashier Executive
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ]);

        // 16. Order Items (ព័ត៌មានលម្អិតនៃទំនិញក្នុង Order ខាងលើ)
        DB::table('order_items')->truncate();
        DB::table('order_items')->insert([
            [
                'id' => 1,
                'order_id' => 1,
                'item_id' => 1,            // Iced Latte
                'size_id' => 2,            // Medium (តម្លៃ $2.50)
                'ice_level_id' => 3,       // Normal Ice
                'sugar_level_id' => 3,     // 50% Sugar
                'quantity' => 2,           // ទិញ ២ កែវ
                'unit_price' => 2.50,
                'sub_total' => 5.00,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 2,
                'order_id' => 1,
                'item_id' => 2,            // Matcha Green Tea
                'size_id' => 2,            // Medium (តម្លៃ $2.80)
                'ice_level_id' => 2,       // Less Ice
                'sugar_level_id' => 4,     // Normal Sugar
                'quantity' => 1,           // ទិញ ១ កែវ
                'unit_price' => 2.80,
                'sub_total' => 2.80,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ]);

        // 17. Invoices (វិក្កយបត្រដែលបានទូទាត់រួច)
        DB::table('invoices')->truncate();
        DB::table('invoices')->insert([
            [
                'id' => 1,
                'order_id' => 1,
                'invoice_no' => 'INV-' . date('Ymd') . '-0001',
                'payment_status_id' => 2,  // Paid
                'payment_type_id' => 2,    // ABA KHQR
                'total_paid' => 7.02,
                'change_amount' => 0.00,
                'issued_at' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ]);
    }
}