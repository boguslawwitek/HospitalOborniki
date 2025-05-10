<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JobOffersPhoto extends Model
{
    protected $table = 'job_offers_photo';

    protected $fillable = [
        'job_offers_id',
        'photo_id',
    ];

    use HasFactory;
}
