<?php

Route::group(['namespace'=>'Elsayednofal\Imagemanager\Http\Controllers'],function(){
    
    Route::any('image-manager/upload','ImageManagerController@uplooadImage');
    Route::any('image-manager/done','ImageManagerController@doneImage');
    Route::get('image-manager/get-images/{keyword?}','ImageManagerController@getImages');
//     Route::post('image-manager/crop','ImageManagerController@cropImage');
});

