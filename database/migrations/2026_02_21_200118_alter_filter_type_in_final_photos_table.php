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
        Schema::table('final_photos', function (Blueprint $table) {
            // Ubah panjang menjadi 50 atau lebih untuk menampung slug filter
            $table->string('filter_type', 50)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('final_photos', function (Blueprint $table) {
            //
        });
    }
};
