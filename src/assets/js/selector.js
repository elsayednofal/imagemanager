
<script type="text/javascript">

$(document).ready(function(){
    var number_of_selected_images=0;
    var get_images_url='<?=url('image-manager/get-images')?>';
    
    getImages();
    
    // auto scroll choose images
    $('.image_manger_images-container').scroll(function() {
        if ($(this).scrollTop() + $(this).innerHeight() >= $(this)[0].scrollHeight) {
            if (get_images_url === null){
                return false;
            }
            getImages();
        }
    });
    
    // get images after search
    $('.image_manger_search_button').click(function(){
        
        $('.image_manger_image').remove();
        var keyword=$(this).closest('.tab-content').find('input.image_manger_search').val();
        $('input.image_manger_search').val(keyword);
        if(keyword.length>0){
            get_images_url='<?=url('image-manager/get-images')?>/'+keyword;
        }else{
            get_images_url='<?=url('image-manager/get-images')?>';
        }
        getImages();
    });
    
    // select and unselect action on image
    $(document).on('click', '.image_manger_image', function () {   
        if($(this).attr('data-select')==='1'){
            unSelectImage(this);
            $(this).closest('div.image_manger_choose').attr('data-count',0);
        }else{
            var is_multi=$(this).closest('div.image_manger_choose').attr('data-multi');
            var selected_count=$(this).closest('div.image_manger_choose').attr('data-count');
            if(is_multi==='0'){
                if(selected_count==='0'){
                    selectImage(this);
                     $(this).closest('div.image_manger_choose').attr('data-count',1);
                }else{
                    alert('you must choose only one Image');
                }
            }else{
                selectImage(this);
                $(this).closest('div.image_manger_choose').attr('data-count',1);
            }
            
        }
        
        // show and hide save buuton 
        if(number_of_selected_images>0){
            $('.image_manger_save').show();
        }else{
            $('.image_manger_save').hide();
        }
        //show and hide edit button
        if(number_of_selected_images==1){
            $('.image_manger_edit').show();
        }else{
            $('.image_manger_edit').hide();
        }
        
    });
    
    //edit selected image
    $('.image_manger_edit').click(function(){
        var modal_div=$(this).closest('div.modal');
        var image_div=modal_div.find('.image_manger_image[data-select="1"]');
        image_div.trigger('click');
        var image=image_div.find('img');
        var src=image.attr('src');
        var alt=image.attr('alt');
        $('div.form_upload').find('.upload_alt').val(alt);
        $('.show_uploaded_image').html('<img id="edit_image" class="edit-image-manger-img" src="'+src+'" style="max-width:1000px" />');
        showActions('exist');
        $('.image_manger_upload_button').trigger('click');
        $(this).hide();
        $('.upload_edite').first().trigger('click');
        
    });
    
    
    // save and apply result
    $('.image_manger_save').click(function(){
        var modal_div=$(this).closest('div.modal');
        var images=modal_div.find('.image_manger_image[data-select="1"]');
        var varibale_name=$(this).attr('data-name');
       // modal_div.prev('.image_manger_inputs').html('');
       var append=$(this).attr('data-append');
        images.each(function(){
            var image_src=$(this).find('img').attr('src');
            var image_id =$(this).attr('data-id');
            
            $('#'+append).append(''+
                '<div class="image_manager_image_container" style="position:relative;border:1px solid;border-color:green;display:inlinr-block;margin:5px;float:left">'+
                    '<img src="'+image_src+'"  />'+
                    '<img class="image_manger_delete_image" src="<?=url('vendor/SayedNofal/ImageManager/images/close.png')?>" style="width:20px;position:absolute;left:-3px;top:-5px;cursor:pointer" />'+
                    '<input type="hidden" value="'+image_id+'" name="'+varibale_name+'" />'+
                '</div>'
            );
            unSelectImage(this);
        });
        $('.image_manger_image[data-select="1"]').each(function(){
            unSelectImage(this);
        });
        $('.image_manger_edit').hide();
        $('.close_image_manger').trigger('click');
        $(window).resize();
    });
    
    // remove added images
    $(document).on('click','.image_manger_delete_image',function(){
        var modal_id=$(this).closest('.image_manger_inputs').attr('data-modal')
        var is_multi=$('#'+modal_id).find('div.image_manger_choose').attr('data-multi');
        
        if(is_multi=='0'){
            $('#'+modal_id).find('div.image_manger_choose').attr('data-count',0);
        }
        $(this).closest('div.image_manager_image_container').remove();
    });
    
    // submit upload new image
    $('.submit_upload').click(function(){
        $(this).closest('div.form_upload').find('alert').remove();
        var files=$(this).closest('div.form_upload').find('.upload_image_input').prop('files')
        var alt=$(this).closest('div.form_upload').find('.upload_alt').val();
        console.log('+++>'+$(this).closest('div.form_upload').find('.upload_alt'));
        console.log('====>'+alt);
        if(files.length==0){
            $(this).closest('div.form_upload').prepend('<div class="alert alert-danger alert-dismissible" role="alert">'+
                                        '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>you must select image.</div>');
                                return false;
        }
        if(alt==''){
            $(this).closest('div.form_upload').prepend('<div class="alert alert-danger alert-dismissible" role="alert">'+
                                        '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>you must set image alt.</div>');
                                return false;
        }
       
        
        uploadImage('<?=url("image-manager/upload")?>',files[0],alt);
    });
    
    // Done uploaded images and move to select area
    $(document).on('click','.upload_done',function(){
        var images_resut=$(this).closest('div.image_manger_upload').find('div.show_uploaded_image')
        var img=images_resut.find('img');
        var src=img.attr('src');
        var alt=$(this).closest('div.image_manger_upload').find('.upload_alt').val();
        
        var action_area=$(this).closest('div.image_manger_upload').find('.uploaded-image-action-area');
        $.ajax({
            url: './image-manager/done?src='+src+'&alt='+alt, // point to server-side PHP script 
            dataType: 'text',  // what to expect back from the PHP script, if anything
            cache: false,
            contentType: false,
            processData: false,
            type: 'post',
            beforeSend: function (xhr) {
                action_area.html('<br style="clear:both"/><img class="image_manager_loader" src="<?=url('vendor/SayedNofal/ImageManager/images/loader.gif')?>" width="100"/>');
            },
            success: function(response){
                var data=jQuery.parseJSON(response);
                console.log(response)
                if(data.status==200){
                    img.attr('data-id',data.data.id);
                    img.attr('src',data.data.path+'/'+data.data.name);
                    img.attr('alt',data.data.alt);
                    $('.image_manger_choose .image_manger_images-container .row').prepend('<div class="col-md-2 col-sm-4 image_manger_image" data-id="'+data.data.id+'" ><img src="'+data.data.path+'/'+data.data.name+'" alt="'+data.data.alt+'" /></div>');
                    showActions('done');
                }
            }
        });
        
    });
    
    // applay uploaded images and move to select area
    $(document).on('click','.upload_select',function(){
        var images_resut=$(this).closest('div.image_manger_upload').find('div.show_uploaded_image');
        var img=images_resut.find('img');
        var src=img.attr('src');
        var id=img.attr('data-id');
       // $('.image_manger_choose .image_manger_images-container .row').prepend('<div class="col-md-2 col-sm-4 image_manger_image" data-id="'+id+'" ><img src="'+src+'" /></div>');
        if($('.image_manger_image[data-id="'+id+'"]').length>0){
            $('.image_manger_image[data-id="'+id+'"]').first().trigger('click');
        }else{
            $('.image_manger_choose .image_manger_images-container .row').prepend('<div class="col-md-2 col-sm-4 image_manger_image" data-id="'+id+'" ><img src="'+src+'"  /></div>');
            $('.image_manger_image[data-id="'+id+'"]').first().trigger('click');
        }
        $('.image_manger_choose_button').trigger('click');
        
        $('.show_uploaded_image').html('');
        var action_area=$('.uploaded-image-action-area').html('');
        $('.form_upload input').each(function(){
            $(this).val('');
        });
    });
    
    // remove temp image and clear upload area
    $(document).on('click','.upload_remove',function(){
        if(!confirm('are you sure? you want to remove the image')){
            return false;
        }
        
        $(this).closest('div.image_manger_upload').find('div.show_uploaded_image').html('');
        $('.uploaded-image-action-area').html('');
        $('.form_upload input').each(function(){
            $(this).val('');
        });
    });
    
    // select image actions
    function selectImage(image){
        $(image).addClass('active').attr('data-select','1');
        number_of_selected_images++;
    }
    
    // unselect image action
    function unSelectImage(image){
        $(image).removeClass('active').attr('data-select','0');
        number_of_selected_images--;
    }
    
    // get images funcction
    function getImages(){
        $.ajax({
           url:get_images_url,
           method:'get',
           beforeSend: function (xhr) {
                $('.image_manger_choose').append('<br style="clear:both"/><img class="image_manager_loader" src="<?=url('vendor/SayedNofal/ImageManager/images/loader.gif')?>" width="100"/>')
            },
            error: function (jqXHR, textStatus, errorThrown) {
                $('.image_manger_choose').html('<br/><span style="color:red">some thing went wrong, try to refresh the page<span>');
            },  
            complete: function (jqXHR, textStatus ) {
              $('.image_manger_choose').find('.image_manager_loader').prev().remove();              
              $('.image_manger_choose').find('.image_manager_loader').remove();                
            },        
           success:function(response){
               response=jQuery.parseJSON(response);
               if(!response.status=='ok'){
                   alert('something went wrong , try to refresh the page');
                   return false;
               }
               get_images_url=response.data.next_page_url;
               for(var i in response.data.data){
                    $('.image_manger_choose .image_manger_images-container .row').append('<div class="col-md-2 col-sm-4 image_manger_image" data-id="'+response.data.data[i].id+'" ><img src="'+response.upload_path+'/'+response.data.data[i].name+'" alt="'+response.data.data[i].alt+'"/></div>');
               }  
           }


       }); 
    }
    
    // handel upload image
    function uploadImage(url,files,alt){
        var file_data = files;   
        var form_data = new FormData();                  
        form_data.append('image', file_data);
        form_data.append('alt', alt);
        //alert(form_data); 
        $('.upload-loading').show();
        $('.actions_buttons').hide();
        $('.show_uploaded_image').html();
        $.ajax({
            url: url, // point to server-side PHP script 
            dataType: 'text',  // what to expect back from the PHP script, if anything
            cache: false,
            contentType: false,
            processData: false,
            data: form_data,                         
            type: 'post',
            success: function(response){
                var data=jQuery.parseJSON(response);
                console.log(data);
                if(data.status===200){
                    $('.upload-loading').hide();
                    $('.show_uploaded_image').html('<img id="edit_image" src="'+data.data.path+'/'+data.data.name+'" data-id="'+data.data.id+'" style="max-width:1000px" />')
                    
                    if(data.message=='exist'){
                       showActions('exist');
                    }else{
                        showActions('new');
                    }
                }else{
                    $('.show_uploaded_image').html('<div class="alert alert-danger alert-dismissible" role="alert">'+
                                        '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>'+
                                        ''+data.message+'.</div>');
                    $('.upload-loading').hide();
                                    
                }
            }
        });
    }
    
    function showActions(type){
        $('.uploaded-image-action-area').html('');
        if(type=='new'){
            $('.uploaded-image-action-area').html($('#button-component').html());
            $('.uploaded-image-action-area').find('.upload_select').hide();
        }else if(type=='exist'){
            $('.uploaded-image-action-area').html($('#button-component').html());
            //$('.component').first().find('#button-component').clone().appendTo('.uploaded-image-action-area');
            $('.uploaded-image-action-area').find('.upload_remove').hide();
            $('.uploaded-image-action-area').find('.upload_done').hide();
        }else if(type=='done'){
            $('.uploaded-image-action-area').html('<button class="btn btn-success upload_select" >Select</button>');
        }else if(type=='edit'){
            $('.uploaded-image-action-area').html('<button class="btn btn-danger cancle-edit">Cancle Edit</button>\n\
                                                   <br/><br/>\n\
                                                   <button class="btn btn-info save-edit">Save Edit</button>');
        }else if(type=='loading'){
            $('.uploaded-image-action-area').html('<br style="clear:both"/><img class="image_manager_loader" src="<?=url('vendor/SayedNofal/ImageManager/images/loader.gif')?>" width="100"/>');
        }
    }
    
    
    //##################### cropper js Actions ############################//
    /* enable edite*/
    $(document).on('click','.upload_edite',function(){
        $('.component').find('.cropper-buttons').first().clone().appendTo('.show_uploaded_image');
        $('.edit-image-manger-img').cropper('enable');
        $('.edit-image-manger-img').cropper('crop');
        showActions('edit');
    }); 
    
    $(document).on('click','#rotate-left',function(){
        $('.edit-image-manger-img').cropper('rotate', 45);
    });
    
    $(document).on('click','#rotate-right',function(){
        $('.edit-image-manger-img').cropper('rotate', - 45);
    });
     
    $(document).on('click','.cancle-edit',function(){
        if(!confirm('Are you want to cancle edit?'))return false;
        $('.edit-image-manger-img').cropper("clear");
        $('.edit-image-manger-img').cropper("destroy");
        $('.show_uploaded_image').find('.cropper-buttons').remove();
        showActions('new');
    });
    
    $(document).on('click','#scalex',function(){
        var x = $(this).attr('data-x');
        $('.edit-image-manger-img').cropper('scaleX', x);
        $(this).attr('data-x', x * - 1);
    });
    
    $(document).on('click','#scaley',function(){
        var y = $(this).attr('data-y');
        $('.edit-image-manger-img').cropper('scaleY', y);
        $(this).attr('data-y', y * - 1);
    });
    
    // save cropper
    $(document).on('click','.save-edit',function(){
        var img=$(this).closest('div.image_manger_upload').find('img.edit-image-manger-img');
        var crop_data=JSON.stringify(img.cropper("getData"));
        var src=img.attr('src');
        $.ajax({
            url:'<?=url("image-manager/crop")?>',
            data:{'data':crop_data,'src':src},
            method:'post',
            beforeSend: function (xhr) {
                showActions('loading');
            },
            success: function (response) {
                data=jQuery.parseJSON(response);
                if(data.status==200){
                    $('.show_uploaded_image').html('<img id="edit_image" class="edit-image-manger-img" src="'+data.data.path+'/'+data.data.name+'" data-id="'+data.data.id+'" style="max-width:500px" />');
                    showActions('new');
                }else{
                    showActions('edit');
                    $('.uploaded-image-action-area').append('<br/><span style="color:red">Something went wrong , try again</span>');
                }
            }
           
        });
       
    });
    
    
});
</script>
