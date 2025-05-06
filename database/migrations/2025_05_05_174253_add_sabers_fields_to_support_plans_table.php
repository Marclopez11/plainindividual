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
        Schema::table('support_plans', function (Blueprint $table) {
            $table->json('area_materia')->nullable();
            $table->json('bloc_sabers')->nullable();
            $table->json('saber')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('support_plans', function (Blueprint $table) {
            $table->dropColumn('area_materia');
            $table->dropColumn('bloc_sabers');
            $table->dropColumn('saber');
        });
    }
};
