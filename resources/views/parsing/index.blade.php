@extends('layout.base')

@section('content')
    

    <div class="container">
        <div class="container">
            <form action="{{route('par.test.start')}}" method="POST">
                @csrf
                <x-input.selector :data="$data" label="Тестовая страница" key="url" placeholder="URL"></x-input.selector>
                <x-input.selector :data="$data" label="Доменное имя" key="site" placeholder="Селектор"></x-input.selector>
                <x-input.selector :data="$data" label="Название" key="title" placeholder="Селектор"></x-input.selector>
                <x-input.selector :data="$data" label="Описание" key="description" placeholder="Селектор"></x-input.selector>
                <x-input.selector :data="$data" label="Картинка" key="image" placeholder="Селектор"></x-input.selector>
                <x-input.selector :data="$data" label="Дата" key="date" placeholder="Селектор"></x-input.selector>
        
                <div class="mb-3 row">
                    <div class="offset-sm-4 col-sm-8">
                        <button type="submit" class="btn btn-primary" name="action" value="start">Начать парсинг</button>
                    </div>
                </div>

                @if(!empty($data['result']['title']) and !empty($data['result']['description']))
                <div class="row mt-5">
                  <div class="col-md-12">
                    <button type="submit" class="btn btn-primary" name="action" value="save">Сохранить</button>
                  </div>
                </div>
              
                @endif

            </form>
        </div>
    </div>
    <div class="container">
      <div class="row">
        @isset($data['result'])
    
            @foreach($data['result'] as $key => $value)
            <div class="col-md-12">
              <strong>{{$key}}: </strong> {{$data['result'][$key]}}
            </div>
    
            @endforeach
  
        @endisset
      </div>
  

 
    </div>
    
  @endsection