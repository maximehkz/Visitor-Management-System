<?php

namespace App\Http\Controllers;

use App\Models\userImage;
use Illuminate\Http\Request;

class exifValidation extends Controller
{
//function to store the image once uploaded
  public function store (Request $request) {

   $this->validate([

      'image' => ['mimes:jpeg,png,jpg,svg','required'],

   ]);

   if(Auth::check()){

     $name = str_random(30);

     \Image::make($request->image)->save(public_path('images/').$name);

      userImage::create([
        'user_id' => Auth::id(),
        'image' => $name;
      ]);

      return response()->json(['status'=>'updated'],201);

   }
   else{
     return response()->json(['status'=>'not authenticated'],400);
   }

//Once already stored image, then in your controller for extracting exif data or your function inside the controller this function controller applies:
function validateExif(){
{
$reader = \PHPExif\Reader\Reader::factory(\PHPExif\Reader\Reader::TYPE_NATIVE);

$exif = $reader->read('/storage/user1.png');

echo $exif->getTitle(); // this will echo out user1 since thats the title of the image

}

}


//this function might be returning the entire exif data of the file 
/**

*alias to read method
*
*@param string $file
*@return \PHPExif\Exif Instance of Exif Object with Data
*/

public function getEXifFromFile($file)

{
    return $this -> read($file);
}


}
