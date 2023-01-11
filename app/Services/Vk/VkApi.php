<?php

namespace App\Services\Vk;

use CURLFile;
use Illuminate\Support\Facades\Log;

class VkApi
{
    public $groupId = '217456935';
    public $albumId = '289249913';
    public $version = '5.81';

    /**
     * Получение хранилища для загрузки файлов 
     *
     * @return upload_url ссылка сервера для загрузки 
     */
    public function getStorage()
    {

        $curl = curl_init();
        $get = [
            'group_id' => $this->groupId,
            'album_id' => $this->albumId,
            'access_token' => env('VK_TOKEN'),
            'v' => $this->version,
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

    /**
     * Загрузка изображений на сервер 
     *
     * @param [type] $upload_server ссылка сервера загрузки 
     * @param [array] $images массив изображений (локальное расположение)
     * @return array $ids массив id загруженых изображений  
     */
    public function loadImage($upload_server, array $images)
    {
        $curl = curl_init();

        foreach ($images as $key => $image) {
            $images_param['file' . $key + 1] = new CURLFile($image);
        }

        curl_setopt_array($curl, array(
            CURLOPT_URL => $upload_server,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $images_param,
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $result = json_decode($response, true);

        $img_hash = $result['hash'];
        $photos_list = $result['photos_list'];
        $server = $result['server'];

        $curl = curl_init();
        $get = [
            'group_id' => $this->groupId,
            'album_id' => $this->albumId,
            'hash' => $img_hash,
            'photos_list' => $photos_list,
            'server' => $server,
            'access_token' => env('VK_TOKEN'),
            'v' => '5.81',
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


        $ids = [];
        foreach ($result['response'] as $imageJson) {
            $ids[] = $imageJson['id'];
        }
        return $ids;
    }
    
    /**
     * Создание поста 
     *
     * @param [string] $message текст публикации 
     * @param array $photo_ids массив id изобрежений 
     * @return responce 
     */
    public function createPost($message, array $photo_ids = null, array $video_ids = null)
    {
        $curl = curl_init();
        $get = [
            'owner_id' => '-' . $this->groupId,
            'from_group' => '1',
            'message' => $message,
            'access_token' => env('VK_TOKEN'),
            'v' => '5.81',
        ];

        if (!empty($photo_ids)) {
            $attachments = [];
            foreach ($photo_ids as $photo) {
                $attachments[] = 'photo' . '-' . $this->groupId . '_' . $photo;
            }
            $get['attachments'] =  join(',', $attachments);
        }

        if (!empty($video_ids)) {
            $attachments = [];
            foreach ($video_ids as $video) {
                $attachments[] = 'video' . '-' . $this->groupId . '_' . $video;
            }
            $get['attachments'] =  join(',', $attachments);
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

        return $result;
    }
    
    //TODO: переделать под публикацию нескольких изобображений или видео 

    /**
     * Undocumented function
     *
     * @param [string] $message 
     * @param array|null $images путь к файлам с изображениями()
     * @param array|null $videos путь к файлам с видео()
     * @return responce 
     */
    public function publishedPost(string $message, array $images = null, array $videos = null, string $video_name='')
    {
        $vkApi = new VkApi();
        $photoIds = [];
        if (!empty($images)) {
            $uploadImage = $vkApi->getStorage();
            $photoIds =  $vkApi->loadImage($uploadImage, $images);
        }
        $videoIds = []; 
        if(!empty($videos))
        {   
            $count = 1; 
            foreach($videos as $video)
            {
                $videoIds[] = $this->loadVideo($video, $video_name . '_' . $count); 
                $count ++; 
            }
        }
        return $vkApi->createPost($message, $photoIds, $videoIds);
    }



    public function loadVideo($path_image, $name = 'No name', $description = '' )
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
            'name'         => $name ,
            'description'  => $description,
            'group_id'     => $this->groupId, // ID группы
            'no_comments'  => 0 // разрешаем комментирование
        ]);

        curl_setopt($ch, CURLOPT_URL, 'https://api.vk.com/method/video.save?' . $parameters);
        curl_setopt($ch, CURLOPT_HTTPGET, TRUE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

        $curl_result = json_decode(curl_exec($ch), TRUE); // превращаем JSON-массив, который нам вернул VK, в обычный PHP-массив
        curl_close($ch);


        // // //
        // Загружаем видео на серверы ВК
        // // //

        $ch = curl_init();
        $parameters = [
            'video_file' => new CURLFile($path_image)  // PHP >= 5.5.0
            // 'video_file' => '@kinopoisk.ru-L_odyss__233_e-311292.mp4' // PHP < 5.5.0
        ];


        curl_setopt($ch, CURLOPT_URL, $curl_result['response']['upload_url']);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

        $curl_result = json_decode(curl_exec($ch), TRUE);

        curl_close($ch);

        if (isset($curl_result['error'])) {
            return 'Строка ' . __LINE__ . ': Ошибка при загрузке видео на серверы ВК: ';
        }

        return $curl_result['video_id'];
    }
}
