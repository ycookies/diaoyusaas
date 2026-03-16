<?php

namespace App\Models;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;


class PortalUser extends Authenticatable implements JWTSubject
{
    protected $connection = 'hotel';
    protected $table = 'user';
    //public $timestamps = false;
    public $guarded = [];

    /*protected $hidden = [
        'password', 'remember_token',
    ];*/
    protected $hidden = [
        'password', 'remember_token','api_token','updated_at','session_key',
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

    /**
     * Get the identifier that will be stored in the subject claim of the JWT
     *
     * @return mixed
     */
    /*public function getJWTIdentifier()
    {
        return $this->getUserId();
    }*/

    public function getUserId()
    {

        return [
            'id'=>$this->id,
            'name'=>$this->name,
            'phone'=>$this->phone,
            'type'=>'merchant',
            'merchant_type'=>$this->type,
            'pid'=>$this->pid,
            'imei'=>$this->imei
        ];//返回用户id
    }

}
