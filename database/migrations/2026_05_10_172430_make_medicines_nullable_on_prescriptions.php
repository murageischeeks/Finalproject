<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('prescriptions', function (Blueprint $table) {
            // The old 'medicines' column has a NOT NULL constraint but the app
            // uses 'medicine' (singular). Make it nullable so it doesn't block inserts.
            $table->text('medicines')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('prescriptions', function (Blueprint $table) {
            $table->text('medicines')->nullable(false)->change();
        });
    }
};
