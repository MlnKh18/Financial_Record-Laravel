<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Transaction extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'category_id',
        'source_id',
        'type',
        'amount',
        'description',
        'transaction_date',

    ];


    protected $casts = [
        'transaction_date' => 'date',
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
