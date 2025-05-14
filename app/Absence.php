<?php

namespace App;
use Illuminate\Support\Str;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Absence extends Model
{
    protected $table = 'absences';
    protected $guarded = [];

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    public function location()
    {
        return $this->belongsTo(location::class, 'location_point_id');
    }
}