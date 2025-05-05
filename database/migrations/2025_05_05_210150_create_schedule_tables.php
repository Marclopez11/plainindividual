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
        Schema::create('timetables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('support_plan_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('configuration')->nullable(); // JSON configuration for the timetable layout
            $table->timestamps();
        });

        Schema::create('timetable_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('timetable_id')->constrained()->onDelete('cascade');
            $table->string('day');
            $table->string('time_start');
            $table->string('time_end');
            $table->string('subject');
            $table->string('type')->nullable(); // For color-coding: regular, codocencia, desdoblament, etc
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('timetable_slots');
        Schema::dropIfExists('timetables');
    }
};
