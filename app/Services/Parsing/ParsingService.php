<?php

namespace App\Services\Parsing;

use App\Models\Post;
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
        $sites = [
            new RiaSite(), 
            new TassSite(), 
            new LentaSite() 
        ];
        $mas = []; 
        foreach($html->find('p a') as $href) 
        { 
            $parse = parse_url($href->href);
            if(isset($parse['host']))
            {
                foreach($sites as $site)
                {
                   if($site->getHost() == $parse['host'])
                   {    
                        sleep(5); 
                        $site->getPageContent($href->href);
                        $site->getTitle(); 
                        $site->getDescription(); 

                        foreach($site->words as $word)
                        {
                            if(strpos($site->description, $word) !== false)
                            {
                                if(!Post::where('url', $site->url)->exists())
                                {
                                    $masResult[] = $site->url; 
                                    $post = new Post();
                                    $post->title = $site->title;
                                    $post->description = $site->description;
                                    $post->image = $site->image;
                                    $post->url = $site->url;
                                    $post->status = 0;
                                    $post->date = $site->date; 
                                    $post->save(); 
                                }
                            
                            }
                        }
                        //$masResult[] = $site->getTitle(); 
                   }
                }
                $mas[] = $href->href; 
            }
            
        }
        if(empty($masResult))
        {
            return $mas; 
        }
        // Returns the page title
        return $masResult;

    }
}