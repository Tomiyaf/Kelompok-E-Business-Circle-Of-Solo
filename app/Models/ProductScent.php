<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['product_id', 'scent_id'])]
class ProductScent extends Model
{
    use HasFactory;

    protected $table = 'product_scents';

    public $timestamps = false;

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function scent(): BelongsTo
    {
        return $this->belongsTo(Scent::class);
    }
}
