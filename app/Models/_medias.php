<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class _medias extends Model
{
    protected $table = '_medias';
    protected $primaryKey = 'MEDIA_ID';

    protected $fillable = [
        'MEDIA_MIME_TYPE',
        'MEDIA_CONTENT_TYPE',
        'MEDIA_CONTENT_VALUE',
    ];

    protected $keyType = 'string';
    public $timestamps = false;
}
