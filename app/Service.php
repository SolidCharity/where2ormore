<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Service extends Model
{
    protected $appends = ['count_adults', 'count_children'];

    public function getCountAdultsAttribute()
    {
        return DB::table('participants')
                ->where('service_id', $this->id)
                ->sum('count_adults');
    }

    public function getCountChildrenAttribute()
    {
        return DB::table('participants')
                ->where('service_id', $this->id)
                ->sum('count_children');
    }
}
