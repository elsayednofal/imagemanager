<div class="modal fade" id="media-model" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body" id="media-modal-body">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="images-tab" data-toggle="tab" href="#media-images" role="tab" aria-controls="home" aria-selected="true"><i class="far fa-images"></i>{{trans('media-manager::media.images')}}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="upload-tab" data-toggle="tab" href="#media-uploader" role="tab" aria-controls="profile" aria-selected="false"><i class="fas fa-cloud-upload-alt"></i>{{trans('media-manager::media.upload')}}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="settings-tab" data-toggle="tab" href="#media-setting" role="tab" aria-controls="contact" aria-selected="false"><i class="fas fa-cog"></i>{{trans('media-manager::media.setting')}}</a>
                    </li>
                </ul>
                <div class="tab-content" id="myTabContent">
                    @include('backend.image-manager.partials.images')
                    @include('backend.image-manager.partials.upload')
                    @include('backend.image-manager.partials.settings')
                </div>
            </div>
        </div>
    </div>
</div>
