<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('borrowings', function (Blueprint $table) {
            // Harga sewa per hari
            $table->decimal('rental_price_per_day', 10, 2)->default(0)->after('return_date');
            
            // Jumlah hari pinjam
            $table->integer('total_days')->default(0)->after('rental_price_per_day');
            
            // Total harga sewa
            $table->decimal('subtotal', 10, 2)->default(0)->after('total_days');
            
            // Diskon yang digunakan (kode kupon)
            $table->string('discount_code')->nullable()->after('subtotal');
            
            // Nominal diskon
            $table->decimal('discount_amount', 10, 2)->default(0)->after('discount_code');
            
            // Total setelah diskon
            $table->decimal('total_after_discount', 10, 2)->default(0)->after('discount_amount');
            
            // Denda keterlambatan
            $table->decimal('late_fee', 10, 2)->default(0)->after('total_after_discount');
            
            // Deposit yang dibayar
            $table->decimal('deposit_paid', 10, 2)->default(0)->after('late_fee');
            
            // Total yang harus dibayar
            $table->decimal('grand_total', 10, 2)->default(0)->after('deposit_paid');
            
            // Status pembayaran
            $table->enum('payment_status', ['pending', 'paid', 'refunded'])->default('pending')->after('grand_total');
            
            // Metode pembayaran
            $table->string('payment_method')->nullable()->after('payment_status');
            
            // Tanggal bayar
            $table->timestamp('paid_at')->nullable()->after('payment_method');
        });
    }

    public function down(): void
    {
        Schema::table('borrowings', function (Blueprint $table) {
            $table->dropColumn([
                'rental_price_per_day',
                'total_days',
                'subtotal',
                'discount_code',
                'discount_amount',
                'total_after_discount',
                'late_fee',
                'deposit_paid',
                'grand_total',
                'payment_status',
                'payment_method',
                'paid_at',
            ]);
        });
    }
};