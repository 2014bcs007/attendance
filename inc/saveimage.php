<?php

//set random name for the image, used time() for uniqueness
$filename = time() . ".jpg";
//$filepath = 'students/';

$filepath = "students/";
$target_file = $filepath . basename($_FILES["std_photo"]["name"]);
move_uploaded_file($_FILES["std_photo"]["tmp_name"], $filepath.$filename);

//move_uploaded_file($_FILES['webcam']['tmp_name'], $filepath . $filename);
echo $filepath . $filename;
?>