<?php

use Illuminate\Database\Eloquent\Model;
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
        Schema::create('stok_masuk', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bahan_id')->constrained('bahan')->restrictOnDelete();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete(); // manajer pencatat
            $table->decimal('jumlah', 14, 3);
            $table->date('tanggal');
            $table->timestamps();
        });

        DB::statement('ALTER TABLE stok_masuk ADD CONSTRAINT chk_stok_masuk_jumlah CHECK (jumlah > 0)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stok_masuk');
    }
};
