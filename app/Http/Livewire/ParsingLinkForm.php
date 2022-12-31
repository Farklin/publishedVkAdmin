<?php

namespace App\Http\Livewire;

use App\Models\ParsingLink;
use Livewire\Component;
use simplehtmldom\HtmlWeb;

class ParsingLinkForm extends Component
{
    public ParsingLink $parsingLink; 
    public $html_content;
    public $links;
    public $button_save;


    protected $rules = [
        'parsingLink.title' => '', 
        'parsingLink.format' => '', 
        'parsingLink.url' => '', 
        'parsingLink.selector_links_container' => '', 
        'parsingLink.selector_links_end' => '', 
    ];

    public function existsHost($link, $host_default)
    {
        $parse = parse_url($link);
        if (!empty($parse['host'])) {
            $result = $link;
        } else {
            $result = 'http://' . $host_default . $link;
        }
        return $result;
    }

    public function parsingPage()
    {
        $this->validate(['parsingLink.url' => 'required'], ['url.required' => 'Требуется ввести ссылку на страницу']);

        $links = [];
        $host = parse_url($this->parsingLink->url)['host'];

        if ($this->parsingLink->format == 'html') {
            $html = new HtmlWeb();
            $responce = $html->load($this->parsingLink->url);
            $this->html_content = $responce->__toString();

            $validatedData = $this->validate([
                'parsingLink.url' => 'required',
                'parsingLink.selector_links_container' => 'required',
            ]);

            foreach ($responce->find($this->parsingLink->selector_links_container . ' a') as $a) {
                $links[] = $this->existsHost($a->href, $host);
            }
        } elseif ($this->parsingLink->format == 'json') {
            $html = new HtmlWeb();
            $responce = $html->load($this->parsingLink->url);
            $this->html_content = $responce->__toString();
            $json = json_decode($this->html_content, true);
            $validatedData = $this->validate([
                'parsingLink.url' => 'required',
                'parsingLink.selector_links_container' => 'required',
                'parsingLink.selector_links_end' => 'required',
            ],  [
                'parsingLink.url.required' => 'Требуется ввести ссылку на страницу',
                'parsingLink.selector_links_container.required' => 'Для продолжения требуется заполнить',
                'parsingLink.selector_links_end.required' => 'Для продолжения требуется заполнить',
            ],);
            foreach ($json[$this->parsingLink->selector_links_container] as $match) {
                $links[] = $this->existsHost($match[$this->parsingLink->selector_links_end], $host);
            }
        }

        if (!empty($this->links)) {
            $this->button_save = true;
        } else {
            $this->button_save = false;
        }
        $this->links = join(" \n", $links);
    }

    public function save()
    {
        $default_validate = [
            'parsingLink.url' => 'required|unique:parsing_links,url,'.$this->parsingLink->id,
            'parsingLink.format' => 'required',
            'parsingLink.title' => 'required|unique:parsing_links,title,'.$this->parsingLink->id,
            'parsingLink.selector_links_container' => 'required',
        ];
        $default_validate_description = [
            'parsingLink.title.unique' => 'Требуется ввести уникальное название проекта',
            'parsingLink.url.required' => 'Требуется ввести ссылку на страницу',
            'parsingLink.title.required' => 'Требуется ввести название проекта',
            'parsingLink.selector_links_container.required' => 'Для продолжения требуется заполнить',
        ];

        if ($this->parsingLink->format == 'html') {
            
            $dataValidate = $this->validate($default_validate, $default_validate_description);
            $this->parsingLink->save(); 

        } elseif ($this->parsingLink->format == 'json') {
            $edit_validate = $default_validate;
            $edit_validate_description = $default_validate_description;
            $edit_validate['parsingLink.selector_links_end'] = 'required';
            $edit_validate_description['parsingLink.selector_links_container.required'] = 'Для продолжения требуется заполнить';
            $dataValidate = $this->validate($edit_validate, $default_validate_description);
            $this->parsingLink->save(); 
        }
        session()->flash('message', 'Настройки успешно сохранены');
        
    }

    public function render()
    {   
        return view('livewire.parsing-link-form');
    }
}
