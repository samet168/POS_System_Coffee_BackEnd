<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

use App\Models\{
    User,
    ItemCategory,
    Item,
    Size,
    ItemSizePrice,
    Discount,
    PaymentType,
    PaymentStatus,
    Order,
    OrderItem,
    Invoice
};

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ================= USER =================
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('12345678'),
            'role' => 'admin'
        ]);

        $user = User::create([
            'name' => 'User',
            'email' => 'user@gmail.com',
            'password' => Hash::make('12345678'),
            'role' => 'user'
        ]);

        // ================= CATEGORY =================
        $coffee = ItemCategory::create(['name' => 'Coffee']);
        $tea    = ItemCategory::create(['name' => 'Tea']);

        // ================= ITEM =================
        $latte = Item::create([
            'item_category_id' => $coffee->_id ?? $coffee->id,
            'name' => 'Latte',
            'status' => 'In Stock'
        ]);

        $americano = Item::create([
            'item_category_id' => $coffee->_id ?? $coffee->id,
            'name' => 'Americano',
            'status' => 'In Stock'
        ]);

        // ================= SIZE =================
        $small = Size::create([
            'size_name' => 'Small',
            'size_code' => 'S'
        ]);

        $medium = Size::create([
            'size_name' => 'Medium',
            'size_code' => 'M'
        ]);

        $large = Size::create([
            'size_name' => 'Large',
            'size_code' => 'L'
        ]);

        // ================= ITEM SIZE PRICE =================
        ItemSizePrice::create([
            'item_id' => $latte->_id ?? $latte->id,
            'size_id' => $small->_id ?? $small->id,
            'price' => 2.5
        ]);

        ItemSizePrice::create([
            'item_id' => $latte->_id ?? $latte->id,
            'size_id' => $medium->_id ?? $medium->id,
            'price' => 3.0
        ]);

        ItemSizePrice::create([
            'item_id' => $latte->_id ?? $latte->id,
            'size_id' => $large->_id ?? $large->id,
            'price' => 3.5
        ]);

        // ================= DISCOUNT =================
        $discount = Discount::create([
            'name' => 'New Year Promo',
            'type' => 'percentage',
            'value' => 10
        ]);

        // ================= PAYMENT TYPE =================
        $cash = PaymentType::create(['type_name' => 'Cash']);
        $aba  = PaymentType::create(['type_name' => 'ABA']);

        // ================= PAYMENT STATUS =================
        $paid = PaymentStatus::create(['status_name' => 'Paid']);
        $pending = PaymentStatus::create(['status_name' => 'Pending']);

        // ================= ORDER =================
        $order = Order::create([
            'user_id' => $user->_id ?? $user->id,
            'total_amount' => 5.0,
            'table_number' => 'T01'
        ]);

        // ================= ORDER ITEM =================
        OrderItem::create([
            'order_id' => $order->_id ?? $order->id,
            'item_id' => $latte->_id ?? $latte->id,
            'size_id' => $small->_id ?? $small->id,
            'quantity' => 1,
            'unit_price' => 2.5
        ]);

        // ================= INVOICE =================
        Invoice::create([
            'order_ids' => [$order->_id ?? $order->id],
            'invoice_no' => 'INV-' . time(),
            'payment_status_id' => $paid->_id ?? $paid->id,
            'payment_type_id' => $cash->_id ?? $cash->id,
            'total_amount' => 5.0,
            'total_paid' => 5.0,
            'change_amount' => 0
        ]);

        // ================= DISCOUNT LINK (optional use) =================
        // (បើ system អ្នក support order discount)
        $order->update([
            'discount_id' => $discount->_id ?? $discount->id
        ]);
    }
}