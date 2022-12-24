<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VkPost extends Model
{
    use HasFactory;

    protected $fillable = ['post_id','vk_post_id', 'likes', 'reposts'];

}
