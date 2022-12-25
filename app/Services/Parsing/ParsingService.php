<?php

namespace App\Services\Parsing;

use App\Jobs\ProcessParsingSite;
use App\Models\Post;
use App\Models\SiteSetting;
use App\Services\Parsing\Sites\LentaSite;
use App\Services\Parsing\Sites\RiaSite;
use App\Services\Parsing\Sites\TassSite;
use simplehtmldom\HtmlWeb;


class ParsingService
{
    public function start() 
    {

        $client = new HtmlWeb();
        $html = $client->load('https://idp-cs.net/news_cat_faier.php?N=1#main-tbl');
        $sites = SiteSetting::all(); 

        foreach($html->find('p a') as $href) 
        { 
            $parse = parse_url($href->href);
            if(isset($parse['host']))
            {
                foreach($sites as $site)
                {
                   if($site->site == $parse['host'])
                   {    
                        ProcessParsingSite::dispatch($href, $site);
                   }
                }
            }
        }

    }
}