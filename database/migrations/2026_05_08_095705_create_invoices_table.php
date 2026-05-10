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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->unique()->constrained();
            $table->string('invoice_no')->unique();
            $table->foreignId('payment_status_id')->constrained();
            $table->foreignId('payment_type_id')->constrained();
            $table->decimal('total_paid', 10, 2);
            $table->decimal('change_amount', 10, 2)->default(0);
            $table->dateTime('issued_at')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
