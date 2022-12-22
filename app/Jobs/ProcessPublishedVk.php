<?php

namespace App\Jobs;

use App\Models\Hashtag;
use App\Models\Post;
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
        $img = null; 
        // если есть картинка скачиваем     
        if(!empty($this->post ->image))
        {   // получаем картинку 
            $url = $this->post->image;
            
            $img = explode('/', $this->post->image);

            $img = public_path() . '/images/' . $img[count($img) - 1]; 
            // скачиваем картинку 
            file_put_contents($img, file_get_contents($url));
        }
        
        $vkApi = new VkApi();

        // найти хештеги
        $hashtags = []; 

        foreach(Hashtag::where('status', 1)->get() as $itemHashtag)
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
            
        $vkApi->publishedPost($message, $img); 
        // изменяем статус публикации в базе данных 
        $this->post->status = true;
        
        $this->post->save();  
        if(!empty($img))
        {   
            // удаляем картинку 
            unlink($img); 
        }
    }
}
