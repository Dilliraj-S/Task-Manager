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
        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar')->nullable()->after('password');
            $table->string('theme_preference')->default('dark')->after('avatar'); // dark, light, system
            $table->string('density_preference')->default('comfortable')->after('theme_preference'); // comfortable, compact
            $table->string('timezone')->nullable()->after('density_preference');
            $table->string('date_format')->default('Y-m-d')->after('timezone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['avatar','theme_preference','density_preference','timezone','date_format']);
        });
    }
};
