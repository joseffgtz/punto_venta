<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'brand',
        'price',
        'stock',
        'image_path',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer',
    ];

    protected $appends = ['image_url'];

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function getImageUrlAttribute(): string
    {
        if (!$this->image_path) {
            return asset('images/no-image.svg');
        }

        return Storage::disk('public')->url($this->image_path);
    }
}
