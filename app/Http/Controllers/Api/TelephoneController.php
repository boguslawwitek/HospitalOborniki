<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Telephone;
use Illuminate\Http\Request;

class TelephoneController extends Controller
{
    public function index()
    {
        $telephones = Telephone::orderBy('sort_order', 'asc')->get();
        
        return response()->json([
            'telephones' => $telephones,
        ]);
    }
}
