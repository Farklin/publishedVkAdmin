<?php

namespace App\Services\Vk;

use CURLFile;

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
            'access_token' => 'vk1.a.EwSI8b66TZEnwQEOkaPEC4_8sEy34ZAbd8Y-ZYlqt6f2N4inEDrZEH_g9dH62sSC-LTi7bERkvfWAM11cT1YaG7KD4CK4at0ml9hSRGjdgmOn2Bm9DsS5_f-d-ScSdlIGc5-f58mpn1kcyfqj_BAIwtaQuj5_2CFNoOoFFEwTsPijPOcfABVizxxqtVEWZTLWC9pSERRq5Z-MEAxDWCKxA' , 
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
            'access_token' => 'vk1.a.EwSI8b66TZEnwQEOkaPEC4_8sEy34ZAbd8Y-ZYlqt6f2N4inEDrZEH_g9dH62sSC-LTi7bERkvfWAM11cT1YaG7KD4CK4at0ml9hSRGjdgmOn2Bm9DsS5_f-d-ScSdlIGc5-f58mpn1kcyfqj_BAIwtaQuj5_2CFNoOoFFEwTsPijPOcfABVizxxqtVEWZTLWC9pSERRq5Z-MEAxDWCKxA' , 
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
            'access_token' => 'vk1.a.EwSI8b66TZEnwQEOkaPEC4_8sEy34ZAbd8Y-ZYlqt6f2N4inEDrZEH_g9dH62sSC-LTi7bERkvfWAM11cT1YaG7KD4CK4at0ml9hSRGjdgmOn2Bm9DsS5_f-d-ScSdlIGc5-f58mpn1kcyfqj_BAIwtaQuj5_2CFNoOoFFEwTsPijPOcfABVizxxqtVEWZTLWC9pSERRq5Z-MEAxDWCKxA' , 
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
}