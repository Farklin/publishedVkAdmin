<?php
namespace App\Services\Parsing\Sites; 
use App\Services\Parsing\Sites\SiteInterface;
use Exception;
use simplehtmldom\HtmlWeb;


class Site implements SiteInterface
{
    public $site = '';
    public $title = ''; 
    public $description = '';
    public $image = '';
    public $url = ''; 
    public $date = ''; 
    public $words = [
        'пожар', 'пожаров', 'пожара', 'спасатели', 'взрыв'
    ]; 

    public function __construct()
    {
        $this->client = new HtmlWeb(); 
    }

    public function getHost()
    {
        return $this->site; 
    }
    public function getUrl()
    {
        return $this->url;
    }
    public function getPageContent($url)
    {   
        $this->url = $url; 
        $this->responce = $this->client->load($this->url);
    }

    public function getTitle()
    {
        try{
            if($this->responce)
            {
                $this->title = $this->responce->find('h1', 0)->plaintext;
                return  $this->title; 
            }
          
        }
        catch (Exception $e)
        {
            
        }
       
    }
    public function getDescription()
    {}
    public function getImage()
    {}
    public function getDate()
    {}

    public function searchDescription($selector)
    {
        $this->description = ''; 
        foreach($this->responce->find($selector) as $paragraph)
        {
            $this->description .= $paragraph->plaintext . "\n\n"; 
        }
        return $this->description;
    }
    

}