<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Developers extends Model
{
    use HasFactory;

    protected $fillable = [
        "datanascimento",
        "sexo",
        "nome",
        "hobby_id"
    ];

    public function setIdadeAttribute()
    {
        $this->attributes['idade'] =  \Carbon\Carbon::parse($this->attributes['datanascimento'])->age;
    }

    public function hobby(){
        return $this->belongsTo(Hobbies::class);
    }
}
