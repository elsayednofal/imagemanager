<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Elsayednofal\Imagemanager\Models;

use Illuminate\Database\Eloquent\Model;
use Elsayednofal\Imagemanager\Controllers\FileSystem;

class MediaManager extends Model {

    protected $table = 'media_manager';
    protected $guarded = ['id'];
    protected $appends = ['url','rel_path'];
    private $file_sys;

    public function __construct() {
        if (!is_object($this->file_sys)) {
            $f_s = new FileSystem();
            $this->file_sys = $f_s->init();
        }
    }

    public function getUrlAttribute() {
        //return 'xyz';
        return $this->file_sys->getFullPath(config("ImageManager.upload_path") . "/" . $this->name);
    }
    
    public function getRelPathAttribute(){
        return config("ImageManager.upload_path")."/".$this->name;
    }
    
    public function getThumbRelPath($name){
        return config("ImageManager.upload_path")."/".$name."/".$this->name;
    }

}
