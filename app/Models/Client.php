<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['name', 'email', 'phone', 'address', 'created_by', 'updated_by'])]
class Client extends Model
{
    use HasFactory;

    // Un cliente tiene muchas licitaciones asociadas
    public function tenders()
    {
        return $this->hasMany(Tender::class);
    }

    // Usuario que registró al cliente (Auditoría)
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}