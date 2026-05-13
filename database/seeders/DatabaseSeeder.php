<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ១. Users (សម្រាប់តារាង users)
        DB::table('users')->insert([
            [
                'name' => 'Admin User',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('12345678'),
                'role' => 'admin',
                'created_at' => now(),
            ],
            [
                'name' => 'Samet Moeun', // យោងតាមឈ្មោះអ្នកប្រើប្រាស់
                'email' => 'samet@gmail.com',
                'password' => Hash::make('12345678'),
                'role' => 'user',
                'created_at' => now(),
            ]
        ]);

        // ២. Item Categories (សម្រាប់តារាង item_categories)
        DB::table('item_categories')->insert([
            ['name' => 'Coffee', 'created_at' => now()],
            ['name' => 'Tea', 'created_at' => now()],
            ['name' => 'Smoothie', 'created_at' => now()],
        ]);

        // ៣. Sizes (សម្រាប់តារាង sizes)
        DB::table('sizes')->insert([
            ['size_name' => 'Small', 'size_code' => 'S', 'created_at' => now()],
            ['size_name' => 'Medium', 'size_code' => 'M', 'created_at' => now()],
            ['size_name' => 'Large', 'size_code' => 'L', 'created_at' => now()],
        ]);

        // ៤. Payment Statuses (សម្រាប់តារាង payment_statuses)
        DB::table('payment_statuses')->insert([
            ['status_name' => 'Paid', 'created_at' => now()],
            ['status_name' => 'Unpaid', 'created_at' => now()],
            ['status_name' => 'Refunded', 'created_at' => now()],
        ]);

        // ៥. Items (សម្រាប់តារាង items ដែលមាន Foreign Key ទៅ item_categories)
        DB::table('items')->insert([
            ['item_category_id' => 1, 'name' => 'Iced Latte', 'status' => 'In Stock', 'created_at' => now()],
            ['item_category_id' => 1, 'name' => 'Cappuccino', 'status' => 'In Stock', 'created_at' => now()],
            ['item_category_id' => 2, 'name' => 'Green Tea', 'status' => 'In Stock', 'created_at' => now()],
        ]);

        // ៦. Item Size Prices (សម្រាប់តារាង item_size_prices)
        DB::table('item_size_prices')->insert([
            ['item_id' => 1, 'size_id' => 1, 'price' => 2.50, 'created_at' => now()],
            ['item_id' => 1, 'size_id' => 2, 'price' => 3.00, 'created_at' => now()],
            ['item_id' => 2, 'size_id' => 1, 'price' => 2.25, 'created_at' => now()],
        ]);

        // ៧. Discounts (សម្រាប់តារាង discounts)
        DB::table('discounts')->insert([
            [
                'name' => 'Grand Opening',
                'type' => 'percentage',
                'value' => 10.00,
                'start_date' => now(),
                'end_date' => now()->addDays(30),
                'created_at' => now(),
            ],
        ]);

        // ៨. Orders (សម្រាប់តារាង orders ដែលទាក់ទងនឹង users និង discounts)
        DB::table('orders')->insert([
            [
                'discount_id' => 1,
                'total_amount' => 5.50,
                'table_number' => 'A1',
                'user_id' => 1,
                'created_at' => now(),
            ],
        ]);
    }
}