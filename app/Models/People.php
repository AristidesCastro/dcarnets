<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class People extends Model
{
    //
    protected $fillable = [
        'nombres',
        'apellidos',
        'cedula',
        'fecha_nacimiento',
        'institution_id',
        'peopletype_id'
    ];

    public function institution()
    {
        return $this->hasMany(Institution::class, 'id', 'institution_id');
    }
    public function peopletype()
    {
        return $this->hasMany(peopletype::class, 'id', 'peopletype_id');
    }
}

