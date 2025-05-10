<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JobOffersAttachment extends Model
{
    protected $table = 'job_offers_attachment';

    protected $fillable = [
        'job_offers_id',
        'attachment_id',
    ];

    use HasFactory;
}
