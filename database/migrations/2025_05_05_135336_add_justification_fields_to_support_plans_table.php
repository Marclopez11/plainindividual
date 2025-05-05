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
            $table->json('justification_reasons')->nullable()->after('other_data');
            $table->string('justification_other')->nullable()->after('justification_reasons');
            $table->text('brief_justification')->nullable()->after('justification_other');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('support_plans', function (Blueprint $table) {
            $table->dropColumn('justification_reasons');
            $table->dropColumn('justification_other');
            $table->dropColumn('brief_justification');
        });
    }
};
