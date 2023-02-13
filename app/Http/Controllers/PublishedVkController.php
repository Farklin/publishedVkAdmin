<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessPublishedVk;
use App\Models\Post;
use App\Services\Parsing\ParsingService;
use App\Services\Vk\VkApi;
use Illuminate\Http\Request;

class PublishedVkController extends Controller
{
    public function index() 
    {
     

        $posts = Post::where('status', 0)->where('moderation', 'published')->get();
        
        foreach($posts as $post)
        {   
            ProcessPublishedVk::dispatch($post);
        }

    }
}
