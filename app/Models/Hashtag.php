<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hashtag extends Model
{
    use HasFactory;
    protected $connection='sqlite_publishedVk'; 
    protected $table = 'hashtags';
    public $timestamps = false;

}
