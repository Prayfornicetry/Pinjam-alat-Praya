<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            // Harga sewa normal (per hari)
            $table->decimal('rental_price', 10, 2)->default(0)->after('condition');
            
            // Harga sewa member (per hari) - diskon member
            $table->decimal('member_price', 10, 2)->default(0)->after('rental_price');
            
            // Denda keterlambatan (per hari)
            $table->decimal('late_fee', 10, 2)->default(0)->after('member_price');
            
            // Deposit/jaminan
            $table->decimal('deposit', 10, 2)->default(0)->after('late_fee');
            
            // Status diskon (aktif/tidak)
            $table->boolean('has_discount')->default(false)->after('deposit');
            
            // Persentase diskon (0-100)
            $table->integer('discount_percentage')->default(0)->after('has_discount');
            
            // Tanggal mulai diskon
            $table->date('discount_start')->nullable()->after('discount_percentage');
            
            // Tanggal akhir diskon
            $table->date('discount_end')->nullable()->after('discount_start');
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn([
                'rental_price',
                'member_price',
                'late_fee',
                'deposit',
                'has_discount',
                'discount_percentage',
                'discount_start',
                'discount_end',
            ]);
        });
    }
};