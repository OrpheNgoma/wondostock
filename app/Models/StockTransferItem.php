<?php

namespace App\Models;

use App\Models\Product;
use App\Models\StockTransfer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StockTransferItem extends Model
{
    use HasFactory;
    
    public $timestamps = false;

    protected $fillable = ['stock_transfer_id', 'product_id', 'quantity'];

    public function transfer() 
    { 
        return $this->belongsTo(StockTransfer::class); 
    }

    public function product() 
    { 
        return $this->belongsTo(Product::class); 
    }
}
