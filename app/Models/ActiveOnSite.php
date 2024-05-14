<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActiveOnSite extends Model
{
    public $table = 'active_on_site';

    protected $fillable = [
        'plugin',
        'url'
    ];
}
