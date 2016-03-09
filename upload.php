<?php
$msg = '';//Message to send back via json
$status = 'failed';
$target_dir = "project/";
$target_file = $target_dir . basename($_FILES["file"]["name"]);
$uploadOk = 1;
$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
// Check if image file is a actual image or fake image
if(isset($_POST["submit"])) {
    $uploadOk = 1;
}
// Check if file already exists
if (file_exists($target_file)) {
    $msg .= "Sorry, file already exists.";
    $uploadOk = 0;
}
// Check file size
if ($_FILES["file"]["size"] > 50000) {
    $msg .= "Sorry, your file is too large.";
    $uploadOk = 0;
}
// Allow certain file formats
if($imageFileType != "pdf" && $imageFileType != "doc" && $imageFileType != "docx" && $imageFileType != "ppt" ) {
    $msg .= "Sorry, only PDF, DOC, DOCX & PowerPoint files are allowed.";
    $uploadOk = 0;
}
// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
    $msg .= "Sorry, your file was not uploaded.";
// if everything is ok, try to upload file
} 
else {
    if (copy($_FILES["file"]["tmp_name"], $target_file)) {
        $msg .= "The file ". basename( $_FILES["file"]["name"]). " has been uploaded.";
        $status = 'ok';
    } else {
        $msg .= "Sorry, there was an error uploading your file.";
    }
}

echo json_encode(array('status' => $status, 'Filename'=> basename($_FILES["file"]["name"]), 'message' => $msg));