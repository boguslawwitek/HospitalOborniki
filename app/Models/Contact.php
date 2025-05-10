<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $fillable = ['system_email', 'telephone', 'email', 'address', 'fax'];
}
