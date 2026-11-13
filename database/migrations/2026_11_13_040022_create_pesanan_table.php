<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pesanan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pelanggan_id')->nullable()->constrained('pengguna')->restrictOnDelete();
            $table->foreignId('dibuat_oleh')->constrained('pengguna')->restrictOnDelete();
            $table->unsignedTinyInteger('nomor_meja')->nullable();
            $table->enum('metode_bayar', ['tunai', 'qris']);
            $table->enum('status', ['baru', 'diproses', 'selesai', 'dibatalkan'])->default('baru');
            $table->unsignedInteger('subtotal');
            $table->unsignedInteger('service');
            $table->unsignedInteger('pajak');
            $table->unsignedInteger('total');
            $table->timestamps();

            $table->index(['pelanggan_id', 'status']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pesanan');
    }
};
