<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Homepage;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Homepage::firstOrCreate(['title' => 'Strona główna', 'photo' => null, 'content' => null]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Homepage::where('title', 'Strona główna')->delete();
    }
};
