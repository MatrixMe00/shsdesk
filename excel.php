<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php require_once("PHPExcel.php");
    
    $path = "admin/admin/assets/files/default files/house_allocation.csv";
    $reader = PHPExcel_IOFactory::createReaderForFile($path);
    $excel_obj = $reader->load($path);

    //get data of first worksheet
    $worksheet = $excel_obj->getSheet('0');

    //print details
    echo $worksheet->getCell("A1")->getValue();
    
    ?>
</body>
</html>