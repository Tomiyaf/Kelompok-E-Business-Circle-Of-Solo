<?php

namespace App\Models;

use App\Models\Product;
use App\Models\ProductScent;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name'])]
class Scent extends Model
{
    use HasFactory;

    public $timestamps = false;

    public function productScents(): HasMany
    {
        return $this->hasMany(ProductScent::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_scents')->withPivot('id');
    }
}
