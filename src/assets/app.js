$(document).ready(function () {
    var old_images = '';
    var single_image = false;
    var cats = [];
    var _token = $('meta[name="csrf-token"]').attr('content');
    //$('body').append($('#media-model'));
    _target = null;


    function blockDom(selector, message = '') {
        $(selector).block({
            css: {
                border: 'none',
                padding: '15px',
                backgroundColor: '#000',
                '-webkit-border-radius': '10px',
                '-moz-border-radius': '10px',
                opacity: .5,
                color: '#fff'
            },
            message: message
        });
    }

    function unBlockDom(selector) {
        $(selector).unblock();
    }

    $('#media-model').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        old_images = button.data('images');
        //images_app.fetchSelected(old_images);
        old_images = button.data('single');
    });

    $("body").on('click', '.media-selector', function () {
        var imgs = $(this).attr('data-images');
        images_app.$data.selected_ids = imgs;
        images_app.$data.next_page = 1;
        images_app.$data.complet = false;
        images_app.fetchSelected(imgs);
    });



    function copy(id) {
        var copyText = document.getElementById(id);
        copyText.select();
        document.execCommand("copy");
        alert("Copied the URL !");
    }

    //============================== categories area ===========================
    var cat_app = new Vue({
        el: '#media-setting',
        data: {
            categories: [],
            new_cat: '',
            edit: false,
            update_cat_name: null,
            update_cat_id: null
        },
        created() {
            //            $('#media-model').on('show.bs.modal', function (event) {
            //                this.fetchCats();
            //            }.bind(this));
            //cats=this.categories;
        },
        methods: {
            fetchCats() {
                //blockDom("#cats-table");
                //blockDom("#new-cat");
                var cats = this.categories;
                $.ajax({
                    url: './media-manager/cat/fetch',
                    success: function (response) {
                        response = jQuery.parseJSON(response);
                        this.categories = response.cats;
                        cats = response.cats;
                    }.bind(this)
                });
            },
            enableUpdateCat(cat) {
                blockDom("#cats-table");
                this.update_cat_name = cat.name;
                this.update_cat_id = cat.id;
                this.edit = true;
            },
            updateCat() {
                blockDom("#update-cat");
                $.ajax({
                    url: './media-manager/cat/update/' + this.update_cat_id,
                    method: "POST",
                    data: {
                        name: this.update_cat_name,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (res) {
                        res = jQuery.parseJSON(res);
                        if (res.status === 'ok') {
                            this.categories = res.cats;
                        } else {
                            alert('something went wrong');
                        }
                    }.bind(this)
                });
                this.edit = false;
                unBlockDom("#cats-table");
                unBlockDom("#update-cat");
            },
            addCat() {
                blockDom('#new-cat', media_trans.please_wait);
                var categories = this.categories;
                $.ajax({
                    url: './media-manager/cat/add',
                    method: "POST",
                    data: {
                        name: this.new_cat,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        response = jQuery.parseJSON(response);
                        if (response.status === 'ok') {
                            this.categories.unshift(response.cat);
                        } else {
                            alert('something went wrong');
                        }
                        unBlockDom('#new-cat');
                    }.bind(this)
                });
                this.new_cat = '';

            },
            cancleEdit() {
                this.update_cat = null;
                unBlockDom('#cats-table');
                this.edit = false;
            },
            deleteCat(cat) {
                blockDom("#cats-table", media_trans.processing + ' ...');
                $.ajax({
                    url: './media-manager/cat/delete/' + cat.id,
                    success: function (res) {
                        res = jQuery.parseJSON(res);
                        if (res.status === "ok") {
                            for (var i = 0; i < this.categories.length; i++) {
                                if (this.categories[i].id === cat.id) {
                                    this.categories.splice(i, 1);
                                }
                            }
                        } else {
                            alert("Something went wrong !");
                        }
                        unBlockDom("#cats-table");
                    }.bind(this)
                });

            }
            // update , add and cancle cat
        }
    });
    //========================== end categories area ===========================


    //========================== upload area ===================================
    var upload_app = new Vue({
        el: "#media-uploader",
        data: {
            categories: [],
            cat_id: "",
            image: null,
            alt: "",
            temp_image: null,
            done_image: null,
            image_name: null,
            target_cat: null,
            image_status: null //null , temp , done
        },
        created() {
            $(document).on('show.bs.modal', '#media-model', function (event) {
                var button = $(event.relatedTarget);
                this.target_cat = button.data('cat');
            }.bind(this));
        },
        methods: {
            fetchCats() {
                var cats = this.categories;
                $.ajax({
                    url: './media-manager/cat/fetch',
                    success: function (response) {
                        response = jQuery.parseJSON(response);
                        this.categories = response.cats;
                        cats = response.cats;
                        images_app.$data.categories = response.cats;
                    }.bind(this)
                });
            },
            done() {
                blockDom("#upload-result", media_trans.please_wait + ' ...');
                var cat = this.target_cat !== null ? this.target_cat : this.cat_id;
                $.ajax({
                    url: './media-manager/images/done',
                    method: "POST",
                    data: {
                        alt: this.alt,
                        cat_id: cat,
                        name: this.image_name,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (res) {
                        res = jQuery.parseJSON(res);
                        if (res.message === 'ok') {
                            this.image_status = 'done';
                            this.done_image = res.data;
                            images_app.$data.images.unshift(this.done_image);
                        }
                        unBlockDom('#upload-result');
                    }.bind(this),
                    fial: function () {
                        alert('somthing went wrong');
                        unBlockDom('#upload-result');
                    }
                });

            },
            upload() {
                if (this.alt === '') {
                    alert("please enter Image alt");
                    return false;
                }
                if (this.image === null) {
                    alert("please choose Image");
                    return false;
                }

                blockDom("#media-uploader", media_trans.uploading + ' ...');
                var form_data = new FormData();
                form_data.append('image', this.image);
                form_data.append('alt', this.alt);
                if (this.target_cat !== null)
                    form_data.append('cat_id', this.target_cat);
                else
                    form_data.append('cat_id', this.cat_id);

                form_data.append('_token', $('meta[name="csrf-token"]').attr('content'));
                $.ajax({
                    url: "./media-manager/images/upload",
                    cache: false,
                    contentType: false,
                    processData: false,
                    method: "POST",
                    data: form_data,
                    success: function (res) {
                        res = jQuery.parseJSON(res);
                        if (res.message === 'ok') {
                            this.temp_image = res.data;
                            this.image_name = res.data.name;
                            this.image_status = "temp";
                            blockDom("#upload_form", media_trans.upload_complete + ' , ' + media_trans.check_image_below);
                        } else if (res.message === "exist") {
                            this.done_image = res.data;
                            this.image_name = res.data.name;
                            this.image_status = "done";
                        } else {
                            alert('something Went Wrong');
                        }
                        unBlockDom("#media-uploader");
                    }.bind(this),
                    fial: function () {
                        alert('something Went Wrong');
                        unBlockDom("#media-uploader");
                    }
                });
            },
            processFile($event) {
                this.image = $event.target.files[0];
            },
            resetUploadForm() {
                this.image = null;
                this.alt = '';
                this.cat_id = '';
            },
            cancleTemp() {
                this.image = null;
                this.temp_image = null;
                this.image_status = null;
                this.image_name = null;
                this.resetUploadForm();
                unBlockDom("#upload_form");
            },
            okImage() {

                $("#images-tab").trigger('click');
                this.cancleTemp();
            },
            okNew() {
                this.cancleTemp();
            }
        }
    });
    //========================== end upload area ===============================


    //=========================== Images Area ==================================
    var images_app = new Vue({
        el: "#media-images",
        data: {
            images: [],
            selected_images: [],
            selected_ids: null,
            single_image: false,
            cat_id: '',
            target_cat: null,
            alt: '',
            next_page: 1,
            complet: false,
            target: null,
            categories: [],
            loading: true,
            name: ''
        },
        created() {
            $(document).on('show.bs.modal', '#media-model', function (event) {
                var button = $(event.relatedTarget);
                this.target = button;
                this.target_cat = button.data('cat');
                this.single_image = button.data('single');
                this.name = button.data('name');
                this.images = [];
                if (!this.target_cat)
                    this.fetchCats();
                console.log(this.next_page);
                this.next_page = 1;
                this.fetchImages();
            }.bind(this));
        },
        methods: {
            fetchCats() {
                var cats = this.categories;
                $.ajax({
                    url: './media-manager/cat/fetch',
                    success: function (response) {
                        response = jQuery.parseJSON(response);
                        this.categories = response.cats;
                        cats = response.cats;
                        upload_app.$data.categories = response.cats;
                        cat_app.$data.categories = response.cats;
                    }.bind(this)
                });
            },
            fetchImages() {
                if (this.next_page == 1) {
                    this.loading = true;
                }
                var cat = this.target_cat ? this.target_cat : this.cat_id;
                $.ajax({
                    url: './media-manager/images/get',
                    data: {
                        cat_id: cat,
                        alt: this.alt,
                        page: this.next_page,
                        ids: this.selected_ids
                    },
                    success: function (res) {
                        res = jQuery.parseJSON(res);
                        if (res.message === 'ok') {
                            if (this.next_page === 1) {
                                this.images = res.data.data;
                            } else {
                                this.images = this.images.concat(res.data.data);
                            }
                            if (res.data.last_page > this.next_page) {
                                this.next_page++;
                            } else {
                                this.complet = true;
                            }
                        }
                        this.loading = false;
                    }.bind(this),
                    fail: function () {
                        alert('something went wrong');
                        this.loading = false;
                    }
                });
            },
            scrollLoad(event) {
                const bottom = event.target.scrollHeight - event.target.scrollTop === event.target.clientHeight;
                if (bottom) {
                    if (this.complet) {
                        return false;
                    }
                    this.fetchImages();
                }
            },
            fetchSelected(selected_ids) {
                $.ajax({
                    url: "./media-manager/images/get-by-ids",
                    method: "POST",
                    data: {
                        _token: _token,
                        ids: selected_ids
                    },
                    success: function (res) {
                        res = jQuery.parseJSON(res);
                        if (res.message === "ok") {
                            this.selected_images = res.data;
                        }
                    }.bind(this)
                });
            },
            selectImage(image) {
                if (!this.in_arr(this.selected_images, image)) {
                    if (!this.single_image) {
                        this.selected_images.push(image);
                    } else {
                        this.selected_images = [image];
                    }
                } else {
                    var index = this.get_index(this.selected_images, image); //selected_images.indexOf(image);
                    if (index > -1) {
                        this.selected_images.splice(index, 1);
                    } else {}
                }

                var group = $("#sortable").sortable({
                    axis: 'y',
                    stop: function (event, ui) {
                        var children = $('#sortable').sortable('refreshPositions').children();
                        $.each(children, function () {});
                    }.bind(this)
                });
                $("#sortable").disableSelection();
            },
            removeImage(image) {
                var index = this.selected_images.indexOf(image);
                this.selected_images.splice(index, 1);
            },
            copy() {
                copy('image_url');
            },
            deleteImage(image) {
                $.ajax({
                    url: './media-manager/images/delete',
                    method: 'POST',
                    data: {
                        image_id: image.id,
                        _token: _token
                    },
                    beforeSend: function (xhr) {
                        blockDom("#media-model");
                    },
                    success: function (res) {
                        res = jQuery.parseJSON(res);
                        if (res.message === "ok") {
                            // remove form all images
                            var index = this.images.indexOf(image);
                            this.images.splice(index, 1);
                            // remove from selected
                            this.removeImage(image);
                        }
                        unBlockDom("#media-model");
                    }.bind(this).bind(image),
                    fail: function () {
                        alert('something went wrong !');
                        unBlockDom("#media-model");
                    },
                    always: function () {
                        unBlockDom("#media-model");
                    }
                });
            },
            save() {
                blockDom("#media-model");
                var target = this.target
                var name = this.name;
                target.closest('div.media-manager-group').find('.result').html('');
                $('#sortable').find('.on-img').each(function () {
                    var src = $(this).attr('src');
                    var val = $(this).attr('data-id');
                    var html = "<img src='" + src + "' class='media-image on-image' ><input type='hidden' name='" + name + "' value='" + val + "'/>";
                    target.closest('div.media-manager-group').find('.result').append(html);
                });
                var ids = [];
                for (var i = 0; i < this.selected_images.length; i++) {
                    ids.push(this.selected_images[i].id);
                }
                target.attr('data-images', ids);
                $('#media-model').modal('toggle');
                unBlockDom("#media-model");
            },
            in_arr(hystack, target) {
                for (var i = 0; i <= hystack.length; i++) {
                    if (JSON.stringify(target) === JSON.stringify(hystack[i])) {
                        return true;
                    }
                }
                return false;
            },
            get_index(hystack, target) {
                for (var i = 0; i <= hystack.length; i++) {
                    if (JSON.stringify(target) === JSON.stringify(hystack[i])) {
                        return i;
                    }
                }
                return -1;
            },
            resetSearch() {
                this.cat_id = '';
                this.alt = '';
                this.fetchImages();
            },
            search() {
                this.next_page = 1;
                this.fetchImages();
            }
        }
    });
    //======================== end Images Area ==================================
    var _URL = window.URL || window.webkitURL;
    $('.sfu').change(function () {
    

    var max_width=$(this).data('width');    
    var max_height=$(this).data('height');  
    var input=$(this);  
    var file, img;
    if ((file = this.files[0])) {
        img = new Image();
        var objectUrl = _URL.createObjectURL(file);
        img.onload = async function () {
            if(max_width != undefined && max_height!=undefined){
                if(this.width != max_width || this.height != max_height){
                    alert('Allowed width :'+ max_width+' and height : '+max_height);
                    return false;
                }
            }
            var file_data = input.prop('files')[0];
        var form_data = new FormData();
        form_data.append('image', file_data);
        input.next('img.img-loading').show();
        input.prev('label').hide();
        //var button = $(this);
        var target_div=input.data('target');
       // console.log('#'+target_div);
        target_div=$('#'+target_div);
        $.ajax({
            url: './media-manager/images/upload', // point to server-side PHP script 
            dataType: 'text', // what to expect back from the PHP script, if anything
            cache: false,
            contentType: false,
            processData: false,
            data: form_data,
            type: 'post',
            success: function (response) {
                var data = jQuery.parseJSON(response);

                if (data.status == 200) {
                    target_div.find('img.img-loading').hide();
                    target_div.find('label').show();
                    target_div.find('input.image-val').val(data.data.name);
                    target_div.find('img.img-preview').attr('src', data.data.path +
                        '/' + data.data.name).show();
                } else {
                    alert(data.message);
                    target_div.find('img.img-loading').hide();
                    target_div.find('label').show();
                }
            },
            error: function () {
                alert('something went wrong');
            }
        });



            _URL.revokeObjectURL(objectUrl);
        };
        img.src = objectUrl;
    }

    return false;

        var file_data = $(this).prop('files')[0];
        var form_data = new FormData();
        form_data.append('image', file_data);
        $(this).next('img.img-loading').show();
        $(this).prev('label').hide();
        var button = $(this);
        var target_div=$(this).data('target');
        console.log('#'+target_div);
        target_div=$('#'+target_div);
        $.ajax({
            url: './media-manager/images/upload', // point to server-side PHP script 
            dataType: 'text', // what to expect back from the PHP script, if anything
            cache: false,
            contentType: false,
            processData: false,
            data: form_data,
            type: 'post',
            success: function (response) {
                var data = jQuery.parseJSON(response);

                if (data.status == 200) {
                    target_div.find('img.img-loading').hide();
                    target_div.find('label').show();
                    target_div.find('input.image-val').val(data.data.name);
                    target_div.find('img.img-preview').attr('src', data.data.path +
                        '/' + data.data.name).show();
                } else {
                    alert(data.message);
                    target_div.find('img.img-loading').hide();
                    target_div.find('label').show();
                }
            },
            error: function () {
                alert('something went wrong');
            }
        });
    });

});
