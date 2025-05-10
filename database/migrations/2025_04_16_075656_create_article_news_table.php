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
        Schema::create('article_news', function (Blueprint $table) {
            $table->id();
            $table->string('title', 2048);
            $table->string('slug', 2048);
            $table->string('thumbnail', 2048)->nullable();
            $table->longText('body')->nullable();
            $table->boolean('active')->default(false);
            $table->datetime('published_at')->nullable();
            $table->foreignIdFor(\App\Models\User::class, 'user_id');
            $table->timestamps();
        });

        Schema::create('article_news_attachment', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_news_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('attachment_id')->nullable()->constrained()->onDelete('cascade');
        });

        Schema::create('article_news_photo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_news_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('photo_id')->nullable()->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('article_news');
        Schema::dropIfExists('article_news_attachment');
        Schema::dropIfExists('article_news_photo');
    }
};
