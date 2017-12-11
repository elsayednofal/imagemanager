<style>
    .dev-page .dev-page-container, .dev-page .dev-page-container .dev-page-content {
        z-index: 6;
    }
    .image-manager-modal {
        padding: 0 !important;
    }
    .image-manager-modal .modal-dialog {
        width: 90%;
    }
    .image-manager-search-container, .form_upload {
        padding: 20px 0;
        margin-bottom: 20px;
        border-bottom: 1px solid #CCCCCC;
    }
    .image_manger_images-container {
        height: 300px;
        padding: 0 20px;
        overflow-y: scroll;
    }
    .image_manger_images-container .image_manger_image {
        height: 150px;
        text-align: center;
        margin-bottom: 10px;
        position: relative;
    }
    .image_manger_image img {
        max-width: 90%;
        max-height: 90%;
        /*border: 2px solid #333333;*/
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        margin: auto;
        padding: 0.25rem;
        background-color: #F1F1F1;
        border: 1px solid #ddd;
        border-radius: 0.18rem;
        transition: all .2s ease-in-out;
    }
    .image_manger_image.active img {
        border: 5px solid #00b0ff;
    }
    .image_manger_image.active:before {
        content: "";
        position: absolute;
        z-index: 11;
        top: 0;
        left: 0;
        width: 30px;
        height: 30px;
        background: url(<?= url('vendor/SayedNofal/ImageManager/images/button-check_blue.png') ?>) no-repeat center;
        background-size: 100%;
    }
    .image_manger_inputs {
        display: table;
        width: 100%;
    }
    .image_manager_image_container {
        
        float: right;
        position: relative;
        margin: 5px;
        padding: 0.25rem;
        background-color: #F1F1F1;
        border: 1px solid #ddd;
        border-radius: 0.18rem;
        transition: all .2s ease-in-out;
        max-width: 100%;
        
    }
    .image_manager_image_container img:not(.image_manger_delete_image) {
        /*height: 150px;*/
        height:150px;
        width:150px;
    }
    .image_manger_delete_image {
        position: absolute;
        
        right: 1px;
        top: -3px;
        /*width: 20px;*/
        font-size:25px;
        cursor: pointer;
    }
</style>
<!-- Button trigger modal -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
<?php $id = rand(1, 99999) * rand(2, 100); ?>
<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#image_manger_single_choose_<?= $id ?>">
    {{Lang::get('image_manager.ChooseImage')}}
</button>
<div class="image_manger_inputs" id="image_manger_inputs_<?= $id ?>" data-modal="image_manger_single_choose_<?= $id ?>">
    @if(isset($images))
    @foreach($images as $image)
    <div class="image_manager_image_container">
        <a class="image_manger_delete_image text-danger"><i class="icon-ei-close-o"></i></a>
        <img src="{{config('ImageManager.upload_path').'/'.$image->name}}"/>
        <input type="hidden" value="{{$image->id}}" name="{{$name}}"/>
    </div>
    @endforeach
    @endif
</div>

