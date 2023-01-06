<?php

namespace App\Jobs;

use App\Models\ParsingWord;
use App\Models\Post;
use App\Services\Parsing\Sites\Site;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessParsingSite implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $site;
    protected $href;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($href, $site)
    {
        $this->site = $site;
        $this->href = $href;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (!Post::where('url', $this->href)->exists()) {
            // Log::info('Начат парсинг сайта ' . $this->href);
            $parsingSite = new Site();
            $parsingSite->getPageContent($this->href);

            $title = $parsingSite->getCustom($this->site->title, '');
            // Log::info('Заголовок' .  $title );

            $description = $parsingSite->getCustom($this->site->description);
            // Log::info('Описание' .  $description );

            $image = $parsingSite->getCustom($this->site->image);
            $images[] = $image; 
            // Log::info('Изображение' .  $image );

            $date =  $parsingSite->getCustom($this->site->date);
            // Log::info('Дата' .  $date );

            foreach (ParsingWord::pluck('word')->toArray() as $word) {
                if (strpos($description, $word) !== false) {
                    if (!Post::where('url', $parsingSite->url)->exists()) {
                        $post = new Post();
                        $post->title = $title;
                        $post->description = $description;
                        $post->image = $image;
                        $post->url = $parsingSite->url;
                        $post->status = 0;
                        $post->date = $date;
                        $post->save();
                        
                        foreach($images as $img)
                        {
                            $post->media()->create(
                                ['url' => $img, 'format' => 'image']
                            );
                        }
            
                    }
                }
            }
        }
    }
}
