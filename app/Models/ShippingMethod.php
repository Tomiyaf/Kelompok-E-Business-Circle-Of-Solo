<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'cost'])]
class ShippingMethod extends Model
{
    use HasFactory;

    protected $table = 'shipping_methods';

    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'cost' => 'decimal:2',
        ];
    }
}
