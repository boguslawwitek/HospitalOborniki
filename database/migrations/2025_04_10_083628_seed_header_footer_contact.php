<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Header;
use App\Models\Footer;
use App\Models\Contact;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Header::firstOrCreate(['telephone' => '123123123', 'links' => null, 'logo' => null, 'title1' => null, 'title2' => null, 'subtitle' => null]);
        Footer::firstOrCreate(['wosp_link' => null, 'links' => null, 'registration_hours' => null]);
        Contact::firstOrCreate(['system_email' => 'szpital@przykladowymail.pl', 'telephone' => '123123123', 'email' => 'test@przykladowymail.pl', 'address' => 'ul. Przykladowa 123', 'fax' => '123123123']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Header::where('telephone', '123123123')->delete();
        Footer::where('wosp_link', 'https://wosp.pl')->delete();
        Contact::where('system_email', 'szpital@przykladowymail.pl')->delete();
    }
};
