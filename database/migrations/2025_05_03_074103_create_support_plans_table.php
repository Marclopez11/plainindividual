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
        Schema::create('support_plans', function (Blueprint $table) {
            $table->id();
            $table->string('student_name');
            $table->date('birth_date')->nullable();
            $table->string('educational_level')->nullable();
            $table->string('course')->nullable();
            $table->string('school_year')->nullable();
            $table->string('tutor_name')->nullable();
            $table->string('assessment_team')->nullable();
            $table->string('teacher_name')->nullable();
            $table->date('revision_date')->nullable();
            $table->date('elaboration_date')->nullable();
            $table->date('family_agreement_date')->nullable();
            $table->text('other_data')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_plans');
    }
};
