<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillDetail extends Model
{
    use HasFactory;

    protected $table = 'bill_details';

    protected $fillable = [
        'bill_id', 'item_name', 'quantity', 'price', 'sub_total',
    ];

    protected $hidden = [
        'created_at', 'updated_at',
    ];

    public function bill() {
        return $this->belongsTo(Bill::class);
    }
}
