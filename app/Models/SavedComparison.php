<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SavedComparison extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'property_ids',
        'name'
    ];

    protected $casts = [
        'property_ids' => 'array'
    ];

    protected $table = 'saved_comparisons';

    public function user()
    {
        return $this->belongsTo(User::class);
    }
} 