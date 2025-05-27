<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Institution extends Model
{
    //
    protected $fillable = ['nombre','logo','user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function institutionsContacts()
    {
        return $this->hasMany(InstitutionContact::class, 'institution_id', 'id');
    }



}
