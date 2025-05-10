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
        Schema::create('diets', function (Blueprint $table) {
            $table->id();
            $table->string('name', 2048);
            $table->string('breakfast_photo', 2048)->nullable();
            $table->string('lunch_photo', 2048)->nullable();
            $table->longText('breakfast_body')->nullable();
            $table->longText('lunch_body')->nullable();
            $table->string('diet_attachment', 2048)->nullable();
            $table->boolean('active')->default(false);
            $table->datetime('published_at');
            $table->foreignIdFor(\App\Models\User::class, 'user_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diets');
    }
};
