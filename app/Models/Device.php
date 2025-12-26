<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_type',
        'device_status',
        'device_last_activity',
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
