<!-- Button trigger modal -->
<?php $id = rand(1, 99999) * rand(2, 100); ?>
<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#image_manger_single_choose_<?= $id ?>">
    choose Image
</button>
<div class="image_manger_inputs">
    @if(isset($images) )
        @foreach($images as $image)
            <div class="image_manager_image_container">
                <!--<img src="./uploads/thumbs/02-2017/8134a92623c50e39ef69938c59e6863259d100da.jpeg">-->
                <img src="{{config('ImageManager.upload_path').'/'.$image->name}}">
                <img class="image_manger_delete_image" src="{{url('vendor/SayedNofal/ImageManager/images/close.png')}}">
                <input type="hidden" value="{{$image->id}}" name="{{$name}}">
            </div>
        @endforeach
    @endif
</div>

<!-- Modal -->
<div class="modal fade image-manager-modal" id="image_manger_single_choose_<?= $id ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Image Manager</h4>
            </div>
            <div class="modal-body">
                <div>
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="active"><a href="#image_manger_choose_<?= $id ?>" class="image_manger_choose_button" aria-controls="choose" role="tab" data-toggle="tab">Choose</a></li>
                        <li role="presentation"><a href="#image_manger_upload_<?= $id ?>" aria-controls="upload" role="tab" data-toggle="tab">Upload</a></li>
                    </ul>

                    <!-- Tab panes -->
                    <div class="tab-content" >
                        <div role="tabpanel" class="tab-pane active image_manger_choose" data-multi="<?php if ($multi) {echo 1;} else {echo 0;} ?>" data-count="0" id="image_manger_choose_<?= $id ?>">
                            <div class="image-manager-search-container">
                                <div class="row">
                                    <div class="col-md-10">
                                        <input type="text" class="form-control image_manger_search" placeholder="search...." />
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
                                        <input type="text" name="alt" placeholder="alternative text .." class="form-control upload_alt" />
                                    </div>
                                    <div class="col-md-6">
                                        <input type="file" class="form-control upload_image_input" />
                                    </div>
                                    <div class="col-md-2">
                                        <button class="btn btn-primary submit_upload" >submit</button> 
                                    </div>
                                </div>
                            </div>
                            <img src="<?= url('vendor/SayedNofal/ImageManager/images/loader.gif') ?>" width="100" class="upload-loading" style="display: none" />
                            <div class="row">
                                <div class="show_uploaded_image col-md-9 text-center"></div>
                                <div class="col-md-2">
                                    <button class="btn btn-primary upload_done actions_buttons" style="display:none" >Done</button>
                                    <button class="btn btn-danger upload_remove actions_buttons" style="display:none" >remove</button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default close_image_manger" data-dismiss="modal">Close</button>
                <button type="button" style="display: none" data-name="<?= $name ?>" class="btn btn-primary image_manger_save">Save</button>
            </div>
        </div>
    </div>
</div>
<?php include_once './vendor/SayedNofal/ImageManager/js/selector.js'; ?>
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
        border: 2px solid #333333;
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        margin: auto;
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
        float: left;
        position: relative;
        margin: 5px;
    }
    .image_manager_image_container img:not(.image_manger_delete_image) {
        height: 150px;
    }
    .image_manger_delete_image {
        position: absolute;
        left: -3px;
        top: -5px;
        width: 20px;
        cursor: pointer;
    }
</style>