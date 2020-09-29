<div class="media-manager-group">
    <button type="button" class="btn btn-primary media-selector" data-toggle="modal" id="media-{{$name}}" data-name="{{$name}}" data-cat="{{$cat_id}}" data-single="<?=$single?>" data-target="#media-model" data-images="<?= implode(',',$imgs) ?>">
        <i class="far fa-images"></i> {{trans('media-manager::media.choose')}} {{trans('media-manager::media.images')}}
    </button>
    <div class="result">
        @foreach($imgs as $img)
        <img src="<?= ImageManager::getImagePath($img,'thumb')?>" class="media-image on-img" />
        <input type="hidden" name="{{$name}}" value="{{$img}}" />
        @endforeach
    </div>
</div>