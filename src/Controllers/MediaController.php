<?php

namespace Elsayednofal\Imagemanager\Controllers;

use App\Http\Controllers\Controller;
use Elsayednofal\Imagemanager\Models\MediaCats;
use Illuminate\Http\Request;
use Elsayednofal\Imagemanager\Controllers\FileSystem;
use Elsayednofal\Imagemanager\Models\MediaManager;

class MediaController extends Controller {

    static function selector($name, $imgs = [], $multi = true, $category = null) {
        if(is_string($category)){
            $cat= MediaCats::where('name',$category)->first();
            $data['cat_id'] = is_object($cat)?$cat->id:null;
        }else{
            $data['cat_id'] = $category;
        }
        
        //if(!strstr($name, '[') && !strstr($name, ']')){
        if(!strstr($name, '[]')){
            $name.='[]';
        }
            
        $data['name'] = $name;
        $data['imgs'] = $imgs;
        $data['single'] = !$multi;
        return view('backend.image-manager.selector', $data);
    }
    
    static function loadAssets(){
        return view('backend.image-manager.assets');
    }
    
    static function loadModal(){
        return view('backend.image-manager.modal');
    }        
    

    //======================== single file ====================================
    static function ImageUploader(array $config){
        if(isset($config['image']) && $config['image']!=''){
            $config['display']=url(config('media-manager.upload_path').'/'.$config['image']);
        }
        return view('backend.image-manager.file-uploader')->with('config',$config);
    }

    static function moveTempImage($fileName){

        $file_system = new FileSystem();
        $file_system = $file_system->init();

        $file_system->makDir(config('media-manager.upload_path') . '/' . date('m-Y'));

        $file_system->moveFile(config('media-manager.upload_path') . '/temp/' . $fileName, config('media-manager.upload_path') . '/' . date('m-Y') . '/' . $fileName);
        return date('m-Y').'/'.$fileName;
    }

    static function getImageUrl($image){
        $file_system=new FileSystem();
        $file_system=$file_system->init();

        return $file_system->getFullPath(config('media-manager.upload_path').'/'.$image);
    }

    //************************* end single file ******************************* */

    //==================== category section ====================================
    function addCat(Request $request) {
        $cat = MediaCats::create(['name' => $request->name]);
        $response = new \stdClass();
        $response->status = 'ok';
        $response->cat = $cat;
        return response(json_encode($response));
    }

    function updateCat(Request $request, $id) {
        $cat = MediaCats::findOrFail($id)->update(['name' => $request->name]);
        $response = new \stdClass();
        $response->status = 'ok';
        $response->cats = MediaCats::orderBy('id', 'desc')->get()->toArray();
        return response(json_encode($response));
    }

    function fetchCats(Request $request) {
        $cats = MediaCats::orderBy('id', 'desc')->get()->toArray();
        $response = new \stdClass();
        $response->status = 'ok';
        $response->cats = $cats; //dd($response);
        return response(json_encode($response));
    }

    function deleteCat($id) {
        MediaCats::find($id)->delete();
        $response = new \stdClass();
        $response->status = 'ok';
        return response(json_encode($response));
    }

    // ************************ end category section ***************************

    function uploadImage(Request $request) {
        $response = new \stdClass();
        if (!$request->hasFile('image')) {
            $response->status = 400;
            $response->message = 'missing image file';
            $response->data = [];
            echo json_encode($response);
            die;
        }

        // check if file is valide image
        $image = $request->file('image');
        if (!in_array($image->extension(), config('media-manager.alloweed_types'))) {
            $response->status = 400;
            $response->message = 'image type not allowed';
            $response->data = [];
            echo json_encode($response);
            die;
        }

        $file_system = new FileSystem();
        $file_system = $file_system->init();

        $file_system->makDir(config('media-manager.upload_path'));
        $file_system->makDir(config('media-manager.upload_path') . '/temp');

        $filePath = config('media-manager.upload_path') . '/temp/';
        $fileName = sha1(random_int(1, 5000) * (float) microtime()) . '.' . $image->extension(); // renameing image

        $file_system->uploadFile($filePath, $fileName, $image);

        // check if image not duplicated
        $arrContextOptions=[
            "ssl"=>[
                "verify_peer"=>false,
                "verify_peer_name"=>false,
        ],
            ]; 
        $content = md5(file_get_contents($file_system->getFullPath($filePath . $fileName),false,stream_context_create($arrContextOptions)));
        if (is_object($image_mang = MediaManager::where('content', $content)->where('cat_id',$request->cat_id)->first())) {
            $response->status = 200;
            $response->message = 'exist';
            $response->data = [
                'name' => $image_mang->name,
                'path' => $file_system->getFullPath(config('media-manager.upload_path')),
                'id' => $image_mang->id
            ];
            echo json_encode($response);
            die;
        }

        $response->status = 200;
        $response->message = 'ok';
        $response->data = [
            'name' => $fileName,
            'path' => $file_system->getFullPath(config('media-manager.upload_path') . '/temp'),
            'id' => -1
        ];
        echo json_encode($response);
        die;
    }

