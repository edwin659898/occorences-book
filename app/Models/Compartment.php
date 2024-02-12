<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Compartment extends Model
{
    use HasFactory;

    protected $fillable = ['compartment_name', 'site'];

    public function occurences()
    {
        return $this->hasMany(Occurence::class);
    }
}
