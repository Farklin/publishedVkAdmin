<div>
    @if (session()->has('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif
    <div class="row">
        <div class="col-md-8">

            <div class="mb-3">
              <label for="" class="form-label"></label>
              <textarea class="form-control" name="" id="" rows="3">{{$links}}</textarea>
            </div>

            <div class="row">
                <code>
                {{$html_content}}
                </code>
            </div>
        </div>

        <div class="col-md-4">
            <form method="POST">
                @csrf
                <div class="mt-2"><input  class="form-control" wire:model="parsingLink.title" type="text" placeholder="Наименование проекта"></div>
                @error('parsingLink.title') <span class="text-danger">{{ $message }}</span> @enderror

                
                <div class="mb-3">
                    <label for="" class="form-label">Тип парсинга</label>
                    <select class="form-select form-select-lg" wire:model="parsingLink.format" id="">
                        <option value="html" selected>html</option>
                        <option value="json">json</option>
                    </select>
                </div>
            
    
                <div class="mt-2"><input  class="form-control"  name="parsingLink.url" wire:model="parsingLink.url" type="text" placeholder="Ссылка на страницу"></div>
               @error('parsingLink.url') <span class="text-danger">{{ $message }}</span> @enderror

                <div class="mt-2"><input  class="form-control"  type="text" wire:model="parsingLink.selector_links_container" placeholder="Селектор парсинга блока"></div>
                @error('parsingLink.selector_links_container') <span class="text-danger">{{ $message }}</span> @enderror

                <div class="mt-2"><input  class="form-control"  type="text" wire:model="parsingLink.selector_links_end" placeholder="Конец парсинга"></div>
                @error('parsingLink.selector_links_end') <span class="text-danger">{{ $message }}</span> @enderror

                <div class="mt-2"><input  class="form-control"  type="text" placeholder="Исключения"></div>
                <div class="mt-4">
                    <button type="button" class="btn btn-primary" wire:click="parsingPage">Начать парсинг</button>
                </div>
           
                <div class="mt-4">
                    <button type="button" class="btn btn-success" wire:click="save">Сохранить</button>
                </div>
                
            </form>
        </div>
    </div>
    
</div>
