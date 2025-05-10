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
        Schema::create('project_types', function (Blueprint $table) {
            $table->id();
            $table->string('title', 2048);
            $table->string('slug', 2048);
            $table->timestamps();
        });

        Schema::create('project_articles', function (Blueprint $table) {
            $table->id();
            $table->string('title', 2048);
            $table->string('slug', 2048);
            $table->string('logo', 2048)->nullable();
            $table->longText('body')->nullable();
            $table->boolean('active')->default(false);
            $table->datetime('published_at');
            $table->foreignIdFor(\App\Models\User::class, 'user_id');
            $table->timestamps();
        });

        Schema::create('project_article_attachment', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_article_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('attachment_id')->nullable()->constrained()->onDelete('cascade');
        });

        Schema::create('project_article_photo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_article_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('photo_id')->nullable()->constrained()->onDelete('cascade');
        });

        Schema::create('project_type_project_article', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_type_id')->nullable()->references('id')->on('project_types')->onDelete('cascade');
            $table->foreignId('project_article_id')->nullable()->references('id')->on('project_articles')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_types');
        Schema::dropIfExists('project_articles');
        Schema::dropIfExists('project_article_attachment');
        Schema::dropIfExists('project_article_photo');
        Schema::dropIfExists('project_type_project_article');
    }
};
