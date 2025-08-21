<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Morpromt extends Model
{
    protected $connection =  'mysql';
    protected $table = 'morpromt';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'token', 'ttl','created_at','updated_at'
    ];

}
