<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Participant extends Model
{
    // avoiding issue with MassAssignmentException
    protected $fillable = array('name', 'service_id', 'count_adults', 'count_children', 'tenant_id');
}
