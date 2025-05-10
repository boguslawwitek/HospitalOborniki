<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Location;
use App\Models\Price;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Location::firstOrCreate(['photo_id' => null]);
        Price::firstOrCreate(['attachment_id' => null]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Location::where('photo_id', null)->delete();
        Price::where('attachment_id', null)->delete();
    }
};
