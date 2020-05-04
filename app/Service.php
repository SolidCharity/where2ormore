<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Auth;

class Service extends Model
{
    protected $appends = ['count_adults', 'count_children'];

    // avoiding issue with MassAssignmentException
    protected $fillable = array('description', 'tenant_id');

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
