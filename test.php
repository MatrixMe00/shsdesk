<?php 
include_once("includes/session.php");
$ad_gender = "Male";
$shs_placed = 8;
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


//count number of houses of the school
$sql = "SELECT id, maleHeadPerRoom, maleTotalRooms, femaleHeadPerRoom, femaleTotalRooms
FROM houses
WHERE schoolID = $shs_placed AND gender = '$ad_gender' OR gender='Both'";
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

//search for last house allocation entry for this gender
$sql = "SELECT houseID
    FROM house_allocation
    WHERE schoolID = $shs_placed AND studentGender='$ad_gender'
    ORDER BY indexNumber DESC
    LIMIT 1";
$result = $connect->query($sql);

$hid = $result->fetch_assoc()["houseID"];

//fetch student details for entry from cssps
$student_details = fetchData("*", "cssps", "indexNumber='$ad_index'");

if($result->num_rows == 1){
    //retrieve last house id given out
    $id = $hid;
    $next_room = 0;

    for($i = 0; $i < $total; $i++){
        

        //try choosing the next or current house
        if($house[$i]["id"] == $id){
            if($i+1 < $total){
                $nid = $house[$i+1]["id"];
                $ttl = fetchData("COUNT(indexNumber) AS total", "house_allocation", "schoolID=$shs_placed AND houseID=$nid AND boardingStatus='Boarder'")["total"];
            }elseif($i-1 < $total){
                $nid = $house[$i-1]["id"];
                $ttl = fetchData("COUNT(indexNumber) AS total", "house_allocation", "schoolID=$shs_placed AND houseID=$nid AND boardingStatus='Boarder'")["total"];
            }elseif($i+1 == $total){
                $nid = $house[$i+1]["id"];
                $ttl = fetchData("COUNT(indexNumber) AS total", "house_allocation", "schoolID=$shs_placed AND houseID=$nid AND boardingStatus='Boarder'")["total"];
            }
            //check immediate available houses
            if($i+1 < $total && $ttl < $house[$i+1]["totalHeads"]){
                $next_room = $house[$i+1]["id"];
            }elseif($i-1 >= 0 && $ttl < $house[$i-1]["totalHeads"]){                                            
                $next_room = $house[$i-1]["id"];
            }elseif($i+1 == $total && $ttl < $house[0]["totalHeads"]){
                $next_room = $house[0]["id"];
            }
            
            if($next_room > 0){
                break;
            }
        }
    }

    echo "<br>prev room: $hid<br>next room: $next_room";
    }
}

?>