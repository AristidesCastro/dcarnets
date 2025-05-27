<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\belongsToMany;

class Contact extends Model
{
    //
    protected $fillable = ['nombre','icono','tipo'];

    public function institutionsContacts()
    {
        return $this->belongsToMany(InstitutionContact::class);
    }
}
