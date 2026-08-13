<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Institution extends Model
{
    protected $fillable = [
        'name',
        'acronym',
        'country_code',
        'city',
        'website',
        'ror_id',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
