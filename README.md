# ImageManager
Image manger to mange upload and select image with one line of code

## Installation
- run this command 
` composer require elsayednofal/imagemanager:3.0`
- add service provider to you app config in path config/app.php
` Elsayednofal\Imagemanager\ImageManagerServiceProvider::class ` 
- in alias add the line 
` 'ImageManager' => Elsayednofal\Imagemanager\Controllers\MediaController::class,`
- run command
` php artisan vendor:publish ` 
- run command
` php artisan migrate `
 

## Config
  - edit configration from file config/image-manager
	- you can change view from backend/image-manager
 
 
## Usage
1- add `{!! ImageManager::loadAssets() !!}` befor </body> close
2- add `  {!! ImageManager::loadModal() !!}` after </body>

### Selector
- one single line in your blade where you want uploader in your form :
```php  
 //images[] is the variable you will recieve the ids of selected or uploaded images in 
 {!! ImageManager::selector('images[]') !!}
 ``` 
 
- some cases like update you want to show old selected images so you can pass the ids as second prameter like :
 ```php  
 //images[] is the variable you will recieve the ids of selected or uploaded images in 
 {!! ImageManager::selector('images[]',[10,15,17])?>
 ``` 
 
 - some cases you want the user to select just one image :
  ```php  
 //images[] is the variable you will recieve the ids of selected or uploaded images in 
 // [] represent selected ids
 // false => means only one image can be set
 {!! ImageManager::selector('images[]',[],false)?>
 ```
 How to get the value of selected or uploaded image ?
  The ImageManger::selector() inject input hidden with value of ids of selcted images 
  
- Retrive Image (display image )
   ```php
   ImageManager::getImagePath($id,$size='')
   id image id
   size can be '' for orginal size ,
               'thumb' for thumbnial and
               'small' for small image

   <img src="{{ImageManager::getImagePath($activity->mainImage->image_id,'small')}}" />
    ```
### single Image uploader
- one file upload
```{!! ImageManager::ImageUploader(['name'=>'logo'])!!}```

- upload and update old
```{!! ImageManager::ImageUploader(['name'=>'logo','image'=>$logo])!!}```

- upload file musr have width and hight
```{!! ImageManager::ImageUploader(['name'=>'logo','image'=>$logo,'width'=>160,'height'=>160])!!}```
 
## Support
 For any questions contact me at : `elsayed_nofal@ymail.com`
 
