<?php

namespace App\Console\Commands;

use App\Models\ParsingWord;
use App\Models\Post;
use Illuminate\Console\Command;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class StartPythonScript extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'python:start';

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
        // $process = new Process(['python', app_path() . '/PythonScript/parsing_news_telegram_chanels.py'] );
        // $process->run();
        
        // if (!$process->isSuccessful()) {
        //     throw new ProcessFailedException($process);
        // }

        // $data = $process->getOutput();

        // dd($data);
        $path = public_path() . '\telegram_chanels.json'; 
        $messages = json_decode(file_get_contents($path), true);
        
        foreach($messages as $message)
        {
            foreach (ParsingWord::pluck('word')->toArray() as $word) {

                if (strpos($message['message'], $word) !== false) {

                    $title = mb_substr($message['message'], 0, 60);
                    if (!Post::where('title', $title)->exists()) {
                        $post = new Post();
                        $post->url = $message['chanel_name'];
                        $post->title = $title;
                        $post->description =  $message['message'];
                        $post->status = 0;
                        $post->save();
                        print_r($post);
                    } 
                   

                    if (isset($message['media'])) {
                        $images = $message['media'];
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
                    } 
                } 
            } 
        }

        dd($messages); 
     
    }
}
