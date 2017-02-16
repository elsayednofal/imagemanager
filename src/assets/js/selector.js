
<script type="text/javascript">

$(document).ready(function(){
    var number_of_selected_images=0;
    var get_images_url='<?=url('image-manager/get-images')?>';
    
    getImages();
    
    $('.image_manger_images-container').scroll(function() {
        if ($(this).scrollTop() + $(this).innerHeight() >= $(this)[0].scrollHeight) {
            if (get_images_url === null){
                return false;
            }
            getImages();
        }
    });
    
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
    
    $(document).on('click', '.image_manger_image', function () {   
        console.log(this);
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
        
        if(number_of_selected_images>0){
            $('.image_manger_save').show();
        }else{
            $('.image_manger_save').hide();
        }
    });
    
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
                    '<img src="'+image_src+'" style="width:150px" />'+
                    '<img class="image_manger_delete_image" src="<?=url('vendor/SayedNofal/ImageManager/images/close.png')?>" style="width:20px;position:absolute;left:-3px;top:-5px;cursor:pointer" />'+
                    '<input type="hidden" value="'+image_id+'" name="'+varibale_name+'" />'+
                '</div>'
            );
            unSelectImage(this);
        });
        $('.close_image_manger').trigger('click');
        $(window).resize();
    });
    
    $(document).on('click','.image_manger_delete_image',function(){
        var modal_id=$(this).closest('.image_manger_inputs').attr('data-modal')
        var is_multi=$('#'+modal_id).find('div.image_manger_choose').attr('data-multi');
        
        if(is_multi=='0'){
            $('#'+modal_id).find('div.image_manger_choose').attr('data-count',0);
        }
        $(this).closest('div.image_manager_image_container').remove();
    });
    
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
    
    $('.upload_done').click(function(){
        var images_resut=$(this).closest('div.image_manger_upload').find('div.show_uploaded_image')
        console.log(images_resut);
        var img=images_resut.find('img');
        console.log(img);
        var src=img.attr('src');
        var id=img.attr('data-id');
        $('.image_manger_choose .image_manger_images-container .row').prepend('<div class="col-md-2 col-sm-4 image_manger_image" data-id="'+id+'" ><img src="'+src+'" /></div>');
        $('.image_manger_choose_button').trigger('click');
        $('.image_manger_image[data-id="'+id+'"]').trigger('click');
        
    });
    
    function selectImage(image){
        //$(image).css({'border':'5px solid','border-color': '#03A9F4'});
        $(image).addClass('active').attr('data-select','1');
        number_of_selected_images++;
    }
    
    function unSelectImage(image){
        //$(image).css({'border':'2px solid','border-color': '#00000'});
        $(image).removeClass('active').attr('data-select','0');
        number_of_selected_images--;
    }
    
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
                    $('.image_manger_choose .image_manger_images-container .row').append('<div class="col-md-2 col-sm-4 image_manger_image" data-id="'+response.data.data[i].id+'" ><img src="'+response.upload_path+'/'+response.data.data[i].name+'" /></div>');
               }  
           }


       }); 
    }
    
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
                    $('.show_uploaded_image').html('<img src="'+data.data.path+'/'+data.data.name+'" data-id="'+data.data.id+'" style="max-width:500px" />')
                   $('.upload_done').show();
                   $('.actions_buttons').show();
                   if(data.message=='exist'){
                       $('.upload_remove').remove();
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
    
    
    
});
</script>
