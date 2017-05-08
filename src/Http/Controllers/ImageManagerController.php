<?php

namespace Elsayednofal\Imagemanager\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Elsayednofal\Imagemanager\Http\Helpers\Cropper;

class ImageManagerController extends Controller {
    
    
    public function uplooadImage(Request $request) {
        $response = new \stdClass();

        // check if client send image
        if (!$request->hasFile('image')) {
            $response->status = 400;
            $response->message = 'missing image file';
            $response->data = [];
            echo json_encode($response);
            die;
        }

        // check if file is valide image
        $image = $request->file('image');
        if (!in_array($image->extension(), config('ImageManager.alloweed_types'))) {
            $response->status = 400;
            $response->message = 'image type not allowed';
            $response->data = [];
            echo json_encode($response);
            die;
        }
        
        // check if image not duplicated
        $content=md5(file_get_contents($request->file('image')));
        if(is_object($image_mang=  \Elsayednofal\Imagemanager\Models\ImageManagerModel::where('content',$content)->first())){
            $response->status=200;
            $response->message = 'exist';
            $response->data=[
                'name'=>$image_mang->name,
                'path' => config('ImageManager.upload_path'),
                'id'=>$image_mang->id
            ];
            echo json_encode($response);
            die;
        }
        
        // upload the image
        if (!file_exists(config('ImageManager.upload_path'))) {
            mkdir(config('ImageManager.upload_path'), 0775);
        }
        if (!file_exists(config('ImageManager.upload_path') . '/temp' )) {
            mkdir(config('ImageManager.upload_path') . '/temp', 0755);
        }

        $fileName = sha1(random_int(1, 5000) *(float) microtime()) . '.' . $image->extension(); // renameing image
        $image->move(config('ImageManager.upload_path') . '/temp', $fileName);
        
        
        
        $response->status = 200;
        $response->message = 'ok';
        $response->data = [
                        'name' => $fileName,
                        'path' => config('ImageManager.upload_path').'/temp',
                        'id'   => -1
                ];
        echo json_encode($response);
        die;
    }
    
    
    function doneImage(Request $request){
        $src=  explode('/', $request->get('src'));
        $src=end($src);
        if (!file_exists(config('ImageManager.upload_path') . '/' . date('m-Y'))) {
            mkdir(config('ImageManager.upload_path') . '/' . date('m-Y'), 0755);
        }

        $fileName = $src; // renameing image
        rename('./'.config('ImageManager.upload_path').'/temp/'.$fileName, './'.config('ImageManager.upload_path').'/'.date('m-Y').'/'.$fileName);
        
        // applay thumbnails images
        $this->applyThumbs($fileName);
        
        $content=md5(file_get_contents('./'.config('ImageManager.upload_path').'/'.date('m-Y').'/'.$fileName));
        
        // save the image to database
        $image_manger=new \Elsayednofal\Imagemanager\Models\ImageManagerModel();
        $image_manger->name=date('m-Y') . '/' . $fileName;
        $image_manger->content=$content;
        $image_manger->alt=$request->get('alt');
        $image_manger->save();
        $response=new \stdClass();
        $response->status = 200;
        $response->message = 'ok';
        $response->data = [
                        'name' => date('m-Y') . '/' . $fileName,
                        'path' => config('ImageManager.upload_path'),
                        'id'   => $image_manger->id,
                        'alt'  => $image_manger->alt
                ];
        echo json_encode($response);
        die;
    }

    
    public function uplooadImage_old(Request $request) {
        $response = new \stdClass();

        // check if client send image
        if (!$request->hasFile('image')) {
            $response->status = 400;
            $response->message = 'missing image file';
            $response->data = [];
            echo json_encode($response);
            die;
        }

        // check if file is valide image
        $image = $request->file('image');
        if (!in_array($image->extension(), config('ImageManager.alloweed_types'))) {
            $response->status = 400;
            $response->message = 'image type not allowed';
            $response->data = [];
            echo json_encode($response);
            die;
        }
        
        // check if image not duplicated
        $content=md5(file_get_contents($request->file('image')));
        if(is_object($image_mang=  \Elsayednofal\Imagemanager\Models\ImageManagerModel::where('content',$content)->first())){
            $response->status=200;
            $response->message = 'exist';
            $response->data=[
                'name'=>$image_mang->name,
                'path' => config('ImageManager.upload_path'),
                'id'=>$image_mang->id
            ];
            echo json_encode($response);
            die;
        }
        
        // upload the image
        if (!file_exists(config('ImageManager.upload_path'))) {
            mkdir(config('ImageManager.upload_path'), 0775);
        }
        if (!file_exists(config('ImageManager.upload_path') . '/' . date('m-Y'))) {
            mkdir(config('ImageManager.upload_path') . '/' . date('m-Y'), 0755);
        }

        $fileName = sha1(random_int(1, 5000) *(float) microtime()) . '.' . $image->extension(); // renameing image
        $image->move(config('ImageManager.upload_path') . '/' . date('m-Y'), $fileName);
        
        // applay thumbnails images
        $this->applyThumbs($fileName);
        
        // save the image to database
        $image_manger=new \Elsayednofal\Imagemanager\Models\ImageManagerModel();
        $image_manger->name=date('m-Y') . '/' . $fileName;
        $image_manger->content=$content;
        $image_manger->alt=$request->get('alt');
        $image_manger->save();
        
        $response->status = 200;
        $response->message = 'ok';
        $response->data = [
                        'name' => date('m-Y') . '/' . $fileName,
                        'path' => config('ImageManager.upload_path'),
                        'id'   => $image_manger->id
                ];
        echo json_encode($response);
        die;
    }

