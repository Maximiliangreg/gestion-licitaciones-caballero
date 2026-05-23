<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['sku', 'name', 'unit_price', 'stock', 'created_by', 'updated_by'])]
class Product extends Model
{
    use HasFactory;

    // Un producto puede estar en muchas licitaciones (Muchos a Muchos)
    public function tenders()
    {
        return $this->belongsToMany(Tender::class, 'tender_products')
                    ->withPivot('quantity', 'unit_price', 'added_by', 'updated_by')
                    ->withTimestamps();
    }
}