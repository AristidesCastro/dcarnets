<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Period extends Model
{
    //
    protected $fillable = [
        'periodo',
        'fecha_inicio',
        'fecha_fin',
        'activo',
        'actual',
        'institution_id'
    ];

    public function institution()
    {
        return $this->hasMany(Institution::class, 'id', 'institution_id');
    }
}