    private function makeThumb($src, $dest, $desired_width,$desired_height) {
	/* read the source image */
       $ext = exif_imagetype($src);

        if ($ext == '1') //GIF
            $source_image = imagecreatefromgif($src);
        elseif ($ext == "2") //jpg
            $source_image = imagecreatefromjpeg($src);
        elseif ($ext == "3") //png
            $source_image = imagecreatefrompng($src);

       
	//$source_image = imagecreatefromjpeg($src);
	$width = imagesx($source_image);
	$height = imagesy($source_image);
	
	/* find the "desired height" of this thumbnail, relative to the desired width  */
        if ($desired_height==0) {
            $desired_height = floor($height * ($desired_width / $width));
        }
	
	/* create a new, "virtual" image */
	$virtual_image = imagecreatetruecolor($desired_width, $desired_height);
	
	/* copy source image at a resized size */
	imagecopyresampled($virtual_image, $source_image, 0, 0, 0, 0, $desired_width, $desired_height, $width, $height);
	
	/* create the physical thumbnail image to its destination */
	imagejpeg($virtual_image, $dest);
    }
    
    function selector($name,$images_ids=[],$multi=TRUE){
        if(!ends_with($name, '[]')){
            $name.='[]';
        }
        $images=[];
        if(count($images_ids)>0){
            $images=  \Elsayednofal\Imagemanager\Models\ImageManagerModel::whereIn('id',$images_ids)->get();
        }
        return view('ImageManager::selector',['name'=>$name,'multi'=>$multi,'images'=>$images]);
    }
    
    function getImages($keyword=''){
        $images=new \Elsayednofal\Imagemanager\Models\ImageManagerModel();
        if($keyword!=''){
            $images=$images->where('alt','like',"%$keyword%")->orderBy('id','desc')->paginate(10);
        }else{
            $images=$images->orderBy('id','desc')->paginate(10)->toArray();
        }
        //$images->upload_path=  config('ImageManager.upload_path');
        $upload_path=config('ImageManager.upload_path');
        if(config('ImageManager.enable_thumbs')){
            $upload_path.='/thumbs';
        }
        
        
        $response=[
            'status'=>200,
            'data'=>$images,
            'upload_path'=>$upload_path
        ];
       // dd($response);
        echo json_encode($response);die;
        
        return response()->json($images,200)->header('Content-Type', 'application/json');
    }
    
