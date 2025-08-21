<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pet extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'breed', 'age', 'owner_id'];

    public $timestamps = false;

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'pet_id');
    }
}
