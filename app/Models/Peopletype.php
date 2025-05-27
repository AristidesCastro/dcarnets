<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Peopletype extends Model
{
    //
    protected $fillable = [
        'nombre',
        'group_id',
        'institution_id'
    ];

    public function institution()
    {
        return $this->hasMany(Institution::class, 'id', 'institution_id');
    }
    public function group()
    {
        return $this->hasMany(Group::class, 'id', 'group_id');
    }
}
