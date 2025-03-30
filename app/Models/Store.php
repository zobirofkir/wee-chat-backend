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
        'theme',
        'theme_applied_at',
        'theme_data',
        'theme_storage_path'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'theme_applied_at' => 'datetime',
        'theme_data' => 'array',
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
