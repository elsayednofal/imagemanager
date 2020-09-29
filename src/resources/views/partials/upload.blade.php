<div class="tab-pane fade" id="media-uploader" role="tabpanel" aria-labelledby="media-upload">
    <div class="row" id="upload_form" style="margin:30px 0px 5px;border-bottom: 1px solid;">
        <div class="col-md-2" v-if="!target_cat">
            <div class="form-group">
                <label for="cat_id">{{trans('media-manager::media.choose')}} {{trans('media-manager::media.category')}}</label>
                <select id="cat_id" class="form-control" v-model="cat_id">
                    <option value="null">{{trans('media-manager::media.choose')}} {{trans('media-manager::media.category')}}</option>
                    <option v-for="cat in categories" v-bind:value="cat.id">@{{cat.name}}</option>
                </select>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label for="alt">{{trans('media-manager::media.alt')}}</label>
                <input type="text" class="form-control" v-model="alt" /> 
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="image">{{trans('media-manager::media.choose')}} {{trans('media-manager::media.image')}}</label>
                <input type="file" class="form-control" v-on:change="processFile($event)" />
            </div>
        </div>
        <div class="col-md-2">
            <br/>
            <button type="button" class="btn btn-primary" @click="upload()">{{trans('media-manager::media.upload')}}</button>
        </div>
        
    </div>
    
    <div class="row" id="upload-result">
        <div class="col-md-9 col-sm-12" style="border-right:1px solid;">
            <img v-if="image_status=='temp'" v-bind:src="temp_image.path+'/'+temp_image.name" style="max-width:70%" />
            <img v-if="image_status=='done'" v-bind:src="done_image.path+'/'+done_image.name" style="max-width:70%" />
        </div>
        <div class="col-md-2 col-md-offset-1 col-sm-12 col-order-first">
            <button v-if="image_status=='temp'" type="button" class="btn btn-primary" @click="done()">{{trans('media-manager::media.approve')}}</button><br/><br/>
            <button v-if="image_status=='temp'" type="button" class="btn btn-danger" @click="cancleTemp()">{{trans('media-manager::media.cancel')}}</button><br/><br/>
            <button v-if="image_status=='done'" type="button" class="btn btn-info" @click="okImage()">{{trans('media-manager::media.ok')}}</button><br/><br/>
            <button v-if="image_status=='done'" type="button" class="btn btn-blue-grey" @click="okNew()">{{trans('media-manager::media.ok')}} {{trans('media-manager::media.and')}} {{trans('media-manager::media.new')}} {{trans('media-manager::media.image')}}</button><br/><br/>
        </div>
    </div>
</div>
