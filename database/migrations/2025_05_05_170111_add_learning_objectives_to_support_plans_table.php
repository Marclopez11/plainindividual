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
            $table->json('learning_objectives')->nullable();
            $table->json('evaluation_criteria')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('support_plans', function (Blueprint $table) {
            $table->dropColumn('learning_objectives');
            $table->dropColumn('evaluation_criteria');
        });
    }
};
