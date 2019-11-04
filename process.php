<?php
$dir = './image/uploads/';
$files = scandir($dir);

$ext_list= ['jpeg', 'png', 'jpg', 'svg', 'gif' ];

foreach($files as $image_file)
{
    $l = strtolower($image_file);
    $parse_file_name = explode(".", $l);
    $file_ext = end($parse_file_name);

    if(in_array($file_ext, $ext_list) )
    {
        $exif_data = exif_read_data($dir.$image_file);
        $photos[] = [
            'FileName'          => $exif_data['FileName'],
            'Model'             => $exif_data['Model']? $exif_data['Model'] : "missing",
            'DateTimeOriginal'  => $exif_data['DateTimeOriginal']? $exif_data['DateTimeOriginal'] : "missing",
        ];
    }
}

/*
[FileName]
[Model]
[ExposureTime] => 1/60
[FNumber] => 5/1
[ISOSpeedRatings]
[FocalLength]
 */
?>

<?PHP foreach($photos as $data): ?>
   <div class="col-xs-12 col-sm-6 col-md-4 col-lg-3">
     <div class="thumbnail">
         <img src="<?PHP echo $dir.$data['FileName'];?>" alt="<?PHP echo $data['FileName'];?>" class="img-responsive">
         <div class="caption">
             <ul>
                 <li><label>FileName:</label><?PHP echo $data['FileName'];?></li>
                 <li><label>Model:</label><?PHP echo $data['Model'];?></li>
                 <li> <label> Date and Time picture taken:</label> <?php echo $data['DateTimeOriginal']; ?> </li>
             </ul>
         </div>
     </div>
   </div>
<?PHP endforeach;?>
