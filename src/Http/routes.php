<?php

Route::group(['namespace'=>'SayedNofal\ImageManager\Http\Controllers'],function(){
    
    Route::post('image-manager/upload','ImageManagerController@uplooadImage');
    Route::get('image-manager/get-images/{keyword?}','ImageManagerController@getImages');
});

