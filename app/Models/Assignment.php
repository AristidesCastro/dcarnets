<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    //
    protected $fillable = [
        'categoria',
        'dependencia',
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
