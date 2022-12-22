<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;
    //protected $connection='sqlite_publishedVk'; 
    protected $table = 'posts';
    //public $timestamps = false;

    public function vk() {
        return $this->belongsTo(PostVk::class); 
    }
}
