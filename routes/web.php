<?php

use App\Console\Commands\TelegramParsingChanel;
use App\Http\Controllers\PublishedVkController;
use App\Http\Livewire\Parsing\ParsingLinks;
use App\Models\ParsingLink;
use App\Models\ParsingWord;
use App\Models\Post;
use App\Models\SiteSetting;
use App\Services\Parsing\Sites\Site;
use App\Services\Parsing\Telegram\TgIca;
use App\Services\Vk\VkApi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Longman\TelegramBot\Telegram as TelegramBotTelegram;
use simplehtmldom\HtmlWeb;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Laravel\Facades\Telegram;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
// Route::middleware('auth')->group(
//     function()
//     {

//     }
// );

Route::get('/par', function () {

    $controller = new PublishedVkController();
    $controller->index();
    $telegramParsing = new TelegramParsingChanel(); 
    $telegramParsing->handle(); 
    return 'Задачи поставлены в очередь';
});

Route::get('test', function () {
    $vk = new VkApi();

    //$vk->publishedPost('текст', [public_path() . '/images/test.jpg', public_path() . '/images/test.jpg']);
    //return $vk->loadVideo(public_path() . '/videos/1778705_Умер_астронавт_Уолтер_Канингэм_2сергей_04_1058_5307907265150329572.mp4');
});

Route::get('/bot/get-update', function () {
    $updates = Telegram::getUpdates();
    $ids = []; 
    
    // $inline_keyboard = json_encode([ //Потому что его объект
    //     'inline_keyboard' => [
    //         [
    //             ['text' => 'Опубликовать', 'callback_data' => 'published|' . 1],
    //         ],
    //     ]
    // ]);

    // Telegram::editMessageReplyMarkup([
    //     'message_id' => 31,
    //     'chat_id' => 1037165023, 
    //     'reply_markup' => '', 
    // ]); 
    

    // $inline_keyboard = json_encode([ //Потому что его объект
    //             'inline_keyboard' => [
    //                 [
    //                     ['text' => 'Опубликовать', 'callback_data' => 'published|' . 1],
    //                     ['text' => 'Не публиковать', 'callback_data' => 'nopublished|' . 1]
    //                 ],
    //             ]
    //         ]);
        
    //         $response = Telegram::sendMessage([
    //             'chat_id' => '1037165023',
    //             'text' => '12',
    //             'reply_markup' => $inline_keyboard
    //         ]);

    // $messageId = $response->getMessageId();  

    // return dd($messageId); 
    foreach ($updates as $message)
    {   
        if(isset($message['callback_query']))
        {    
            // при возврате нажатии кнопки в телеграм 
            
            $calback = explode('|', $message['callback_query']['data']);
            if(in_array($message['callback_query']['from']['id'], config('telegram')['admins'])) 
            {
                if($calback[0] == 'published') 
                { 
                    $post = Post::find($calback[1]);
                    $post->moderation = 'published';
                    $post->save();
                }
                if($calback[0] == 'banned') 
                { 
                    $post = Post::find($calback[1]);
                    $post->moderation = 'banned';
                     $post->save();
                }
    
                try{
                    Telegram::editMessageReplyMarkup([
                        'message_id' =>  $message['callback_query']['message']['message_id'],
                        'chat_id' => $message['callback_query']['message']['chat']['id'], 
                        'reply_markup' => '', 
                    ]); 
                }
                catch(Exception $e){
    
                }
            }
            
        }
    }
  
    
    
    // foreach(Post::limit(8)->get() as $post)
    // {
    //     $inline_keyboard = json_encode([ //Потому что его объект
    //         'inline_keyboard' => [
    //             [
    //                 ['text' => 'Опубликовать', 'callback_data' => 'published|' . $post->id],
    //                 ['text' => 'Не публиковать', 'callback_data' => 'nopublished|' . $post->id]
    //             ],
    //         ]
    //     ]);
    
    //     $response = Telegram::sendMessage([
    //         'chat_id' => '1037165023',
    //         'text' => $post->description,
    //         'reply_markup' => $inline_keyboard
    //     ]);
    // }
    
    
    // $messageId = $response->getMessageId();

    return dd($updates); 
});


Route::middleware('auth')->group(function () {
    Route::get(
        '/par/links',
        ParsingLinks::class
    )->name('par.links');

    Route::any('/par/link/update/{id}', function ($id) {
        $parsingLink = ParsingLink::find($id);
        return view('parsing.site', compact('parsingLink'));
    })->name('par.link.update');

    Route::any('/par/link/add', function (Request $request) {
        $parsingLink = new ParsingLink();
        return view('parsing.site', compact('parsingLink'));
    })->name('par.link.add');

    Route::post('/par/test/start', function (Request $request) {
        $data = [];
        if ($request->input('action') == 'start') {
            $site = new Site();
            $site->getPageContent($request->input('url'));
            $data['form'] = $request->all();
            $data['form']['site'] =  parse_url($request->input('url'))['host'];
            $data['result']['site'] = parse_url($request->input('url'))['host'];
            $data['result']['title'] = $site->getCustom($request->input('title'), '');
            $data['result']['description'] = $site->getCustom($request->input('description'));
            $data['result']['image'] = $site->getCustom($request->input('image'), '');
            $data['result']['date'] = $site->getCustom($request->input('date'));
        } elseif ($request->input('action') == 'save') {
            $siteSetting = new SiteSetting($request->all());
            if (SiteSetting::where('site', $request->input('site'))->exists()) {
                $siteSetting = SiteSetting::where('site', $request->input('site'))->first();
                $siteSetting->update($request->all());
            }
            $siteSetting->save();
            $data['form'] = $request->all();
        }


        return view('parsing.index', compact('data'));
    })->name('par.test.start');

    Route::get('/par/test', function () {
        $data = [];

        return view('parsing.index', compact('data'));
    });
});
