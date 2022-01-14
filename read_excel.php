<?php 
    require_once("includes/session.php");

    //call autoload
    require($rootPath."/PhpSpreadSheet/autoload.php");

    //load IOFactory class
    use PhpOffice\PhpSpreadsheet\IOFactory;

    //load spreadsheet class
    use PhpOffice\PhpSpreadsheet\Spreadsheet;

    if(isset($_REQUEST["submit"]) && $_REQUEST["submit"] != null){
        $submit = $_REQUEST["submit"];

        if($submit == "upload" || $submit == "upload_ajax"){
            if(isset($_FILES['import']) && $_FILES["import"]["tmp_name"] != NULL){
                //retrieve file extension
                $ext = strtolower(fileExtension("import"));

                if($ext == "xls" || $ext == "xlsx"){
                    //identify file type Automatically
                    $file_type = IOFactory::identify($_FILES["import"]["tmp_name"]);

                    //create a reader
                    $reader = IOFactory::createReader('Xlsx');

                    //create a spreadsheet instance
                    $spreadsheet = $reader->load($_FILES["import"]["tmp_name"]);
                    
                    //get the working sheet
                    $sheet = $spreadsheet->getActiveSheet();
                    
                    //get maximum cell number
                    $max_column = $sheet->getHighestDataColumn();
                    $max_row = $sheet->getHighestDataRow();

                    //create a general column header
                    $all_col_names = array("A","B","C","D","E","F",
                                            "G","H","I","J","K","L",
                                            "M","N","O","P","Q","R",
                                            "S","T","U","V","W","X",
                                            "Y","Z");
                    
                    //create columns for cell number operations
                    $current_col_names = array();

                    //count the headers
                    $headerCounter = 0;

                    //set a variable ready for values beyond z, aa - zz
                    $beyond_z = 0;

                    while($all_col_names[$headerCounter%count($all_col_names)]){
                        if($headerCounter <= count($all_col_names)){
                            $current_col_names[$headerCounter] = $all_col_names[$headerCounter%count($all_col_names)];

                            //break here
                            if($current_col_names[$headerCounter] == $max_column){
                                break;
                            }
                        }else{
                            $current_col_names[$headerCounter] = $all_col_names[$beyond_z].$all_col_names[$headerCounter%count($all_col_names)];
                            
                            //increment left label only if the right label is z
                            if($all_col_names[$headerCounter%count($all_col_names)] == end($all_col_names)){
                                $beyond_z++;
                            }

                            //break here
                            if($current_col_names[$headerCounter] == $max_column){
                                break;
                            }
                        }

                        $headerCounter++;
                    }

                    //function to check for merged cells
                    function checkMergedCells($sheet, $cell){
                        foreach ($sheet->getMergeCells() as $row){
                            if($cell->isInRange($row)){
                                //cell is merged
                                return "true";
                            }
                        }

                        return "false";
                    }

                    //display content
                    
                    /*//variable to write data from file
                    $writer = IOFactory::createWriter($spreadsheet, "Html");

                    //parse result into html
                    $result = $writer->save("php://output");

                    echo $result;*/
                }else{
                    $message = "extension-error";
                }
            }else{
                echo "no-file";
                exit(1);
            }
        }
    }
?>