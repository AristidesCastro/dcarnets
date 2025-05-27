<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cardselement extends Model
{
    //
    protected $fillable = [
        'informacion',
        'posicion_X',
        'posicion_Y',
        'tamano_W',
        'tamano_H',
        'visible',
        'elementstype_id',
        'cardsdesign_id'
    ];

    protected $casts = [
        'posicion_X' => 'integer',
        'posicion_Y' => 'integer',
        'tamano_W' => 'integer',
        'tamano_H' => 'integer',
        'visible' => 'boolean',
    ];

    public function elementstype(): BelongsTo
    {
        return $this->belongsTo(Elementstype::class, 'elementstype_id');
    }

    public function cardsdesign(): BelongsTo
    {
        return $this->belongsTo(Cardsdesign::class, 'cardsdesign_id');
    }
}
