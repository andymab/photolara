<?php

namespace App\Models;

class NoteWithChildren extends Note
{
    protected $table = 'notes';
    protected $with = [
        'notes'
    ];
}
