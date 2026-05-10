<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {

        // USERS
        DB::table('users')->insert([
            [
                'name' => 'Admin',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('12345678'),
                'role' => 'admin',
                'image' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'User',
                'email' => 'user@gmail.com',
                'password' => Hash::make('12345678'),
                'role' => 'user',
                'image' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);


        // ITEM CATEGORIES
        DB::table('item_categories')->insert([
            [
                'name' => 'Coffee',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Tea',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Juice',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);


        // SIZES
        DB::table('sizes')->insert([
            [
                'size_name' => 'Small',
                'size_code' => 'S',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'size_name' => 'Medium',
                'size_code' => 'M',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'size_name' => 'Large',
                'size_code' => 'L',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);


        // ITEM STATUSES
        DB::table('item_statuses')->insert([
            ['status_name' => 'Active'],
            ['status_name' => 'Inactive'],
        ]);


        // ORDER STATUSES
        DB::table('order_statuses')->insert([
            ['status_name' => 'Pending'],
            ['status_name' => 'Completed'],
            ['status_name' => 'Cancelled'],
        ]);


        // PAYMENT STATUSES
        DB::table('payment_statuses')->insert([
            ['status_name' => 'Unpaid'],
            ['status_name' => 'Paid'],
        ]);


        // PAYMENT TYPES
        DB::table('payment_types')->insert([
            ['type_name' => 'Cash'],
            ['type_name' => 'Card'],
            ['type_name' => 'ABA'],
        ]);


        // ITEMS
        DB::table('items')->insert([
            [
                'item_category_id' => 1,
                'item_status_id' => 1,
                'name' => 'Latte',
                'image' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'item_category_id' => 1,
                'item_status_id' => 1,
                'name' => 'Cappuccino',
                'image' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);


        // ITEM SIZE PRICES
        DB::table('item_size_prices')->insert([
            [
                'item_id' => 1,
                'size_id' => 1,
                'price' => 2.50,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'item_id' => 1,
                'size_id' => 2,
                'price' => 3.50,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'item_id' => 1,
                'size_id' => 3,
                'price' => 4.50,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);


        // DISCOUNTS
        DB::table('discounts')->insert([
            [
                'name' => 'New Year',
                'type' => 'percentage',
                'value' => 10,
                'start_date' => now(),
                'end_date' => now()->addDays(30),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);


        // ORDERS
        DB::table('orders')->insert([
            [
                'order_status_id' => 1,
                'discount_id' => 1,
                'total_amount' => 10.00,
                'table_number' => 'A01',
                'user_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);


        // ORDER ITEMS
        DB::table('order_items')->insert([
            [
                'order_id' => 1,
                'item_id' => 1,
                'size_id' => 2,
                'quantity' => 2,
                'unit_price' => 3.50,
                'sub_total' => 7.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);


        // INVOICES
        DB::table('invoices')->insert([
            [
                'order_id' => 1,
                'invoice_no' => 'INV-0001',
                'payment_status_id' => 2,
                'payment_type_id' => 1,
                'total_paid' => 10.00,
                'change_amount' => 0,
                'issued_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}