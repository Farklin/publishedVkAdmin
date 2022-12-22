<?php

namespace App\Jobs;

use App\Models\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

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
        $this->site->getPageContent($this->href->href);
        $this->site->getTitle(); 
        $this->site->getDescription(); 
        $this->site->getImage(); 

        foreach($this->site->words as $word)
        {
            if(strpos($this->site->description, $word) !== false)
            {
                if(!Post::where('url', $this->site->url)->exists())
                {
                    $masResult[] = $this->site->url; 
                    $post = new Post();
                    $post->title = $this->site->title;
                    $post->description = $this->site->description;
                    $post->image = $this->site->image;
                    $post->url = $this->site->url;
                    $post->status = 0;
                    $post->date = $this->site->date; 
                    $post->save(); 
                }
            }
        }
    }
}
