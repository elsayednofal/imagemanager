<?php

namespace Elsayednofal\Imagemanager\Http\Controllers\Filesystems;

use Elsayednofal\Imagemanager\Http\Controllers\FileUpload;

class S3 implements FileUpload {

    public function uploadFile($dest,$file_name,$src){
        $s3 = \Storage::disk('s3');
        $s3->put($dest.$file_name, file_get_contents($src), 'public');
    }
    

    public function getFullPath($path){
        return \Storage::url($path);
    }

    public function moveFile($src, $dest){
        $s3 = \Storage::disk('s3');
        $s3->move($src,$dest);
    }

    public function moveThumb($src, $dest){
        $s3 = \Storage::disk('s3');
        $s3->put($dest, file_get_contents($src),'public');
        unlink($dest);
    }
    
    public function makDir($path) {
        $file=new Local();
        $file->makDir($path);
    }
    
}
