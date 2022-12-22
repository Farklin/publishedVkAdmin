<?php

use App\Http\Controllers\PublishedVkController;
use App\Services\Vk\VkApi;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {

    // $image = $_SERVER['DOCUMENT_ROOT'] . '/images/test.jpg'; 
    // $test = new VkApi(); 
    // $uploadImage = $test->getStorage(); 
    // $photoId =  $test->loadImage($uploadImage,$image);
    // return $test->createPost('тест',$photoId ); 
    $controller = new PublishedVkController(); 
    $controller->index();
    return 'Задачи поставлены в очередь';
});
