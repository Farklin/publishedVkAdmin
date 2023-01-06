<?php

namespace App\Jobs\Telegram;

use App\Models\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Laravel\Facades\Telegram;

class ProcessTelegramPostModeraion implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    protected $post; 

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
        $inline_keyboard = json_encode([ //Потому что его объект
            'inline_keyboard' => [
                [
                    ['text' => 'Опубликовать', 'callback_data' => 'published|' . $this->post->id],
                    ['text' => 'Не публиковать', 'callback_data' => 'banned|' . $this->post->id]
                ],
            ]
        ]);
        foreach(config('telegram')['admins'] as $admin_id)
        {
            Telegram::sendMessage([
                'chat_id' => $admin_id,
                'text' => $this->post->description,
                'reply_markup' => $inline_keyboard
            ]);
        }
       
    }
}
