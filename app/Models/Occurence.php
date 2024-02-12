<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Occurence extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'compartment_id', 'security_id',
        'occurence', 'status', 'department','first_aid_item','occurence_scene',
        'om_comment', 'hod_comment', 'md_comment','evidence','type','pushed_to',
    ];

    protected $casts = [
        'evidence' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reporter()
    {
        return $this->belongsTo(User::class, 'security_id');
    }

    public function compartment()
    {
        return $this->belongsTo(Compartment::class,'compartment_id');
    }

    public function kits()
    {
        return $this->belongsToMany(FirstAid::class);
    }
}
