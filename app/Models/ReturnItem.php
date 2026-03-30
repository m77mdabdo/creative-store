<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Auditable;

class ReturnItem extends Model
{
    use HasFactory , Auditable ;

    protected $table = 'return_items';

    protected $fillable = [
        'return_id',
        'order_item_id',
        'quantity',
        'condition_note',
        'refund_amount',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'refund_amount' => 'decimal:2',
    ];

    public function returnRequest()
    {
        return $this->belongsTo(ReturnRequest::class, 'return_id');
    }

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class, 'order_item_id');
    }
}
