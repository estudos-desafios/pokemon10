<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pokemon extends Model
{
    protected $table = 'pokemons';
    
    use HasFactory;

    protected $fillable = [
        'id',
        'name',
        'type',
        'weight',
        'height',
        'updated_at'
    ];


}
