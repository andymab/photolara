<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PhotoItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'descr',
        'photo_id',
        'src_small',
        'src_big',
    ];




     /**
     * Пользователь создавший альбом
     *
     * @return BelongsTo
     */
    public function photo(): BelongsTo
    {
        return $this->belongsTo(Photo::class);
    }
}
