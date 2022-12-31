<?php

namespace App\Http\Livewire\Parsing;

use App\Models\ParsingLink;
use Livewire\Component;

class ParsingLinks extends Component
{   
    public $parsingLinks;
    
    public function deleteParssinLink($id)
    {
        ParsingLink::find($id)->delete(); 
    }

    public function render()
    {
        $this->parsingLinks = ParsingLink::all(); 
        return view('livewire.parsing.parsing-links')
        ->extends('layout.base')
        ->section('content');;
    }
}
