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
            $table->json('transversal_objectives')->nullable();
            $table->json('transversal_criteria')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('support_plans', function (Blueprint $table) {
            $table->dropColumn('transversal_objectives');
            $table->dropColumn('transversal_criteria');
        });
    }
};
