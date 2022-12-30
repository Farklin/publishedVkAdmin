<?php

namespace App\Services\Parsing;

use App\Jobs\ProcessParsingSite;
use App\Models\Post;
use App\Models\ParsingLink;
use App\Models\SiteSetting;
use App\Services\Parsing\Sites\LentaSite;
use App\Services\Parsing\Sites\RiaSite;
use App\Services\Parsing\Sites\TassSite;
use simplehtmldom\HtmlWeb;


class ParsingService
{

    // TODO: протестировать логику работы
    public function start() 
    {
        
        // получаем сайты где собираемссылки 
        $parsingLink = ParsingLink::all();
        // получаем настройки сайтов
        $sites = SiteSetting::all();

        
        foreach($parsingLink as $project) 
        {   
            // получаем хост проекта 
            $host = parse_url($project->url)['host'];
            // если формат html
            if($project->format == 'html')
            {
                // получаем ссылки 
                $links = $this->html($project, $host);

            }elseif($project->format == 'json')
            {
                $links = $this->json($project, $host);
            }
            
            // передаем ссылки на парсиннг 
            foreach($links as $link)
            {   
                //перебераем сайты и находим нужную настройку для парсинга 
                foreach($sites as $site)
                {
                    if($site->site == $host)
                    {    // создаем очередь
                        ProcessParsingSite::dispatch($link, $site);
                    }
                }
               
            }
        }


        // $client = new HtmlWeb();
        // $html = $client->load('https://idp-cs.net/news_cat_faier.php?N=1#main-tbl');
        // $sites = SiteSetting::all(); 

        // foreach($html->find('p a') as $href) 
        // { 
        //     $parse = parse_url($href->href);
        //     if(isset($parse['host']))
        //     {
        //         foreach($sites as $site)
        //         {
        //            if($site->site == $parse['host'])
        //            {    
        //                 ProcessParsingSite::dispatch($href, $site);
        //            }
        //         }
        //     }
        // }

    }

    public function html($project, $host)
    {
        $html = new HtmlWeb(); 
        $responce = $html->load($project->url); 
        foreach($responce->find($project->selector_links_container . ' a') as $a)
        {
            $links[] = $this->existsHost($a->href, $host);
        }
        return $links;
    }

    public function json($project, $host)
    {
        $html = new HtmlWeb(); 
        $responce = $html->load($project->url); 
        $html_content = $responce->__toString(); 
        $json = json_decode($html_content, true);
        foreach($json[$project->selector_links_container] as $match)
        {
            $links[] = $this->existsHost($match[$project->selector_links_end], $host);
        }
        return $links;
    }

    public function existsHost($link, $host_default)
    {   
        $parse = parse_url($link);
        if(!empty($parse['host']))
        {
            $result = $link; 
        }
        else 
        {
            $result = 'http://' . $host_default . $link; 
        }
        return $result;
    }
    
}