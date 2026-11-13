<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bahan', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('kunci_nama')->unique();
            $table->enum('satuan_dasar', ['g', 'ml', 'pcs']);
            $table->decimal('stok', 14, 3)->default(0);
            $table->decimal('batas_minimum', 14, 3)->default(0);
            $table->decimal('harga_per_satuan', 14, 4)->default(0);
            $table->timestamp('harga_diperbarui_pada')->nullable();
            $table->timestamps();
        });

        DB::statement('ALTER TABLE bahan ADD CONSTRAINT chk_bahan_stok CHECK (stok >= 0)');
        DB::statement('ALTER TABLE bahan ADD CONSTRAINT chk_bahan_batas CHECK (batas_minimum >= 0)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bahan');
    }
};
