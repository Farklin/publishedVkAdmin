<?php

use App\Http\Controllers\PublishedVkController;
use App\Models\ParsingWord;
use App\Models\SiteSetting;
use App\Services\Parsing\Sites\Site;
use App\Services\Parsing\Telegram\TgIca;
use App\Services\Vk\VkApi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use simplehtmldom\HtmlWeb;

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
    return 'Задачи поставлены в очередь';
});

Route::get('test', function () {
    return ParsingWord::pluck('word')->toArray(); 
}); 

Route::middleware('auth')->group(function(){
    Route::any('/par/site', function(Request $request){

        if($request->isMethod('post'))
        {

            $html = new HtmlWeb(); 
            $responce = $html->load($request->url); 
            return dd($responce->copy_until('body'));

        }


        return view('parsing.site'); 
    })->name('par.site');

    Route::post('/par/test/start', function (Request $request) {
        $data = [] ;
        if($request->input('action') == 'start')
        {
            $site = new Site(); 
            $site->getPageContent($request->input('url')); 
            $data['form'] = $request->all();
            $data['form']['site'] =  parse_url($request->input('url'))['host'] ;
            $data['result']['site'] = parse_url($request->input('url'))['host'];
            $data['result']['title'] = $site->getCustom($request->input('title'), ''); 
            $data['result']['description'] = $site->getCustom($request->input('description')); 
            $data['result']['image'] = $site->getCustom($request->input('image'), ''); 
            $data['result']['date'] = $site->getCustom($request->input('date')); 
        }
        elseif($request->input('action') == 'save')
        {
            $siteSetting = new SiteSetting($request->all());
            if(SiteSetting::where('site', $request->input('site'))->exists())
            {
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
