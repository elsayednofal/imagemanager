<?php
Route::group(['namespace' => 'Elsayednofal\Imagemanager', 'middleware' => config('image-manager.middlewares')], function() {

Route::post('media-manager/cat/add','Controllers\MediaController@addCat');
Route::post('media-manager/cat/update/{id}','Controllers\MediaController@updateCat');
Route::get('media-manager/cat/delete/{id}','Controllers\MediaController@deleteCat');
Route::get('media-manager/cat/fetch','Controllers\MediaController@fetchCats');

Route::post('media-manager/images/upload','Controllers\MediaController@uploadImage');
Route::post('media-manager/images/done','Controllers\MediaController@doneImage');
Route::get('media-manager/images/get','Controllers\MediaController@getImages');
Route::post('media-manager/images/delete','Controllers\MediaController@deleteImage');
Route::post('media-manager/images/get-by-ids','Controllers\MediaController@getImageByIds');
Route::get('media-manager/fly-compress','Controllers\MediaController@compressImage');

});

