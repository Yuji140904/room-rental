<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    protected $fillable = ['room_number', 'default_rent', 'status'];

    public function tenants(): HasMany
    {
        return $this->hasMany(Tenant::class);
    }

    public function bills(): HasMany
    {
        return $this->hasMany(Bill::class);
    }
}
