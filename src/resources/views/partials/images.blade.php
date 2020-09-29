<div class="tab-pane fade show active" id="media-images" role="tabpanel" aria-labelledby="home-tab">
    <div class="loading" v-if="loading">
        <img src="{{url('vendor/elsayednofal/image-manager/loader.gif')}}" />
        <h3>{{trans('media-manager::media.loading')}} {{trans('media-manager::media.images')}} .....</h3>
    </div>
    <div v-if="!loading">
        
        <div class="row search-box">
            <div class="col-md-3" v-if="!target_cat">
                <div class="form-group">
                    <select id="cat_id" class="form-control" v-model="cat_id">
                        <option value="" selected="">{{trans('media-manager::media.choose')}} {{trans('media-manager::media.category')}}</option>
                        <option v-for="cat in categories" v-bind:value="cat.id">@{{cat.name}}</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <input type="text" class="form-control" id="alt" v-model="alt" placeholder="{{trans('media-manager::media.search')}} {{trans('media-manager::media.with')}} {{trans('media-manager::media.alt')}}"/>
                </div>
            </div>
            <div class="col-md2">
                <div class="form-group">
                    <button type="button" class="btn btn-primary" @click="search()" title="{{trans('media-manager::media.search')}}"><i class="fas fa-search"></i></button>
                    <button type="button" class="btn btn-bitbucket" @click="resetSearch()" title="{{trans('media-manager::media.reset')}}"><i class="fas fa-undo"></i></button>
                </div>
            </div>
        </div>
        
        <div class="row" id="media-manager-content" v-on:scroll="scrollLoad" style="position: relative">
            <div v-bind:class="selected_images.length > 0 ? 'col-md-9':'col-md-12'">
                <img v-for='image in images' v-bind:src="'{{config('media-manager.upload_path')}}/thumbs/'+image.name" v-bind:data-id="image.id" v-bind:class="['media-image',{'image-selected' : in_arr(selected_images,image) } ]" v-bind:xx='in_arr(selected_images,image)' @click="selectImage(image)" />
            </div>
            <div v-if="selected_images.length>0" class="col-md-3 selected-area">
                <div class="row m" v-if="selected_images.length==1">
                    <div class="input-group mt-1 mr-1 ml-1">
                        <input type="text" id="image_url"  readonly="" class="form-control" placeholder="Recipient's username" v-bind:value="'{{url(str_replace('./','',config('media-manager.upload_path')))}}/'+selected_images[0].name" aria-describedby="button-addon2">
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="button" id="copy" @click="copy()"><i class="fas fa-copy text-primary"></i></button>
                        </div>
                    </div>
                    <div class="form-group mx-auto mt-1 mr-1 ml-1">
                        <button type="button" class="btn btn-danger" @click="deleteImage(selected_images[0])">{{trans('media-manager::media.delete')}} <i class="fas fa-trash-alt"></i></button>
                    </div>
                    <br/>
                </div>
                <div class="row">
                    <ul id="sortable">
                        <li v-for="(image,index) in selected_images" v-bind:data-index="index" v-bind:class="[{one_image:selected_images.length==1},'ui-state-default sort-image']">
                            <img v-bind:data-id="image.id"  v-bind:src="'{{config('media-manager.upload_path')}}/'+image.name" v-bind:data-index="index" v-bind:class="['media-image on-img']" />
                            <i class="far fa-times-circle text-light bg-danger remove-image" @click="removeImage(image)" ></i>
                        </li>
                    </ul>
                </div>
                <div class="row">
                    <div class="form-group mx-auto mt-1 mr-1 ml-1">
                        <button type="button" class="btn btn-primary" @click="save()" >{{trans('media-manager::media.save')}}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

