<div class="tab-pane fade" id="media-setting" role="tabpanel" aria-labelledby="contact-tab">
    <div style="margin:30px 0px 5px">
        <div class="row" id="new-cat" v-if="!edit">
            <div class="col-md-4">
                <input type="text" v-model="new_cat" class="form-control" placeholder="{{trans('media-manager::media.new')}} {{trans('media-manager::media.category')}} ....." />
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-primary"  @click="addCat()"><i class="fas fa-plus-circle text-light"></i> {{trans('media-manager::media.add')}}</button>
            </div>
        </div>
        <div class="row" id="update-cat" v-if="edit">
            <div class="col-md-4">
                <input type="text" v-model="update_cat_name" class="form-control" placeholder="enter new model" />
                <input type="hidden" v-model="update_cat_id" class="form-control" placeholder="enter new model" />
            </div>
            <div class="col-md-4">
                <button type="button" class="btn btn-primary" @click="updateCat()"><i class="fas fa-edit text-light"></i> {{trans('media-manager::media.update')}}</button>
                <button type="button" class="btn btn-danger" @click="cancleEdit()"><i class="fas fa-backspace text-light"></i> {{trans('media-manager::media.cancel')}}</button>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">

            <table class="table" id="cats-table">
                <tr class="bg-dark text-light">
                    <th>{{trans('media-manager::media.id')}}</th>
                    <th>{{trans('media-manager::media.category')}}</th>
                    <th>{{trans('media-manager::media.actions')}}</th>
                </tr>
                <tr v-for="(cat,index) in categories">
                    <td># @{{cat.id}}</td>
                    <td>@{{cat.name}}</td>
                    <td >
                        <button type="button" class="btn btn-outline-danger btn-sm" @click="deleteCat(cat)"><i class="fas fa-trash-alt"></i></button>
                        <button type="button" class="btn btn-outline-info btn-sm" @click="enableUpdateCat(cat)"><i class="fas fa-edit"></i></button>
                    </td>
                </tr>

            </table>
        </div>
    </div>
</div>