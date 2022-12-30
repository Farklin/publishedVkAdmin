<?php

namespace App\Http\Livewire;

use App\Models\ParsingLink;
use Livewire\Component;
use simplehtmldom\HtmlWeb;

class ParsingLinkForm extends Component
{

    public $url;
    public $format;
    public $html_content;
    public $links;
    public $selector_links_container;
    public $selector_links_end;
    public $title;
    public $button_save;

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
        $this->validate(['url' => 'required'], ['url.required' => 'Требуется ввести ссылку на страницу']);

        $links = [];
        $host = parse_url($this->url)['host'];

        if ($this->format == 'html') {
            $html = new HtmlWeb();
            $responce = $html->load($this->url);
            $this->html_content = $responce->__toString();

            $validatedData = $this->validate([
                'url' => 'required',
                'selector_links_container' => 'required',
            ]);

            foreach ($responce->find($this->selector_links_container . ' a') as $a) {
                $links[] = $this->existsHost($a->href, $host);
            }
        } elseif ($this->format == 'json') {
            $html = new HtmlWeb();
            $responce = $html->load($this->url);
            $this->html_content = $responce->__toString();
            $json = json_decode($this->html_content, true);
            $validatedData = $this->validate([
                'url' => 'required',
                'selector_links_container' => 'required',
                'selector_links_end' => 'required',
            ],  [
                'url.required' => 'Требуется ввести ссылку на страницу',
                'selector_links_container.required' => 'Для продолжения требуется заполнить',
                'selector_links_end.required' => 'Для продолжения требуется заполнить',
            ],);
            foreach ($json[$this->selector_links_container] as $match) {
                $links[] = $this->existsHost($match[$this->selector_links_end], $host);
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
            'url' => 'required|unique:parsing_links',
            'format' => 'required',
            'title' => 'required|unique:parsing_links',
            'selector_links_container' => 'required',
        ];
        $default_validate_description = [
            'title.unique' => 'Требуется ввести уникальное название проекта',
            'url.required' => 'Требуется ввести ссылку на страницу',
            'title.required' => 'Требуется ввести название проекта',
            'selector_links_container.required' => 'Для продолжения требуется заполнить',
        ];

        if ($this->format == 'html') {
            $dataValidate = $this->validate($default_validate, $default_validate_description);
            ParsingLink::create($dataValidate);
        } elseif ($this->format == 'json') {
            $edit_validate = $default_validate;
            $edit_validate_description = $default_validate_description;
            $edit_validate['selector_links_end'] = 'required';
            $edit_validate_description['selector_links_container.required'] = 'Для продолжения требуется заполнить';
            $dataValidate = $this->validate($edit_validate, $default_validate_description);
            ParsingLink::create($dataValidate);
        }
    }

    public function render()
    {
        return view('livewire.parsing-link-form');
    }
}
