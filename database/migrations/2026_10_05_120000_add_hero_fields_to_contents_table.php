<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contents', function (Blueprint $table) {
            $table->string('hero_kicker')->nullable()->after('content');
            $table->string('hero_headline')->nullable()->after('hero_kicker');
            $table->string('hero_headline_accent')->nullable()->after('hero_headline');
            $table->text('hero_text')->nullable()->after('hero_headline_accent');
            $table->foreignId('hero_image_id')->nullable()->after('hero_text')->constrained('curator')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('contents', function (Blueprint $table) {
            $table->dropConstrainedForeignId('hero_image_id');
            $table->dropColumn(['hero_kicker', 'hero_headline', 'hero_headline_accent', 'hero_text']);
        });
    }
};
