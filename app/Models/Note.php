<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Note extends Model
{
    use HasFactory;
    protected $fillable = [
        'path_logo',
        'name',
        'descr',
        'post_md',
        'post_js',
    ];

    public static function boot(){
        static::creating(function($model){
            $model->slug = Str::slug($model->name, '-');
        });
    }

    protected $casts = [
        'path_images' => 'array',
    ];

    /** получить связанные модели на уровень ниже
     * для получения всех моделей уровня требуется вызвать NoteWhithChildren
     * например для построения BreadCrumbs найденых моделей
     * @return HasMany
     */
    public function notes(): HasMany
    {
        return $this->hasMany(Note::class, 'parent_id', 'id');
    }

    /**
     * Пользователь создавший заметку
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
