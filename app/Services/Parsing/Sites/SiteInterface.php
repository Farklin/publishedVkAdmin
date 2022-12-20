<?php

namespace App\Services\Parsing\Sites; 

interface SiteInterface
{
    public function getTitle();
    public function getDescription();
    public function getImage();
    public function getDate(); 
    public function getUrl(); 
    
}