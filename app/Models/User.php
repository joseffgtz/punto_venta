<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'email', 'password', 'role'];

    protected $hidden = ['password'];

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}
