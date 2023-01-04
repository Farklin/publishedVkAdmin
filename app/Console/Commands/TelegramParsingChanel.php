<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class TelegramParsingChanel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:parsing-chanel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Парсинг каналов телеграм';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $MadelineProto = new \danog\MadelineProto\API('session.madeline');
        
        $chanels = [
            "@rian_ru", '@izvestia', '@truekpru', 
        ];

        foreach($chanels as $chanel)
        {
           
            $settings = array(
                'peer' => $chanel, //название_канала, должно начинаться с @, например @breakingmash
                'offset_date' =>  0,
                'add_offset' => 0,
                'limit' => 20, //Количество постов, которые вернет клиент
                'max_id' =>  0, //Максимальный id поста
                'min_id' =>  0, //Минимальный id поста - использую для пагинации, при  0 возвращаются последние посты.
                //'hash' => []
            );
    
            $data = $MadelineProto->messages->getHistory($settings);
            
      
            foreach($data['messages'] as $message)
            {
                if(isset($message['media']))
                {
                    Log::info($message['media']); 
                    $MadelineProto->download_to_dir($message,  public_path() . '/videos');
                    break; 
                }
                if(isset($message['message']))
                {   
                    //print_r($message['message']); 
                    // echo '-------------------';
                    // $m = strip_tags($message['message']); 
                    // print_r( str_replace(['🐔 Подписаться на @truekpr'], '', $m) ); 
                    // echo '-------------------';
                }
            }
            sleep(5);
        }
      
      
        return Command::SUCCESS;
    }
}
