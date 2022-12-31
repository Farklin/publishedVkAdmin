
<div class="container">
    <div class="row">
        <a href="{{route('par.link.add')}}">Создать</a>
    </div>
    <table class="table">
        <thead>
          <tr>
            <th scope="col">#</th>
            <th scope="col">Название</th>
            <th scope="col">URL</th>
            <th scope="col">Формат</th>
            <th scope="col"></th>
            <th scope="col"></th>
          </tr>
        </thead>
        <tbody>
           @foreach($parsingLinks as $project)
          <tr>
            <th scope="row">{{$loop->iteration}}</th>
            <td>{{$project->title}}</td>
            <td>{{$project->url}}</td>
            <td>{{$project->format}}</td>
            <td><a href="{{route('par.link.update', $project->id)}}">Редактировать</a></td>
            <td><button wire:click="deleteParssinLink({{$project->id}})">Удалить</button></td>
            
          </tr>
          
          @endforeach
        </tbody>
      </table>
   
</div>
