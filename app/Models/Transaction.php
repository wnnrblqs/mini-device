<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Device;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_id',
        'transaction_details',
    ];

    public function device()
    {
        return $this->belongsTo(Device::class);
    }
}
