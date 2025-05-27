<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InstitutionContact extends Model
{
    //
    protected $fillable = ['informacion','contact_id','institution_id'];

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

}
