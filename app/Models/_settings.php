<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class _settings extends Model
{
    protected $table = '_settings';
    protected $primaryKey = 'SET_ID';

    protected $fillable = [
        'SET_ID',
        'SET_VALUE',
        'SET_VALUE_TEXT',
        'SET_INFO',
        'SET_DISPLAY_FORM',
        'SET_VALUE_DISPLAY_FORM',
    ];
    public $timestamps = false;
}
