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
            $table->string('birth_place')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('other_languages')->nullable();
            $table->string('group')->nullable();
            $table->date('school_incorporation_date')->nullable();
            $table->date('catalonia_arrival_date')->nullable();
            $table->date('educational_system_date')->nullable();
            $table->text('previous_schools')->nullable();
            $table->text('previous_schooling')->nullable();
            $table->string('course_retention')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('support_plans', function (Blueprint $table) {
            $table->dropColumn([
                'birth_place',
                'phone',
                'address',
                'other_languages',
                'group',
                'school_incorporation_date',
                'catalonia_arrival_date',
                'educational_system_date',
                'previous_schools',
                'previous_schooling',
                'course_retention'
            ]);
        });
    }
};
