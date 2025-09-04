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
        Schema::create('roles', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('roles_setting', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignUuid('roles_id')->constrained('roles')->onDelete('cascade');
            $table->string('name');
            $table->foreignUuid('jam_id')->constrained('setting_jams')->onDelete('cascade');
            $table->string('operator');
            $table->integer('value');
            $table->integer('point');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
        Schema::dropIfExists('roles_setting');
    }
};
