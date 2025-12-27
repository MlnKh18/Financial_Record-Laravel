<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Transaction extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'type',        // income | expense
        'amount',
        'description',
        'category_id',
        'source_id',
        'user_id',
        'approved_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'approved_at' => 'datetime',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function source()
    {
        return $this->belongsTo(Source::class);
    }
}
