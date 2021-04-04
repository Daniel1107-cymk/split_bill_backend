<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    use HasFactory;

    protected $table = 'bills';

    protected $fillable = [
        'user_id', 'code', 'date', 'total_people', 'splitted_value', 'grand_total',
    ];

    protected $hidden = [
        'created_at', 'updated_at',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function billDetails() {
        return $this->hasMany(BillDetail::class);
    }
}
