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
        Schema::create('ranking_teams', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->text('content');
            $table->timestamps();
        });

        Schema::create('ranking_drivers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->integer('team');
            $table->text('content');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ranking_teams');
        Schema::dropIfExists('ranking_drivers');
    }
};
