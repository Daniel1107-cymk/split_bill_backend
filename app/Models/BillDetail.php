<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillDetail extends Model
{
    use HasFactory;

    protected $table = 'bill_details';

    protected $fillabel = [
        'item_name',
        'quantity',
        'price',
        'sub_total',
    ];

    public function bill() {
        return $this->belongsTo(Bill::class);
    }
}
