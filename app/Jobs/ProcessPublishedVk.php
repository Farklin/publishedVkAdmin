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
     * Процесс публикации вконтакте 
     *
     * @return void
     */
    public function handle()
    {
        $images = []; 
        $videos = []; 
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

                // если видео 
                if($media->format == 'video')
                {
                    if(!empty($media->path)) 
                    {
                        $videos[] = $media->path; 
                    }
                }
            }
        }

        $vkApi = new VkApi();

        // найти хештеги
        $hashtags = $this->searhHashtag($this->post);  
        $message = $this->post->description . "\n\n" . implode(' ', $hashtags); 
        
        // публикуем пост 
        //$postVk = new PostVk(); 
       
        
        $vk_post_id = $vkApi->publishedPost($message, $images, $videos)['response']['post_id'];
        
        // изменяем статус публикации в базе данных 
        $this->editSatatusPost($this->post, $vk_post_id); 

        $this->deleteMediaFile($images); 
        $this->deleteMediaFile($videos); 

    }

    public function editSatatusPost(Post $post, $vk_post_id)
    {
        $this->post->status = 1;
        $this->post->save();  

        $this->post->vk()->create([
            'post_id' => $this->post->id, 
            'vk_post_id' => $vk_post_id,
        ]);
    }
    
    /**
     * Найти хештеги публикации 
     *
     * @param Post $post
     * @return array hashtags
     */
    public function searhHashtag(Post $post)
    {
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

        return $hashtags; 

    }
    /**
     * Удалить из локальной папки изображения 
     *
     * @param array $images
     * @return void
     */
    public function deleteMediaFile(array $files)
    {
        if(!empty($files))
        {   
            foreach($files as $img)
            {
                unlink($img);
            }
        }
    }
}
