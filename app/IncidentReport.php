<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IncidentReport extends Model
{
    protected $table = 'incident_reports';
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
