<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cluster_related_cluster', function (Blueprint $table) {
            $table->foreignId('cluster_id')->constrained()->cascadeOnDelete();
            $table->foreignId('related_cluster_id')->constrained('clusters')->cascadeOnDelete()->index();
            $table->primary(['cluster_id', 'related_cluster_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cluster_related_cluster');
    }
};