    function doneImage(Request $request) {
        
        $fileName = $request->name; // renameing image


        $file_system = new FileSystem();
        $file_system = $file_system->init();

        $file_system->makDir(config('media-manager.upload_path') . '/' . date('m-Y'));

        $file_system->moveFile(config('media-manager.upload_path') . '/temp/' . $fileName, config('media-manager.upload_path') . '/' . date('m-Y') . '/' . $fileName);


        // applay thumbnails images
        $this->applyThumbs($fileName);

        $content = md5(file_get_contents($file_system->getFullPath(config('media-manager.upload_path') . '/' . date('m-Y') . '/' . $fileName)));

        // save the image to database
        $media_manger = new MediaManager();
        $media_manger->name = date('m-Y') . '/' . $fileName;
        $media_manger->content = $content;
        $media_manger->alt = $request->get('alt');
        $media_manger->cat_id = $request->get('cat_id');
        $media_manger->save();
        $response = new \stdClass();
        $response->status = 200;
        $response->message = 'ok';
        $response->data = [
            'name' => date('m-Y') . '/' . $fileName,
            'path' => $file_system->getFullPath(config('media-manager.upload_path')),
            'id' => $media_manger->id,
            'alt' => $media_manger->alt,
            'cat_id' => $media_manger->cat_id
        ];
        return json_encode($response);
        //die;
    }

    function applyThumbs($fileName) {

        $file_system = new FileSystem();
        $file_system = $file_system->init();
        // per defined thumnails
        if (config('media-manager.enable_thumbs') & count(config('media-manager.thumb_size')) > 0) {
            $file_system->makDir(config('media-manager.upload_path') . '/thumbs');
            $file_system->makDir(config('media-manager.upload_path') . '/thumbs/' . date('m-Y'));
            $this->makeThumb($file_system->getFullPath(config('media-manager.upload_path') . '/' . date('m-Y') . '/' . $fileName), config('media-manager.upload_path') . '/thumbs/' . date('m-Y') . '/' . $fileName, config('media-manager.thumb_size')[0], config('media-manager.thumb_size')[1]);
        }
        if (config('media-manager.enable_small_thumbs') & count(config('media-manager.small_thumbs_size')) > 0) {
            $file_system->makDir(config('media-manager.upload_path') . '/small');
            $file_system->makDir(config('media-manager.upload_path') . '/small/' . date('m-Y'));

            $this->makeThumb($file_system->getFullPath(config('media-manager.upload_path') . '/' . date('m-Y') . '/' . $fileName), config('media-manager.upload_path') . '/small/' . date('m-Y') . '/' . $fileName, config('media-manager.small_thumbs_size')[0], config('media-manager.small_thumbs_size')[1]);
        }

        //custom thumnails
        if (count(config('media-manager.custom_thumbs')) == 0)
            return FALSE;

        foreach (config('media-manager.custom_thumbs') as $key => $value) {
            // check if value is array of width and hights
            if (count($value) == 0)
                continue;
            $file_system->makDir(config('media-manager.upload_path') . '/' . $key);
            $file_system->makDir(config('media-manager.upload_path') . '/' . $key . '/' . date('m-Y'));
            $this->makeThumb($file_system->getFullPath(config('media-manager.upload_path') . '/' . date('m-Y') . '/' . $fileName), config('media-manager.upload_path') . '/' . $key . '/' . date('m-Y') . '/' . $fileName, $value[0], $value[1]);
        }
    }