    function getImagePath($id,$size=''){
        $image=\Elsayednofal\Imagemanager\Models\ImageManagerModel::find($id);
        if(!is_object($image)){
            return false;
        }
        //dd(file_exists(config('ImageManager.upload_path').'/'.$size.'/'.$image->name));
        if($size=='thumb' && config('ImageManager.enable_thumbs')){
            $path='thumbs/';
        }else if($size=='small' && config('ImageManager.enable_small_thumbs')){
            $path='small/';
        }else if($size!='' && file_exists(config('ImageManager.upload_path').'/'.$size.'/'.$image->name)){
            $path=$size.'/';
        }else{
            $path='';
        }
        return url(config('ImageManager.upload_path').'/'.$path.$image->name);
    }
    
    
    function applyThumbs($fileName){
        
        // per defined thumnails
        if(config('ImageManager.enable_thumbs') & count(config('ImageManager.thumb_size'))>0 ){
            if (!file_exists(config('ImageManager.upload_path').'/thumbs')) {
                mkdir(config('ImageManager.upload_path').'/thumbs', 0775);
            }       
            if (!file_exists(config('ImageManager.upload_path').'/thumbs/'.date('m-Y'))) {
                mkdir(config('ImageManager.upload_path').'/thumbs/'.date('m-Y'), 0775);
            }       
            $this->makeThumb(config('ImageManager.upload_path') . '/' . date('m-Y').'/'.$fileName, config('ImageManager.upload_path') . '/thumbs/' . date('m-Y').'/'.$fileName, config('ImageManager.thumb_size')[0], config('ImageManager.thumb_size')[1]);
        }
        if(config('ImageManager.enable_small_thumbs') & count(config('ImageManager.small_thumbs_size'))>0 ){
            if (!file_exists(config('ImageManager.upload_path').'/small')) {
                mkdir(config('ImageManager.upload_path').'/small', 0775);
            }       
            if (!file_exists(config('ImageManager.upload_path').'/small/'.date('m-Y'))) {
                mkdir(config('ImageManager.upload_path').'/small/'.date('m-Y'), 0775);
            }
            $this->makeThumb(config('ImageManager.upload_path') . '/' . date('m-Y').'/'.$fileName, config('ImageManager.upload_path') . '/small/' . date('m-Y').'/'.$fileName, config('ImageManager.small_thumbs_size')[0], config('ImageManager.small_thumbs_size')[1]);
        }
        
        //custom thumnails
        if(count(config('ImageManager.custom_thumbs'))==0)return FALSE;
        
        foreach(config('ImageManager.custom_thumbs') as $key=>$value){
            // check if value is array of width and hights
            if(count($value)==0)continue;
            if (!file_exists(config('ImageManager.upload_path').'/'.$key)) {
                mkdir(config('ImageManager.upload_path').'/'.$key, 0775);
            }
            if (!file_exists(config('ImageManager.upload_path').'/'.$key.'/'.date('m-Y'))) {
                mkdir(config('ImageManager.upload_path').'/'.$key.'/'.date('m-Y'), 0775);
            }
            $this->makeThumb(config('ImageManager.upload_path') . '/' . date('m-Y').'/'.$fileName, config('ImageManager.upload_path') . '/'.$key.'/' . date('m-Y').'/'.$fileName, $value[0], $value[1]);
        }
        
    }
    
    function cropImage(Request $request){
        $type = exif_imagetype($request->get('src'));
        $extension = image_type_to_extension($type);
        $fileName=$fileName = sha1(random_int(1, 5000) *(float) microtime()). $extension;
        $dst=config('ImageManager.upload_path').'/temp';
        $data=  json_decode($request->get('data'));
        $rotate=$data->rotate;
        $flipX=$data->scaleX;
        $flipY=$data->scaleY;
        $crop_data=['x'=>$data->x,'y'=>$data->y,'width'=>$data->width,'height'=>$data->height];
//        $cropper=new Cropper();
//        $cropper->work($request->get('src'), $dst.'/'.$fileName, $request->get('data'), $type, $extension);
        $this->Crop($request->get('src'), $dst.'/'.$fileName, $type,$crop_data, $rotate, $flipX, $flipY);
        $response=new \stdClass();
        $response->status = 200;
        $response->message = 'ok';
        $response->data = [
                        'name' => $fileName,
                        'path' => $dst,
                        'id'   => -1
                ];
        echo json_encode($response);
        die;
    }
    
    private function Crop($src,$dst,$type,$crop_data,$rotate,$flipX,$flipY){
        try{
            switch ($type) {
                case IMAGETYPE_GIF:
                    $src_img = imagecreatefromgif($src);
                    break;

                case IMAGETYPE_JPEG:
                    $src_img = imagecreatefromjpeg($src);
                    break;

                case IMAGETYPE_PNG:
                    $src_img = imagecreatefrompng($src);
                    break;
            }
            
            if($flipX!=1){
                imageflip($src_img, IMG_FLIP_HORIZONTAL);
            }
            if($flipY!=1){
                imageflip($src_img, IMG_FLIP_VERTICAL);
            }
            if($rotate!=0){
                $src_img=  imagerotate($src_img, $rotate, 0);
            }
            $src_img = imagecrop($src_img, $crop_data);
            switch ($type) {
                case IMAGETYPE_GIF:
                    imagegif($src_img, $dst);
                    break;

                case IMAGETYPE_JPEG:
                    imagejpeg($src_img, $dst);
                    break;

                case IMAGETYPE_PNG:
                    imagepng($src_img, $dst);
                    break;
            }
            return true;
        }  catch (\Exception $ex){
            return false;
        }        
    }
    
}
