<?php

namespace App\Models;

use App\Models\Types\ProductType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'type',
        'description',
        'photo_url',
        'price',
        'custom',
    ];

    /**
     * Type selection
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param string $type
     * @return void
     */
    public function scopeOfType(Builder $builder, mixed $type)
    {
        if ($type && in_array($type, ProductType::toArray())) {
            $builder = $builder->where('type', $type);
        }
    }
}