    function makeThumb($src, $dest, $desired_width, $desired_height) {
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
        if ($desired_height == 0) {
            $desired_height = floor($height * ($desired_width / $width));
        }

        /* create a new, "virtual" image */
        $virtual_image = imagecreatetruecolor($desired_width, $desired_height);

        /* copy source image at a resized size */
        imagecopyresampled($virtual_image, $source_image, 0, 0, 0, 0, $desired_width, $desired_height, $width, $height);

        /* create the physical thumbnail image to its destination */
        //imagejpeg($virtual_image, $dest);
        if ($ext == '1') //GIF
            imagegif($virtual_image, $dest);
        elseif ($ext == "2") //jpg
            imagejpeg($virtual_image, $dest, 80);
        elseif ($ext == "3") { //png
            $white = imagecolorallocatealpha($virtual_image, 255, 255, 255, 127);
            imagefill($virtual_image, 0, 0, $white);
            imagepng($virtual_image, $dest, 9);
        }

        // applay to s3
        $file_system = new FileSystem();
        $file_system = $file_system->init();
        $file_system->moveThumb($dest, $dest);
    }
    
    function getImages(Request $request){
        $images=new MediaManager;
        
        if($request->ids){
            $sort=  $request->ids;
            $images=$images->orderByRaw('FIELD(id, '.$sort.') desc , id desc');
        }
            
        if($request->alt!=null){
            $images=$images->where('alt','like','%'.$request->alt.'%');
        }
        if($request->cat_id!=null){
            $images=$images->where('cat_id',$request->cat_id);
        }
        $images=$images->paginate(25);
        
        $response=new \stdClass();
        $response->message="ok";
        $response->data=$images->toArray();
        return json_encode($response);
    }
    
    function deleteImage(Request $request){
        $image= MediaManager::find($request->image_id);
        $file_sys=(new FileSystem())->init();
        
        // delete thumb image
        $file_sys->delete($image->getThumbRelPath('thumbs'));
        // delete small image
        $file_sys->delete($image->getThumbRelPath('small'));
        // delete other thumbs
        foreach(config('media-manager.custom_thumbs') as $key=>$value){
            $file_sys->delete($image->getThumbRelPath($key));
        }
        // delete main image
        $file_sys->delete($image->rel_path);
        // delete from database
        $image->delete();
        
        // return response
        $res=new \stdClass();
        $res->message="ok";
        return json_encode($res);
    }
    
    function getImageByIds(Request $request){
        $ids= explode(',',$request->ids);
        $images= MediaManager::whereIn('id',$ids)->get();
        $response=new \stdClass();
        $response->message="ok";
        $response->data=$images->toArray();
        return json_encode($response);
    }
    
    public static function getImagePath($id,$size=''){
        $image= MediaManager::find($id);
        if(!is_object($image)){
            return false;
        }
        $file_system=new FileSystem();
        $file_system=$file_system->init();
        
        //dd(file_exists(config('media-manager.upload_path').'/'.$size.'/'.$image->name));
        if($size=='thumb' && config('media-manager.enable_thumbs')){
            $path='thumbs/';
        }else if($size=='small' && config('media-manager.enable_small_thumbs')){
            $path='small/';
        }else if($size!='' && $this->URL_exists($file_system->getFullPath(config('media-manager.upload_path').'/'.$size.'/'.$image->name))){
            $path=$size.'/';
        }else{
            $path='';
        }
        if(config('media-manager.fly_compress')){
            $path=$file_system->getFullPath(config('media-manager.upload_path').'/'.$path.$image->name);
            return url('./media-manager/fly-compress?src='.$path);
        }
        return $file_system->getFullPath(config('media-manager.upload_path').'/'.$path.$image->name);
    }

    public function compressImage(Request $request,$save_file_to=null){
        $src=$request->src;
        $ext = exif_imagetype($src);

        if ($ext == '1') //GIF
            $source_image = imagecreatefromgif($src);
        elseif ($ext == "2") //jpg
            $source_image = imagecreatefromjpeg($src);
        elseif ($ext == "3") //png
            $source_image = imagecreatefrompng($src);
        
        
        if ($ext == '1'){ //GIF
            header('Content-Type: image/gif');
            imagegif($source_image,$save_file_to);
        }elseif ($ext == "2"){ //jpg
            header('Content-Type: image/jpeg');
            imagejpeg($source_image,$save_file_to,60);
        }elseif ($ext == "3"){ //png
            header('Content-Type: image/png');
            imagesavealpha($source_image, true);
            imagealphablending($source_image, false);
            # important part two
            $white = imagecolorallocatealpha($source_image, 255, 255, 255, 127);
            imagefill($source_image, 0, 0, $white);
            imagepng ($source_image, $save_file_to, 9);
        }
        
        imagedestroy($source_image);
        
    }
    
}
