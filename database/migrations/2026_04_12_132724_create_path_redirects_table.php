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
        Schema::create('path_redirects', function (Blueprint $table) {
            $table->id();
            $table->string('old_path')->unique();
            $table->string('new_path')->nullable();
            $table->unsignedSmallInteger('status_code');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('path_redirects');
    }
};
