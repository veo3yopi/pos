<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id',
        'size_id',
        'color_id',
        'sku',
        'price',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function size(): BelongsTo
    {
        return $this->belongsTo(Size::class);
    }

    public function color(): BelongsTo
    {
        return $this->belongsTo(Color::class);
    }

    public function stock(): HasOne
    {
        return $this->hasOne(Stock::class, 'variant_id');
    }

    public function stockLogs(): HasMany
    {
        return $this->hasMany(StockLog::class, 'variant_id');
    }

    public function transactionItems(): HasMany
    {
        return $this->hasMany(TransactionItem::class, 'variant_id');
    }
}
