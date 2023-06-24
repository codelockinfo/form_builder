<?php
include dirname(dirname(__FILE__)) . "/append/connection.php";
$imageFolder = ABS_PATH . "/assets/images/";
$img_name = $_FILES['file']['name'];
$filetowrite = $imageFolder . $img_name;
move_uploaded_file($img_name, $filetowrite);
return $filetowrite;
?>