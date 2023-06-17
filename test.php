<head>
    <title>Test Area</title>
</head>
<?php 
include_once("includes/session.php");
$ad_gender = "Female";
$shs_placed = 3;
$ad_index = "0312001201";

/*//count number of houses of the school
$sql = "SELECT id
FROM houses
WHERE schoolID = $shs_placed AND gender = '$ad_gender' OR gender='Both'";
$result = $connect->query($sql);

//create an array for details
$house = array();

if($result->num_rows > 0){
$total = $result->num_rows;
$count = 0;

//fill array
while($row = $result->fetch_assoc()){
    $house[$count] = $row["id"];
    $count++;
}

//search for last house allocation entry
$sql = "SELECT indexNumber, houseID
    FROM house_allocation
    WHERE schoolID = $shs_placed
    ORDER BY indexNumber DESC
    LIMIT 1";
$result = $connect->query($sql);

$hid = $result->fetch_assoc()["houseID"];

//fetch student details for entry
// $student_details = fetchData("*", "cssps", "indexNumber=$ad_index");

if($result->num_rows == 1){
    //retrieve house id
    $id = $hid;

    $next_room = 0;

    for($i = 0; $i < $total; $i++){
        //try choosing the next house
        if($house[$i] == $id){
            //check immediate available houses
            if($i+1 < $total){
                $next_room = $house[$i+1];
            }elseif($i-1 >= 0){                                            
                $next_room = $house[$i-1];
            }elseif($i+1 == $total){
                $next_room = $house[0];
            }
            
            if($next_room > 0){
                break;
            }
        }
    }

    echo "next room: $next_room<br>
    Last Room: $id";
}
}*/

//     if($result->num_rows == 1){
                        //         //retrieve last house id given out
                        //         $id = $hid;
    
                        //         //set variable to receive next allocating house
                        //         $next_room = 0;
    
                        //         for($i = 0; $i < $total; $i++){
                        //             //try choosing the next or current house
                        //             if($house[$i]["id"] == $id){
                        //                 //fetch totals
                        //                 if($i+1 < $total){
                        //                     $nid = $house[$i+1]["id"];
                        //                     $ttl = fetchData("COUNT(indexNumber) AS total", "house_allocation", "schoolID=$shs_placed AND houseID=$nid AND boardingStatus='Boarder'")["total"];
                        //                 }elseif($i-1 < $total){
                        //                     $nid = $house[$i-1]["id"];
                        //                     $ttl = fetchData("COUNT(indexNumber) AS total", "house_allocation", "schoolID=$shs_placed AND houseID=$nid AND boardingStatus='Boarder'")["total"];
                        //                 }elseif($i+1 == $total){
                        //                     $nid = $house[$i+1]["id"];
                        //                     $ttl = fetchData("COUNT(indexNumber) AS total", "house_allocation", "schoolID=$shs_placed AND houseID=$nid AND boardingStatus='Boarder'")["total"];
                        //                 }
    
                        //                 //check immediate available houses
                        //                 if($i+1 < $total && $ttl < $house[$i+1]["totalHeads"]){
                        //                     $next_room = $house[$i+1]["id"];
                        //                 }elseif($i-1 >= 0 && $ttl < $house[$i-1]["totalHeads"]){                                            
                        //                     $next_room = $house[$i-1]["id"];
                        //                 }elseif($i+1 == $total && $ttl < $house[0]["totalHeads"]){
                        //                     $next_room = $house[0]["id"];
                        //                 }
                                        
                        //                 if($next_room > 0){
                        //                     break;
                        //                 }
                        //             }
                        //         }
    
                        //         //parse entry into allocation table
                        //         $sql = "INSERT INTO house_allocation (indexNumber, schoolID, studentLname, studentOname, houseID, studentGender, boardingStatus)
                        //             VALUES(?,?,?,?,?,?,?)";
                        //         $stmt = $connect->prepare($sql);
                        //         $stmt->bind_param("sississ", $student_details["indexNumber"], $student_details["schoolID"], $student_details["Lastname"], $student_details["Othernames"],
                        //         $next_room, $student_details["Gender"], $student_details["boardingStatus"]);
                        //         $stmt->execute();
                        // }elseif($result->num_rows == 0){
                        //         //this is the first entry, place student into house
                        //         $sql = "INSERT INTO house_allocation (indexNumber, schoolID, studentLname, studentOname, houseID, studentGender, boardingStatus)
                        //             VALUES(?,?,?,?,?,?,?)";
                        //         $stmt = $connect->prepare($sql);
                        //         $stmt->bind_param("sississ", $student_details["indexNumber"], $student_details["schoolID"], $student_details["Lastname"], $student_details["Othernames"],
                        //             $house[0]["id"], $student_details["Gender"], $student_details["boardingStatus"]);
                        //         $stmt->execute();
                        //     }
                        // }

