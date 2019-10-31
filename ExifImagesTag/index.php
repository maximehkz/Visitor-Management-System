    <?php
$dir='./Images/';

$files = scandir($dir);
//jpeg,png,jpg,svg
$ext_list= ['jpeg', 'png', 'jpg', 'svg', 'gif' ];

    foreach($files as $image_file)
{
        $l= strtolower($image_file);
        $parse_file_name= explode(".",$l);
        $file_ext = end($parse_file_name);

        if(in_array($file_ext, $ext_list) )
        {
            $exif_data=exif_read_data($dir.$image_file);
            $photos[] = [
                'FileName'             => $exif_data['FileName'],
                'Model'                => $exif_data['Model'],
                'DateTimeOriginal'     => $exif_data['DateTimeOriginal'],
                'GPSversion'           => $exif_data['GPSversion'],
                'GPSLatitude'          => $exif_data['GPSLatitude'],
                'GPSLongitude'         => $exif_data['GPSLongitude'],
            ];

        }
}
print "<pre>";
print_r($photos);
print"</pre>";
exit;
/*
[FileName]
[Model]
[Exposuretime]
[Fnumber]
[ISOSpeedRatings]
[Focalength]
[DateTimeOriginal]
[GPSversion]
[GPSLatitude]
[GPSLongitude]
*/
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <link rel = "stylesheet" href="./css/register.css">
    <link rel = "stylesheet" href="./css/reset.css">
    <link rel = "stylesheet" href="./css/global.css">
    <link rel = "stylesheet" href="./css/app.css">
    <title>EXIF practice </title>
</head>
<body>
    <div class="main-container">

	<div class="input-container">
	<div class="Thumbnail">
        <img src="<?php echo $photos['FileName']; ?>" alt="<?php echo $photos['FileName']; ?>" class="img-responsive">
        <div class="caption">
        <ul>
            <li>
                <label> File Name: </label>
                <?php echo $photos['FileName']; ?>
            </li>
            <li>
                <label> Model: </label>
                <?php echo $photos['Model']; ?>
            </li>

            <li>
                <label> Date and Time picture taken:</label>
                <?php echo $photos['DateTimeOriginal']; ?>
            </li>
            <li>
                <label> GPS Version: </label>
                <?php echo $photos['GPSversion']; ?>
            </li>
            <li>
                <label> GPS Latitude: </label>
                <?php echo $photos['GPSLatitude']; ?>
            </li>
            <li>
                    <label> GPS Longitude: </label>
                <?php echo $photos['GPSLongitude']; ?>
             </li>
        </ul>
    </div>
	</div>
    </div>
</body>
</html>
