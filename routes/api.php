<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\ParsingWord;
use App\Models\ParsingChanelWord;
use App\Models\Post;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('words', function(){
   return ParsingWord::all();
});

Route::get('telegram_chanels', function(){
    return ParsingChanelWord::where('status', 1)->get();
 });

Route::get('posts', function(){
    return Post::limit(120)->orderBy('id', 'desc')->get();
}); 