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
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('title', 2048);
            $table->string('slug', 2048);
            $table->string('thumbnail', 2048)->nullable();
            $table->longText('body')->nullable();
            $table->longText('additional_body')->nullable();
            $table->longText('map_body')->nullable();
            $table->boolean('active')->default(false);
            $table->string('type', 255)->default('article');
            $table->boolean('external')->default(false);
            $table->datetime('published_at')->nullable();
            $table->foreignIdFor(\App\Models\User::class, 'user_id');
            $table->timestamps();
        });

        Schema::create('category_article', function (Blueprint $table) {
            $table->id();
            $table->integer('sort_order')->default(0);
            $table->foreignId('category_id')->nullable()->references('id')->on('categories')->onDelete('cascade');
            $table->foreignId('article_id')->nullable()->references('id')->on('articles')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
        Schema::dropIfExists('category_article');
    }
};
