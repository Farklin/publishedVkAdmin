<?php
namespace App\Services\Parsing\Sites; 
use App\Services\Parsing\Sites\SiteInterface;
use simplehtmldom\HtmlWeb;


class Site implements SiteInterface
{
    public $site = '';
    public $title = ''; 
    public $description = '';
    public $image = '';
    public $url = ''; 

    public function __construct()
    {
        $this->client = new HtmlWeb(); 
    }

    public function getHost()
    {
        return $this->site; 
    }
    
    public function getPageContent($url)
    {   
        $this->url = $url; 
        $this->client->load($this->url);
    }

    

}