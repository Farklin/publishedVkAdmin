@props(['label', 'key', 'placeholder', 'data'])

<div class="mb-3 row">
    <label for="inputName" class="col-4 col-form-label">{{$label}}</label>
    <div class="col-8">
        <input type="text" class="form-control" name="{{$key}}" id="inputName" placeholder="{{$placeholder}}" @isset($data['form'][$key]) value="{{$data['form'][$key]}}" @endif>
    </div>
</div>