<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nama diskon (contoh: "Diskon Lebaran", "Promo Akhir Tahun")
            $table->string('code')->unique(); // Kode kupon (contoh: "LEBARAN2024")
            $table->enum('type', ['percentage', 'fixed']); // Jenis: persen atau nominal
            $table->decimal('value', 10, 2); // Nilai diskon (persen atau nominal)
            $table->decimal('min_transaction', 10, 2)->default(0); // Minimal transaksi
            $table->decimal('max_discount', 10, 2)->nullable(); // Maksimal diskon (untuk percentage)
            $table->integer('usage_limit')->default(0); // Batas penggunaan (0 = unlimited)
            $table->integer('usage_count')->default(0); // Jumlah sudah digunakan
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discounts');
    }
};