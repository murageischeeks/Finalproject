<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('lab_results', function (Blueprint $table) {
        $table->text('notes')->nullable()->after('test_type');
    });
}

public function down()
{
    Schema::table('lab_results', function (Blueprint $table) {
        $table->dropColumn('notes');
    });
}

};
