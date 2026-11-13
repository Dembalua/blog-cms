
<?php

use App\Enums\ContentStatus;
use App\Enums\ContentType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contents', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('excerpt')->nullable();
            $table->longText('content');
            $table->foreignId('feature_image_id')->nullable()->constrained('curator')->nullOnDelete();
            $table->boolean('is_featured')->default(false);
            $table->string('type')->default(ContentType::POST);
            $table->string('status')->default(ContentStatus::DRAFT);
            $table->dateTime('published_at')->nullable();
            $table->dateTime('revised_at')->nullable();
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->foreignId('cluster_id')->nullable()->constrained('clusters')->nullOnDelete();
            $table->tinyInteger('cluster_position')->nullable();
            $table->boolean('is_pillar')->nullable()->default(null);
            $table->unique(['cluster_id', 'is_pillar']);
            $table->integer('sort')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contents');
    }
};
