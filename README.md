# ImageManager
Image manger to mange upload and select image with one line of code

#Installation
run this command 
` composer require elsayednofal/imagemanager:dev-master `

  then 
add service provider to you app config in path config/app.php
` Elsayednofal\Imagemanager\ImageManagerServiceProvider::class ` 

and in alias add the line 

` 'ImageManager' => Elsayednofal\Imagemanager\Http\Controllers\Facades\ImageManager::class `

then run
` php artisan vendor:publish --force ` 
then run
` php artisan migrate `

 
 now lets use it 
 
#Config
you can  edit the package config in path config/ImageManager.php
you can set something like [upload_path,alloweed_types,enable_thumbs,......]
you will find a comment on every single config in the file
 
#Usage
one single line in your blade where you want uploader in your form :
```php  
 //images[] is the variable you will recieve the ids of selected or uploaded images in 
 <?= ImageManager::selector('images[]')?>
 ``` 
 
some cases like update you want to show old selected images so you can pass the ids as second prameter like :
 ```php  
 //images[] is the variable you will recieve the ids of selected or uploaded images in 
 <?= ImageManager::selector('images[]',[10,15,17])?>
 ``` 
in some cases you want the user to select just one image :
  ```php  
 //images[] is the variable you will recieve the ids of selected or uploaded images in 
 // [] represent selected ids
 // false => means only one image can be set
 <?= ImageManager::selector('images[]',[],false)?>
 ```
 
#Support
 for any questions contact me at : `elsayed_nofal@ymail.com`
 
