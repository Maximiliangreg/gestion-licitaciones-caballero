<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['title', 'description', 'client_id', 'max_budget', 'total_amount', 'status', 'delivery_deadline', 'created_by', 'updated_by'])]
class Tender extends Model
{
    use HasFactory;

    // Una licitación pertenece a un único cliente externo (RN-06)
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    // Una licitación tiene muchos productos asociados (Muchos a Muchos con pivote)
    public function products()
    {
        return $this->belongsToMany(Product::class, 'tender_products')
                    ->withPivot('quantity', 'unit_price', 'added_by', 'updated_by')
                    ->withTimestamps();
    }

    // Quién creó la licitación originalmente
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}