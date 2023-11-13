<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Photo extends Model
{
    use HasFactory;


    protected $fillable = [
        'title',
        'descr',
        'user_id',
        'src_small',
        'src_big',
    ];


     /**
     * @return HasMany
     */
    public function photos(): HasMany
    {
        return $this->hasMany(PhotoItem::class);
    }

     /**
     * Пользователь создавший альбом
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
