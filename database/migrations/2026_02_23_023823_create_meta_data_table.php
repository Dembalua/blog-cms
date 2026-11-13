<?php

use App\Enums\MetaDataRobots;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meta_data', function (Blueprint $table) {
            $table->id();
            $table->morphs('model');
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->json('meta_keywords')->nullable();
            $table->string('og_title')->nullable();
            $table->text('og_description')->nullable();
            $table->foreignId('og_image_id')->nullable()->constrained('curator')->nullOnDelete();
            $table->string('robots')->default(MetaDataRobots::INDEX_FOLLOW->value);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meta_data');
    }
};
