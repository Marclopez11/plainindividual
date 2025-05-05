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
            $table->text('competencies_alumne_capabilities')->nullable();
            $table->text('learning_strong_points')->nullable();
            $table->text('learning_improvement_points')->nullable();
            $table->text('student_interests')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('support_plans', function (Blueprint $table) {
            $table->dropColumn([
                'competencies_alumne_capabilities',
                'learning_strong_points',
                'learning_improvement_points',
                'student_interests',
            ]);
        });
    }
};
