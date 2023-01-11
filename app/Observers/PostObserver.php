<?php

namespace App\Observers;

use App\Jobs\ProcessPublishedVk;
use App\Jobs\Telegram\ProcessTelegramPostModeraion;
use App\Models\Post;
use Telegram\Bot\Laravel\Facades\Telegram;

/***
 * Обработчик событий модели пост
 */
class PostObserver
{
    /**
     * Handle the Post "created" event.
     *
     * @param  \App\Models\Post  $post
     * @return void
     */
    public function created(Post $post)
    {   
        // переслать сообщение в телеграм с кнопками опубликовать / не публиковать 
        ProcessTelegramPostModeraion::dispatch($post); 
    }

    /**
     * Handle the Post "updated" event.
     *
     * @param  \App\Models\Post  $post
     * @return void
     */
    public function updated(Post $post)
    {   
        // при обновлении поставить в очередь на публикацию 
        if($post->moderation == 'published' and $post->status == 0)
        {
            ProcessPublishedVk::dispatch($post);
        }
    }

    /**
     * Handle the Post "deleted" event.
     *
     * @param  \App\Models\Post  $post
     * @return void
     */
    public function deleted(Post $post)
    {
        //
    }

    /**
     * Handle the Post "restored" event.
     *
     * @param  \App\Models\Post  $post
     * @return void
     */
    public function restored(Post $post)
    {
        //
    }

    /**
     * Handle the Post "force deleted" event.
     *
     * @param  \App\Models\Post  $post
     * @return void
     */
    public function forceDeleted(Post $post)
    {
        //
    }
}
