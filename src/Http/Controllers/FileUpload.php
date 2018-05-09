<?php
namespace Elsayednofal\Imagemanager\Http\Controllers;


interface FileUpload {
    
    public function uploadFile($dest,$file_name,$src);
    
    public function getFullPath($path);
    
    public function moveFile($src,$dest);
    
    public function moveThumb($src,$dest);
    
    public function makDir($path);
    
}
