<!doctype html>
<html lang="ru">

<head>
  <title>Тестирование парсинга</title>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <!-- Bootstrap CSS v5.2.1 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">

</head>

<body>
  <header>
    <!-- place navbar here -->
  </header>
  <main>
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
    
  </main>
  <footer>
    <!-- place footer here -->
  </footer>
  <!-- Bootstrap JavaScript Libraries -->
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"
    integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous">
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.min.js"
    integrity="sha384-7VPbUDkoPSGFnVtYi0QogXtr74QeVeeIs99Qfg5YCF+TidwNdjvaKZX19NZ/e6oz" crossorigin="anonymous">
  </script>
</body>

</html>