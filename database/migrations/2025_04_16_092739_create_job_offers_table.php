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
        Schema::create('job_offers', function (Blueprint $table) {
            $table->id();
            $table->string('title', 2048);
            $table->string('slug', 2048);
            $table->longText('body')->nullable();
            $table->boolean('active')->default(false);
            $table->datetime('published_at')->nullable();
            $table->foreignIdFor(\App\Models\User::class, 'user_id');
            $table->timestamps();
        });

        Schema::create('job_offers_attachment', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_offers_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('attachment_id')->nullable()->constrained()->onDelete('cascade');
        });

        Schema::create('job_offers_photo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_offers_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('photo_id')->nullable()->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_offers');
        Schema::dropIfExists('job_offers_attachment');
        Schema::dropIfExists('job_offers_photo');
    }
};
