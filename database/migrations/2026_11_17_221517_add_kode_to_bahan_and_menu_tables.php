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
        // kode dari CSV (BHN-001, MNU-001). nullable karena bahan/menu buatan manual nggak punya kode.
        // kode bahan juga dipakai buat nyocokin harga dari Supplier
        Schema::table('bahan', function (Blueprint $table) {
            $table->string('kode', 20)->nullable()->unique()->after('id');
        });

        Schema::table('menu', function (Blueprint $table) {
            $table->string('kode', 20)->nullable()->unique()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bahan', function (Blueprint $table) {
            $table->dropUnique(['kode']);
            $table->dropColumn('kode');
        });

        Schema::table('menu', function (Blueprint $table) {
            $table->dropUnique(['kode']);
            $table->dropColumn('kode');
        });
    }
};
