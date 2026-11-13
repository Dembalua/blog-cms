<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('affiliate_link_clicks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('affiliate_link_id')->constrained()->cascadeOnDelete();
            $table->foreignId('content_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('clicks')->default(0);
            $table->timestamp('last_clicked_at')->nullable();
            $table->unique(['affiliate_link_id', 'content_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('affiliate_link_clicks');
    }
};
