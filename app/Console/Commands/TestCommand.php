<?php

namespace App\Console\Commands;

use App\Jobs\Telegram\ProcessTelegramPostModeraion;
use App\Models\Post;
use App\Services\Vk\VkApi;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class TestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {   
        // $vk = new VkApi(); 
        // $responce = $vk->loadVideo(public_path() . '/videos/1_5327984768380314343.mp4'); 
        // Log::info($responce); 
        $post = Post::find(157);
        ProcessTelegramPostModeraion::dispatch($post);
        return Command::SUCCESS;
    }
}
