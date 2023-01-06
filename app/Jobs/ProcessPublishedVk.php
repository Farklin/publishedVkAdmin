<?php

namespace App\Jobs;

use App\Models\Hashtag;
use App\Models\Post;
use App\Models\VkPost;
use App\Services\Vk\VkApi;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessPublishedVk implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $post; 
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Post $post)
    {
        $this->post = $post;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $images = []; 
        // если есть картинка скачиваем     

        if(!empty($this->post->media))
        {
            foreach($this->post->media as $media)
            {
                // если изображение
                if($media->format == 'image')
                {
                    if(!empty($media->url)) 
                    {
                        $url = $media->url;
                        $img = explode('/', $media->url);
                        $img = public_path() . '/images/' . $img[count($img) - 1]; 
                        $images[] = $img; 
                        // скачиваем картинку 
                        file_put_contents($img, file_get_contents($url));
                    }
                }
            }
        }

        $vkApi = new VkApi();

        // найти хештеги
        $hashtags = []; 

        foreach(Hashtag::where('active', 1)->get() as $itemHashtag)
        {
            foreach(explode(',', $itemHashtag->words) as $hashtag)
            {
                $searchHashtagStatus = strpos($this->post->description, $hashtag);
                if($searchHashtagStatus)
                {  
                    if(!empty($itemHashtag->hashtag))
                    {
                        $hashtags[] = $itemHashtag->hashtag;
                    }
                    break; 
                }
            }
        }

        $message = $this->post->description . "\n\n" . implode(' ', $hashtags); 
        
        // публикуем пост 
        //$postVk = new PostVk(); 
       
        
        $vk_post_id = $vkApi->publishedPost($message, $images)['response']['post_id'];
        
        // изменяем статус публикации в базе данных 
        $this->post->status = 1;
        $this->post->save();  

        $this->post->vk()->create([
            'post_id' => $this->post->id, 
            'vk_post_id' => $vk_post_id,
        ]);

        if(!empty($images))
        {   
            foreach($images as $img)
            {
                unlink($img);
            }
        }
    }
}
