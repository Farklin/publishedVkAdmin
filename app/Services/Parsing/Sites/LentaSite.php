<?php
namespace App\Services\Parsing\Sites; 
use App\Services\Parsing\Sites\SiteInterface;
use simplehtmldom\HtmlWeb;


class LentaSite extends Site
{
    public $site = 'lenta.ru';

    public function getDescription()
    {
        $this->searchDescription('.topic-body__content p'); 
    }

    public function getImage()
    {
        $this->image = $this->responce->find('.picture._news img', 0)->src;
    }


}