//count number of houses of the school
/*$sql = "SELECT id, maleHeadPerRoom, maleTotalRooms, femaleHeadPerRoom, femaleTotalRooms
FROM houses
WHERE schoolID = $shs_placed AND (gender = '$ad_gender' OR gender='Both')";
$result = $connect->query($sql);

//create an array for details
$house = array();

if($result->num_rows > 0){
$total = $result->num_rows;

while($row = $result->fetch_assoc()){
    if(strtolower($ad_gender) == "male"){
        $new = array(
            array(
                "id" => $row["id"],
                "totalHeads" => intval($row["maleHeadPerRoom"]) * intval($row["maleTotalRooms"])
            )                                    
        );
    }else{
        $new = array(
            array(
                "id" => $row["id"],
                "totalHeads" => intval($row["femaleHeadPerRoom"]) * intval($row["femaleTotalRooms"])
            )                                    
        );
    }
    

    //add to house array
    $house = array_merge($house, $new);
}

foreach ($house as $row=>$val){
    echo $val["id"]."j ";
}

//fetch student details for entry from cssps
$student_details = fetchData("*", "cssps", "indexNumber='$ad_index'");

}*/

/*function setHouse($ad_gender, $shs_placed, $ad_index, $house){
    global $connect;
    $next_room = 0;

    $total = count($house);

    //get last index number to successfully register
    $last_student = fetchData("indexNumber","enrol_table","shsID=$shs_placed AND gender='$ad_gender' ORDER BY enrolDate DESC");

    if(is_array($last_student)){
        $last_student = $last_student["indexNumber"];

        //search for last house allocation entry for this gender
        $sql = "SELECT houseID
            FROM house_allocation
            WHERE indexNumber='$last_student'";
        $result = $connect->query($sql);

        $hid = $result->fetch_assoc()["houseID"];

        if(str_contains())

        echo "Last index: $last_student";

        if($result->num_rows == 1){
            //retrieve last house id given out
            $id = $hid;
            $next_room = 0;
    
            for($i = 0; $i < $total; $i++){
                //try choosing the next or current house
            
                if($house[$i]["id"] == $id){
                    while(!$next_room){
                        if($i+1 == $total){
                            $nid = $house[$i]["id"];
                            $ttl = fetchData("COUNT(indexNumber) AS total", "house_allocation", "schoolID=$shs_placed AND houseID=$nid AND boardingStatus='Boarder'")["total"];
                        }elseif($i+1 < $total){
                            $nid = $house[$i+1]["id"];
                            $ttl = fetchData("COUNT(indexNumber) AS total", "house_allocation", "schoolID=$shs_placed AND houseID=$nid AND boardingStatus='Boarder'")["total"];
                        }elseif($i-1 < $total){
                            $nid = $house[$i-1]["id"];
                            $ttl = fetchData("COUNT(indexNumber) AS total", "house_allocation", "schoolID=$shs_placed AND houseID=$nid AND boardingStatus='Boarder'")["total"];
                        }

                        //check immediate available houses
                        if($i+1 < $total && $ttl < $house[$i+1]["totalHeads"]){
                            $next_room = $house[$i+1]["id"];
                        }elseif($i-1 < 0 && $ttl < $house[$total-1]["totalHeads"]){
                            $next_room = $house[$total-1]["id"];
                        }elseif($i-1 >= 0 && $ttl < $house[$i-1]["totalHeads"]){                                            
                            $next_room = $house[$i-1]["id"];
                        }elseif($i+1 == $total && $ttl < $house[0]["totalHeads"]){
                            $next_room = $house[0]["id"];
                        }

                        $i++;

                        //start from 0 if end is reached but no house
                        if($i+1 == $total-1 && !$next_room){
                            $i = 0;
                        }
                    }
                }

                if($next_room > 0){
                    break;
                }
            }
    
            echo "<br>prev room: $hid<br>next room: $next_room";
        }
    }

    return $next_room;
}*/

/*$room = setHouse($ad_gender,$shs_placed,$house, "boarder");

if(is_null($room)){
    echo "A null value was returned";
}elseif($room > 0){
    echo "room $room was provided";
}else{
    echo "A new room has to be provided";
}*/

// echo getTotalMoney(3,3);
/*$_REQUEST['submit'] = "exeat_request";
$student_index = "0012345621";
$exeat_town = "Nungua";
$exeat_type = "external";

// include_once($rootPath."/sms/sms.php");
echo remakeNumber("027 928 4896", true);*/

// echo preg_match("/^[0-9]{10,}$/", "1234512asd3451234");

/*$data = [
    [
        "program_name" => "Program 1",
        "short_p" => "P1",
        "course_name" => "Course 1",
        "short_c" => ""
    ],
    [
        "program_name" => "Program 2",
        "short_p" => "P2",
        "course_name" => "Course 2",
        "short_c" => "C2"
    ],
    [
        "program_name" => "Program 3",
        "short_p" => "",
        "course_name" => "Course 3",
        "short_c" => "C3"
    ],
    [
        "program_name" => "Program 4",
        "short_p" => "P4",
        "course_name" => "Course 4",
        "short_c" => "C4"
    ]
];

$message[0]["teacher_id"] = 2;

$data = fetchData1("p.program_name, p.short_form as short_p, c.course_name, c.short_form as short_c",
"teacher_classes t JOIN program p ON t.program_id = p.program_id JOIN courses c ON c.course_id=t.course_id",
"t.teacher_id={$message[0]['teacher_id']}", 0);

echo stringifyClassNames($data);*/

// echo getStudentPosition("0012345621", 1, 1)
$date = date("2021-09-06"); $semester = 1;
echo getAcademicYear($date, $semester);

?>