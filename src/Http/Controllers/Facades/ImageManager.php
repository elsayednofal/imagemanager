<?php
namespace SayedNofal\ImageManager\Http\Controllers\Facades;

use Illuminate\Support\Facades\Facade;

class ImageManager extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'SayedNofal\ImageManager\Http\Controllers\ImageManagerController'; }

}