<!-- Modal -->
<div class="modal fade image-manager-modal " id="image_manger_single_choose_<?= $id ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">{{Lang::get('image_manager.ImageManager')}}</h4>
            </div>
            <div class="modal-body">
                <div>
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs nav-top-border no-hover-bg" role="tablist">
                        <li class="nav-item" role="presentation"><a href="#image_manger_choose_<?= $id ?>" class="image_manger_choose_button nav-link active" aria-controls="choose" role="tab" data-toggle="tab">{{Lang::get('image_manager.Choose')}}</a></li>
                        <li class="nav-item" role="presentation"><a href="#image_manger_upload_<?= $id ?>" class="nav-link image_manger_upload_button" aria-controls="upload" role="tab" data-toggle="tab">{{Lang::get('image_manager.Upload')}}</a></li>
                    </ul>

                    <!-- Tab panes -->
                    <div class="tab-content" >
                        <div role="tabpanel" class="tab-pane active image_manger_choose" data-multi="<?php if ($multi) {echo 1;} else {echo 0;} ?>" data-count="<?php if(count($images)==0){echo 0;} else {echo count($images);}?>" id="image_manger_choose_<?= $id ?>">
                            <div class="image-manager-search-container">
                                <div class="row">
                                    <div class="col-md-10">
                                        <input type="text" class="form-control image_manger_search" placeholder="{{Lang::get('image_manager.Search')}}" />
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn image_manger_search_button"><img width="20" src='<?= url('vendor/SayedNofal/ImageManager/images/search-icon-2.png') ?>'</button>
                                    </div>
                                </div>
                            </div>
                            <div class="image_manger_images-container">
                                <div class="row">

                                </div>
                            </div>
                        </div>
                        <div role="tabpanel" class="tab-pane image_manger_upload" id="image_manger_upload_<?= $id ?>">
                            <div class="form_upload">
                                <div class="row">
                                    <div class="col-md-4">
                                        <input type="text" name="alt" placeholder="{{Lang::get('image_manager.AlternativeText')}}" class="form-control upload_alt" />
                                    </div>
                                    <div class="col-md-6">
                                        <input type="file" class="form-control upload_image_input" />
                                    </div>
                                    <div class="col-md-2">
                                        <a href="javacript:void()" class="btn btn-primary submit_upload">{{Lang::get('image_manager.Submit')}}</a> 
                                    </div>
                                </div>
                            </div>
                            <img src="<?= url('vendor/SayedNofal/ImageManager/images/loader.gif') ?>" width="100" class="upload-loading" style="display: none" />
                            <div class="row">
                                <div class="show_uploaded_image col-md-10 text-center"></div>
                                <div class="col-md-2 uploaded-image-action-area" >
                                    
                                </div>
                                <div class="component" style="display: none">
                                    <div id="button-component">
                                        <button class="shadow btn btn-primary upload_done">{{Lang::get('image_manager.Done')}}</button>
                                        <br/>
                                        <br/>
                                        <button class="shadow btn btn-danger upload_remove">{{Lang::get('image_manager.Remove')}}</button>
                                        <br/>
                                        <br/>
                                        <button class="shadow btn btn-info upload_edite">{{Lang::get('image_manager.Edit')}}</button>
                                        <br/>
                                        <br/>
                                        <button class="btn btn-success upload_select">{{Lang::get('image_manager.Select')}}</button>
                                        <br/>
                                    </div>
                                    <div class="cropper-buttons">
                                        <button type="button" class="btn btn-prmiary" id="rotate-right"><span class="fa fa-rotate-right"></span></button>
                                        <button type="button" class="btn btn-prmiary"  id="rotate-left"><span class="fa fa-rotate-left"></span></button>
                                        <button type="button" class="btn btn-prmiary"  id="scalex" data-x='-1'><span class="fa fa-arrows-h"></span></button>
                                        <button type="button" class="btn btn-prmiary"  id="scaley" data-y='-1'><span class="fa fa-arrows-v"></span></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default close_image_manger" data-dismiss="modal">{{Lang::get('image_manager.Close')}}</button>
                <button type="button" style="display: none" data-name="<?= $name ?>" data-append="image_manger_inputs_<?=$id?>" class="btn btn-primary image_manger_save">{{Lang::get('image_manager.Save')}}</button>
                <button type="button" style="display: none" data-name="<?= $name ?>" data-append="image_manger_inputs_<?=$id?>" class="btn btn-info image_manger_edit">{{Lang::get('image_manager.Edit')}}</button>
            </div>
        </div>
    </div>
</div>
<?php include_once './vendor/SayedNofal/ImageManager/js/selector.js'; ?>
<style>
<?php include_once './vendor/SayedNofal/ImageManager/cropper/cropper.css'; ?>
</style>
<script>
<?php include_once './vendor/SayedNofal/ImageManager/cropper/cropper.js'; ?>
</script>
