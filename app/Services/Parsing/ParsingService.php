<?php

namespace App\Services\Parsing;
use simplehtmldom\HtmlWeb;


class ParsingService
{
    public function start() 
    {

        $client = new HtmlWeb();
        $html = $client->load('https://idp-cs.net/news_cat_faier.php?N=1#main-tbl');
        $sites = [
            'www.interfax.ru'
        ];
        foreach($html->find('p a') as $href) 
        { 
            $parse = parse_url($href->href);
            if(isset($parse['host']))
            {
                if(in_array( $parse['host'], $sites))
                {
                    $mas[] = $href->href;
                }
            }
        }
        // Returns the page title
        return $mas;

    }
}