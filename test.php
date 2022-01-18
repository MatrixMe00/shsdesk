<?php 
include_once("includes/session.php");
$ad_gender = "Male";
$shs_placed = 3;

//count number of houses of the school
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
}
?>