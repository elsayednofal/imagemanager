@php
 
$id=rand(1,1000);

@endphp

<div class="form-group" id="{{$id}}">
    <label for="file-{{$id}}" class="btn btn-primary">
        <i class="fa fa-upload" aria-hidden="true"></i> choose Picture
    </label>
    {{-- file_upload --}}
    <input id="file-{{$id}}" class="sfu file-uploader d-none " data-target="{{$id}}" type="file" accept="image/*"  @if(isset($config['width'])) data-width="{{$config['width']}}" @endif  @if(isset($config['height'])) data-height="{{$config['height']}}" @endif/>
    @if (isset($config['display']))
        <img src="{{$config['display']}}" class="img-preview">
    @else    
        <img src="" class="img-preview" style="display:none">
    @endif
      
    <img src="{{url('vendor/elsayednofal/image-manager/loader.gif')}}" class="img-loading" style="width: 60px;display:none" />
    <input type="hidden" name="{{$config['name']}}" class="image-val" value="" @if(isset($config['required'])) required @endif >
</div>

