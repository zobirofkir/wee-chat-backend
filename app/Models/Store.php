<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Store extends Model
{
    use HasFactory;

    /**
     * Fillable fields
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'name',
        'domain',
    ];

    /**
     * Get the user that owns the store
     *
     * @return BelongsTo
     */
    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
