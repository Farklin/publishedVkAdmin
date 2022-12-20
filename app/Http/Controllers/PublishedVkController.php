<?php

namespace App\Http\Controllers;

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
            $img = null; 

            if(!empty($post->image))
            {
                $url = $post->image;
                $img = explode('/', $post->image);
                $img = $_SERVER['DOCUMENT_ROOT'] . '/images/' . $img[count($img) - 1]; 
                file_put_contents($img, file_get_contents($url));
            }

            $vkApi = new VkApi();
            $vkApi->publishedPost($post->description, $img); 
            $post->status = true;
            sleep(10);
            $post->save();  
            if(!empty($img))
            {
                unlink($img); 
            }
            


        }
    }
}
