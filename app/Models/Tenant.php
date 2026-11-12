<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tenant extends Model
{
    protected $fillable = ['user_id', 'room_id', 'move_in_date', 'move_out_date'];

    protected function casts(): array
    {
        return [
            'move_in_date' => 'date',
            'move_out_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function bills(): HasMany
    {
        return $this->hasMany(Bill::class);
    }
}
