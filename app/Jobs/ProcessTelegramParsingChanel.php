<?php

namespace App\Jobs;

use App\Models\ParsingWord;
use App\Models\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessTelegramParsingChanel implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $chanel;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($chanel,)
    {
        $this->chanel = $chanel;  
    }

    /**
     * Процесс парсинга сообщений из телегрмма
     *
     * @return void
     */
    public function handle()
    {   
        $MadelineProto = new \danog\MadelineProto\API('session.madeline');
        $settings = array(
            'peer' => $this->chanel, //название_канала, должно начинаться с @, например @breakingmash
            'offset_date' =>  0,
            'add_offset' => 0,
            'limit' => 20, //Количество постов, которые вернет клиент
            'max_id' =>  0, //Максимальный id поста
            'min_id' =>  0, //Минимальный id поста - использую для пагинации, при  0 возвращаются последние посты.
            //'hash' => []
        );

        $data = $MadelineProto->messages->getHistory($settings);
        // перебор сообщений телеграмм 
        foreach ($data['messages'] as $message) {
            // сравнение сообщения с ключивыми словами
            foreach (ParsingWord::pluck('word')->toArray() as $word) {
                // если ключевое слово найдено 
                if (strpos($message['message'], $word) !== false) {

                    $title = mb_substr($message['message'], 0, 60);

                    if (!Post::where('title', $title)->exists()) {
                        // создание нового поста 
                        $post = new Post();
                        $post->url = $this->chanel;
                        $post->title = $title;
                        $post->description =  $message['message'];
                        $post->status = 0;
                        $post->save();
                        // если в записи есть media контент 
                        if (isset($message['media'])) {
                            // скачиваем media
                            $media = $MadelineProto->download_to_dir($message,  public_path() . '/videos');
                            $images[] = $media;
                            // перебираем media
                            Log::info($images); 
                            foreach ($images as $img) {
                                if (isset($img)) {
                                    // получаем тип файла 
                                    $mime = mime_content_type($img);
                                    if (strstr($mime, "video/")) {
                                        $filetype = "video";
                                    } else if (strstr($mime, "image/")) {
                                        $filetype = "image";
                                    }
                                    
                                    // добавляем к записи media контент
                                    $post->media()->create(
                                        ['path' => $img, 'format' => $filetype]
                                    );
                                }
                            }
                            //TODO: добавить задачу на публикацию новости в вк
                            
                            //Log::info($message['media']); 
                            ///$output_file_name = $MadelineProto->download_to_dir($message,  public_path() . '/videos');

                        }
                    }

                    break;
                }
            }
        }
    }
}
