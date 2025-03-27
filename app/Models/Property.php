<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'price',
        'category_id',
        'address',
        'bedrooms',
        'bathrooms',
        'area',
        'is_featured'
    ];

    protected $casts = [
        'images' => 'array',
        'amenities' => 'array',
        'price' => 'float',
        'area' => 'float',
        'is_featured' => 'boolean'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
