<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Tenant extends Model
{
    protected $fillable = array('name');

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($tenant) {
            $tenant->uuid = (string) Str::uuid();
        });
    }
}
