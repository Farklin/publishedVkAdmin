<?php
namespace App\Services\Parsing\Sites; 
use App\Services\Parsing\Sites\SiteInterface;
use simplehtmldom\HtmlWeb;


class TassSite extends Site
{
    public $site = 'tass.ru';

    public function getDescription()
    {   
        $this->description = ''; 
        foreach($this->responce->find('article p') as $paragraph)
        {
            $this->description .= $paragraph->plaintext . "\n\n"; 
        }
    }
}