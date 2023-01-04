<?php

namespace App\Console\Commands;

use App\Jobs\ProcessTelegramParsingChanel;
use App\Models\ParsingWord;
use App\Models\Post;
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
        

        // TODO: создать модель каналов
        $chanels = [
            "@rian_ru", '@izvestia', '@truekpru',
        ];

        foreach ($chanels as $chanel) {
            ProcessTelegramParsingChanel::dispatch($chanel);
           
        }


        return Command::SUCCESS;
    }
}
