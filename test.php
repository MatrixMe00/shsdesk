<?php 
$image_directory = "/home/vol1_1/epizy.com/epiz_30746634/shsdesk.epizy.com/htdocs/admin/admin/assets/images/schools/IMG-20211016-WA0019.jpg";
//if file already exists, try to create another filename
if($image_directory != null){
    //split into directories
    $image_directory = explode("/", $image_directory);

    //get file directory path
    $file_directory = "";

    foreach ($image_directory as $row){
        if($row != end($image_directory))
            $file_directory .= "$row/";
    }

    //retrieve file name
    $filename = end($image_directory);

    //break name into name and extension
    $filename = explode(".",$filename);

    //reset the path
    $image_directory = $file_directory.$filename[0].".".$filename[1];

    //set a counter to count image number for new name
    $counter = 1;

    //create a new image name till unique name is formed
    while($counter <= 5){
        $image_directory = $file_directory.$filename[0]."_$counter.".$filename[1];
        
        $counter++;
    }

    //when loop is over, let the image be prepared for upload
    $uploadOk = 1;
}
?>