<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    use HasFactory;

    protected $table = 'bills';

    protected $fillable = [
        'user_id', 'code', 'date', 'grand_total',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function billDetails() {
        return $this->hasMany(BillDetail::class);
    }
}
