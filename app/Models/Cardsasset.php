<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Importar

class Cardsasset extends Model
{
    //
    protected $fillable = [
        'path_archivo',
        'institution_id'
    ];

     public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class, 'institution_id'); // Clave foránea es 'institution_id'
    }

}
