<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $table = 'schedules';
    protected $fillable = [
        'security_id',
        'shift_date',
        'start_time',
        'end_time',
    ];
    protected $guarded = [];

    // Relasi ke tabel 'securities' (jika belum ada)
    public function security()
    {
        return $this->belongsTo(Security::class);
    }

    public function absence()
    {
        return $this->hasMany(Absence::class, 'schedule_id');
    }

    public function incident()
    {
        return $this->hasMany(IncidentReport::class, 'schedule_id');
    }
}
