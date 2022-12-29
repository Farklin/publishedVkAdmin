<div>
    
    <div class="row">
        <div class="col-md-8">

        </div>

        <div class="col-md-4">
            <form method="POST">
                @csrf
                <div class="mt-2"><input name="url" class="type="text" placeholder="Ссылка на страницу"></div>
                <div class="mt-2"><input class="type="text" placeholder="Начало парсинга"></div>
                <div class="mt-2"><input class="type="text" placeholder="Конец парсинга"></div>
                <div class="mt-2"><input class="type="text" placeholder="Исключения"></div>
                <div>
                    <button type="submit">Отправить</button>
                </div>
            </form>
        </div>
    </div>
 
</div>
