<?php
namespace Elsayednofal\Imagemanager\Controllers;

use Elsayednofal\Imagemanager\Controllers\Filesystems\Local;
use Elsayednofal\Imagemanager\Controllers\Filesystems\S3;

class FileSystem {
    public function __construct() {
        
    }
    
    function init(){
        switch (env('FILESYSTEM_DRIVER', 'local')) {
            case 'local':
                 return (new Local());
            case 's3':
                 return (new S3());
            default:
                return (new Local());
        }
    }
}
