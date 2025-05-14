<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Security extends Authenticatable
{
    use Notifiable;
    protected $table = 'securitys';
    protected $guarded = [];
    protected $fillable = [
        'name', 'email', 'password', 'phone_number', 'address', 'status', 'activate_token'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    //mutator
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }
    
    public function schedule()
    {
        return $this->hasMany(Schedule::class, 'schedule_id');
    }
}
