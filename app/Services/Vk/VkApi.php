<?php

namespace App\Services\Vk;

use CURLFile;
use Illuminate\Support\Facades\Log;

class VkApi
{
    public $groupId = '217456935'; 
    public $albumId = '289249913'; 
    public $version = '5.81';

    public function getStorage()
    {
        
        $curl = curl_init();
        $get = [
            'group_id' => $this->groupId, 
            'album_id' => $this->albumId , 
            'access_token' => env('VK_TOKEN'), 
            'v' => $this->version , 
        ];

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.vk.com/method/photos.getUploadServer?' . http_build_query($get),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        $result = json_decode($response, true);
        return $result['response']['upload_url']; 
    }

    public function loadImage($upload_server, $image_path )
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => $upload_server,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => array('file1'=> new CURLFile($image_path)),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $result = json_decode($response, true);
        
        $img_hash = $result['hash']; 
        $photos_list = $result['photos_list']; 
        $server = $result['server']; 

        $curl = curl_init();
        $get = [
            'group_id' => $this->groupId , 
            'album_id' => $this->albumId , 
            'hash' => $img_hash ,
            'photos_list' => $photos_list ,
            'server' => $server ,
            'access_token' => env('VK_TOKEN'), 
            'v' => '5.81' , 
        ];
        
        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.vk.com/method/photos.save?' . http_build_query($get),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        $result = json_decode($response, true);
        return $result['response'][0]['id']; 
    }

    public function createPost($message, $photo_id = null) 
    {
        $curl = curl_init();
        $get = [
            'owner_id' => '-' . $this->groupId , 
            'from_group' => '1' , 
            'message' => $message ,
            'access_token' => env('VK_TOKEN') , 
            'v' => '5.81' , 
        ];

        if(!empty($photo_id))
        {
            $get['attachments'] = 'photo' . '-' . $this->groupId . '_' . $photo_id; 
        }

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.vk.com/method/wall.post?' . http_build_query($get),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        $result = json_decode($response, true);

        Log::info($result ); 
        return $result; 

    }

    public function publishedPost($message, $image = null) 
    {
        $vkApi = new VkApi(); 
        $photoId = null; 
        if(!empty($image))
        {
            $uploadImage = $vkApi->getStorage(); 
            $photoId =  $vkApi->loadImage($uploadImage,$image); 
        }
        return $vkApi->createPost($message ,$photoId); 
    }


    // TODO: Доделать загрузку видео
    // TODO: Получить доступ загрузки видео, токен вк 
    public function loadVideo($url)
    {

        // $ch = curl_init();
                
        // curl_setopt($ch, CURLOPT_URL, $url);
        // curl_setopt($ch, CURLOPT_HTTPGET, TRUE);
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);


        // $curl_result = curl_exec($ch);

        // curl_close($ch);

        // $video_name = explode("/", $url); 
        // $video_name = $video_name[count($video_name) - 1]; 
        // // Кладем видео в папку со скриптом
        // $path_image = public_path() . '/videos/' . $video_name; 
        // $fp = fopen($path_image, 'x');
        // fwrite($fp, $curl_result);
        // fclose($fp);

        // // //
        // Получаем адрес ссылки, куда загружать видео
        // // //

        $ch = curl_init();
        $parameters = http_build_query([
            'access_token' => env('VK_TOKEN'), 
            'v'            => $this->version, // версия API
            'name'         => 'No name',
            'description'  => '',
            'group_id'     => $this->groupId, // ID группы
            'no_comments'  => 0 // разрешаем комментирование
        ]);

        curl_setopt($ch, CURLOPT_URL, 'https://api.vk.com/method/video.save?' . $parameters);
        curl_setopt($ch, CURLOPT_HTTPGET, TRUE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

        $curl_result = json_decode(curl_exec($ch), TRUE); // превращаем JSON-массив, который нам вернул VK, в обычный PHP-массив
        curl_close($ch);
        return $curl_result;    
     

         // // //
         // Загружаем видео на серверы ВК
         // // //

        // $ch = curl_init();
        // $parameters = [
        //     'video_file' => new CURLFile( $path_image )  // PHP >= 5.5.0
        //     // 'video_file' => '@kinopoisk.ru-L_odyss__233_e-311292.mp4' // PHP < 5.5.0
        // ];
        

        // curl_setopt($ch, CURLOPT_URL, $curl_result['response']['upload_url']);
        // curl_setopt($ch, CURLOPT_POST, TRUE);
        // curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

        // $curl_result = json_decode(curl_exec($ch), TRUE);

        // curl_close($ch);

        // if (isset($curl_result['error'])) {
        //     return 'Строка ' . __LINE__ . ': Ошибка при загрузке видео на серверы ВК: ';
        // }

        // return 'Видеозапись успешно загружена.';
            
    } 
}