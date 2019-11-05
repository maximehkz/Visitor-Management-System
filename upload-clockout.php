<?php

require_once("core/init.php");

if(Input::getfiles("image")["name"]){
   $file = Input::getfiles("image");
   $filename = $file["name"];
   $filedir = "./image/uploads/$filename"; //this is where the image get stored
   if(move_uploaded_file(Input::getfiles("image")["tmp_name"], $filedir)){
          $exif_data = exif_read_data($filedir);

          $date = explode(" ",$exif_data['DateTimeOriginal']);
          $curTime = date("h:i:s");
          $begin = "00:00:00";
          $end = "23:55:00";
          if($date[0] == date("Y:m:d")){
              if($date[1]>$begin && $date[1]<$end){
                  $id = Session::get(Config::get("session/session_name"));
                  $user = new User();
                  if($user->find($id)){
                      if($user->data()->type == "guard"){
                          $user->guardClockOut();
                          Redirect::to("exifout_correct.php");
                      }
                  }
                  // Redirect::to("exifin_correct.php");
              }else{
                  Redirect::to("exifout_correct.php");
              }
          }else{
              Redirect::to("exifout_wrong.php");
          }
   }else{
       echo "There is an error uploading the photo";
   }


  // date("Y-m-d h:i:sa")

  }

?>
