
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_id')->constrained('menus')->cascadeOnDelete();
            $table->string('title');
            $table->morphs('model');
            $table->foreignId('parent_id')->nullable()->constrained('menu_items')->cascadeOnDelete();
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->unique(['model_type', 'model_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_items');
    }
};
