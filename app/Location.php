<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $table = 'locations';
    protected $guarded = [];

    public function schedule()
    {
        return $this->hasMany(Schedule::class);
    }
}