<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class User extends Authenticatable
{   
    use HasFactory;
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = ['name', 'email', 'password', 'token'];
    protected $hidden = ['password'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    public function places()
    {
        return $this->hasMany(Place::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function dislikes()
    {
        return $this->hasMany(Dislike::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function rating()
    {
        return $this->hasMany(Rating::class);
    }

    
    /**
     * Get the unique identifier for the user.
     */
    public function getAuthIdentifier() {
        return $this->name;
    }
    
    /**
     * Get the name of the unique identifier for the user.
     * @return string
     */
    public function getAuthIdentifierName() {
        return 'name';
    }
    
    /**
     * Get the password for the user.
     * @return string
     */
    public function getAuthPassword() {
        return $this->password;
    }
    
    /**
     * Get the token value for the "remember me" session.
     * @return string
     */
    public function getRememberToken() {
        return $this->token;
    }
    
    /**
     * Get the column name for the "remember me" token.
     * @return string
     */
    public function getRememberTokenName() {
        return 'token';
    }
    
    /**
     * Set the token value for the "remember me" session.
     *
     * @param string $value
     * @return void
     */
    public function setRememberToken($value) {
        $this->token = $value;
    }
}
