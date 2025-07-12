<?php

namespace App\Models;

use App\Models\User;
use App\Models\Store;
use App\Enums\StockTransferStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StockTransfer extends Model
{
    use HasFactory;
    
    protected $fillable = ['company_id', 'from_store_id', 'to_store_id', 'user_id', 'transfer_date', 'status', 'notes'];

    protected $casts = ['status' => StockTransferStatus::class, 'transfer_date' => 'date'];

    public function fromStore() 
    { 
        return $this->belongsTo(Store::class, 'from_store_id'); 
    }

    public function toStore() 
    { 
        return $this->belongsTo(Store::class, 'to_store_id'); 
    }

    public function user() 
    { 
        return $this->belongsTo(User::class); 
    }
    
    public function items() 
    { 
        return $this->hasMany(StockTransferItem::class); 
    }
}
