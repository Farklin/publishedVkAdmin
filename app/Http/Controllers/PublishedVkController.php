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
        $parsing = new ParsingService();
        $parsing->start();

        $posts = Post::where('status', 0)->get();
        
        foreach($posts as $post)
        {   
            ProcessPublishedVk::dispatch($post);
        }

    }
}
