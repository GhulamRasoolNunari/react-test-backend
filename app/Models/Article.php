<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    public $table = 'news';

    protected $guarded = [];

    public $hidden = [
        'created_at',
        'updated_at',
    ];
}
