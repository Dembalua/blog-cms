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
        Schema::create('affiliate_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('affiliate_network_id')->constrained()->restrictOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('url');
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('clicks')->default(0);
            $table->timestamp('last_checked_at')->nullable();
            $table->integer('last_status_code')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('affiliate_links');
    }
};
