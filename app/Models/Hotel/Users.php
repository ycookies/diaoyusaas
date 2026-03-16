<?php

namespace App\Models\Hotel;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;


class Users extends Authenticatable implements JWTSubject
{
    protected $connection = 'hotel';
    protected $table = 'hotel_users';
    //public $timestamps = false;
    public $guarded = [];

    protected $hidden = [
        'password', 'remember_token','api_token',
    ];

    use Notifiable;

    // Rest omitted for brevity

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

}
