<?php

namespace App\Models;

use App\Traits\Filterable;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{

    use HasFactory;

    use Searchable, Filterable;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array<string>|bool
     */
    protected $guarded = ['id'];

    protected $casts = [
        'scheduled_opening_time' => 'datetime', // Cast to datetime
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
