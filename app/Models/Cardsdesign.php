<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany; // <-- Añadir import

class Cardsdesign extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'orientacion',
        'caras',
        'institution_id',
        'group_id',
        // 'elements', // Esta columna ya no se usa
    ];

    /**
     * Get all of the cardselements for the Cardsdesign
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cardselements(): HasMany // <-- Añadir método de relación
    {
        // Asegúrate de que el namespace y nombre del modelo Cardselement sean correctos
        return $this->hasMany(\App\Models\Cardselement::class);
    }

    // Añade aquí otras relaciones si las tienes (ej. institution, group)
    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }
}
