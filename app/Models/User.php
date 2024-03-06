<?php

namespace App\Models;

use App\Traits\Filterable;
use App\Traits\Searchable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Foundation\Testing\Concerns\InteractsWithAuthentication;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Image\Manipulations;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    use Searchable, Filterable;

    /**
     * ALlowed search fields
     * @var string[]
     */
    protected $searchFields = ['first_name', 'last_name', 'middle_name', 'email'];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array<string>|bool
     */
    protected $guarded = ['id'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Bootstrap the model and its traits.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();
    }

    /**
     * Returns the full_name attribute
     * @return string
     */
    public function getFullNameAttribute()
    {
        $names = [];
        foreach (['first_name', 'middle_name', 'last_name'] as $key) {
            $value = $this->getAttribute($key);
            if ( ! empty($value)) {
                $names[] = $value;
            }
        }

        return implode(' ', $names);
    }

}
