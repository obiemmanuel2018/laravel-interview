<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Support\Facades\Storage;


class User extends Authenticatable implements JWTSubject
{
    use Notifiable;
    public $timestamps = true;
     /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'user_name',
        'avatar',
        'email',
        'registered_at',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    public function link_profile($avatar){
      $uri = env('APP_URL');
      $destination_path = 'public/images/profile';
      $avatar_name = $avatar->getClientOriginalName();
      $path = $uri .'/'. $avatar->storeAs($destination_path,$avatar_name);
      $this->avatar = $path;
    }


    public function unlink_profile(){
   
        $array = explode("public",$this->avatar);
      
        $destination_path = 'public/'.$array[1];
      
        Storage::delete($destination_path);
        $this->avatar ="";
   
   
}


    public function createSuperAdmin(array $details) : self
    {
        $user = new self();
        $user->email = $details['email'];
        $user->user_name = $details['user_name'];
        $user->user_role = 'admin';
        $user->password = bcrypt($details['password']);
        $user->save();
        return $user;
    }



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
