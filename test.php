<?php include_once("includes/session.php"); ?><!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Test Area</title>
    </head>
    <body>
        
    </body>
    <?php 
    // test the send_email function
    // $message = "This is a test email for the test email function";
    // $subject = "Testing send_email Function";
    // formatDump(send_request_email(1));
    // header("Cache-Control: no-cache, must-revalidate");
    // header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

    // get all transactions with null academic year
    /*$user_school_id = 3;
    $academic_year = getAcademicYear(now(), false);
    $year = date("y");
    echo create_query_string(
        ["c.indexNumber", "c.Lastname", "c.Othernames", "e.enrolDate", "c.accept_old"],
        [
            "join" => "enrol_table cssps", "on" => "indexNumber indexNumber", "alias" => "e c",
        ],
        ["e.shsID=$user_school_id", "e.current_data = TRUE", "e.indexNumber IS NOT LIKE '%$year'", "e.academic_year = '$academic_year'"],
        0, "AND"
    );*/
    /*function get_salutation(string $gender){
        return strtolower($gender) == "male" ? "Mr" : "Mrs";
    }

    function get_username($email){
        $username_ = explode("@", $email)[0];
        $count = -1;

        do {
            ++$count;
            $username = $username_ . ($count > 0 ? "$count" : "");
        } while (is_array(fetchData1("user_username", "teacher_login", "user_username = '$username'", 0)));

        return $username;
    }

    $sql = "SELECT l.user_id, l.user_username, t.lname, t.oname, t.gender, t.email FROM teacher_login l JOIN teachers t ON l.user_id = t.teacher_id WHERE l.user_username REGEXP '^[0-9]+$'";
    $faulters = $connect2->query($sql)->fetch_all(MYSQLI_ASSOC);

    if($faulters){
        $subject = "Update to Your Account Username";
        $sql = "UPDATE teacher_login SET user_username = ? WHERE user_username = ?";
        foreach($faulters as $user){
            extract($user);
            $new_username = get_username($email);
            $salutation = get_salutation($gender);
            $teacher_id = formatItemId($user_id, "TID");

            $mail_body = <<< HTML
            <p>Helo $salutation $lname,</p>
            <p>We would like to inform you of an update to our user naming policy. Upon review, your current username (<strong>$user_username</strong>) has been flagged as invalid. As a result, a new username has been assigned to your account.</p>
            <p>Please log in with your Teacher ID (<strong>$teacher_id</strong>) to update your username. Your password remains unchanged.</p>
            <p><strong>New Username</strong>: $new_username</p>
            <p>Rest assured that all your data remains secure and unmodified. If you encounter any issues, please contact your school administrator for assistance.</p>
            <p>Best regards,</p>
            <p>SHSDesk Team</p>
            HTML;

            $stmt = $connect2->prepare($sql);
            $stmt->bind_param("ss", $new_username, $user_username);

            if($stmt->execute()){
                send_email($mail_body, $subject, $email);
                echo "<p>Email sent to $email</p>";
            }else{
                echo "<p>Problem occured when updating for $email</p>";
            }
        }
    }*/
    $recipients = "059247852, 059240313, 059248632, 059249601, 059242771, 059247420, 059243666, 059248698";
    $transaction_id = "T727852147828886";
    $role_school_id = 48;
    activate_access_pay($recipients, $transaction_id, $user_school_id);

    /*$user_school_id = 3;
    
    function setHouse1($gender, $shs_placed, $ad_index, $house, $boardingStatus, $is_new = true){
            $next_house = null;
    
            //allow whole database details to be passed here
            $house = decimalIndexArray($house);
    
            if(!array_key_exists("totalHeads",$house[0])){
                $houses = $house;
                $house = array_map(function($data) use ($gender){
                    $gender_room = strtolower($gender)."HeadPerRoom";
                    $gender_total_room = strtolower($gender)."TotalRooms";
    
                    return [
                        "id" => $data["id"],
                        "totalHeads" => intval($data[$gender_room]) * intval($data[$gender_total_room])
                    ];
                }, $houses);
            }
        
            $total = count($house);
    
            $last_student_order = $is_new ? "e.enrolDate" : "h.updated_at";
        
            //get last index number to successfully register
            $last_student = fetchData(
                ["h.houseID"],
                [
                    ["join" => "cssps enrol_table", "alias" => "c e", "on" => "indexNumber indexNumber"],
                    ["join" => "cssps house_allocation", "alias" => "c h", "on" => "indexNumber indexNumber"]
                ],
                [
                    "e.gender='$gender'", "e.indexNumber != '$ad_index'", "h.current_data=1", "e.shsID=$shs_placed", 
                    "c.boardingStatus='$boardingStatus'", "h.houseID IS NOT NULL", "h.houseID > 0"
                ],
                where_binds: "AND", order_by: $last_student_order, asc: false
            );
            
            if(is_array($last_student)){
                $hid = (int) $last_student["houseID"];
    
                if(!empty($hid)){
                    //retrieve last house id given out
                    $id = $hid;
                    $next_house = 0;
    
                    //get the total of all houses
                    $hs_ttl = decimalIndexArray(fetchData(...[
                        "columns" => ["h.id", "COUNT(ho.indexNumber) as total"],
                        "table" => [
                            "join" => "house_allocation houses",
                            "alias" => "ho h",
                            "on" => "houseID id"
                        ],
                        "where" => ["ho.schoolID=$shs_placed","ho.studentGender='$gender'", "ho.boardingStatus='Boarder'", "ho.current_data=TRUE"],
                        "limit" => 0, "where_binds" => "AND", "group_by" => "h.id"
                    ]));
    
                    foreach($house as $house_value){
                        $found = false;
                        foreach($hs_ttl as $ttl){
                            if($ttl["id"] == $house_value["id"]){
                                $ttl_data[$house_value["id"]] = (int) $ttl["total"];
                                $found = true;
                            }
                        }
                    
                        if(!$found){
                            $ttl_data[$house_value["id"]] = 0;
                        }
                    }
                    
                    for($i = 0; $i < $total; $i++){                    
                        //try choosing the next, previous or current house
                        //start at the last house given out
                        if($house[$i]["id"] == $id){
                            $check_count = 0;       //this variable would be used check if all houses have been checked at most once
                            
                            //select a house and check its availability
                            while($next_house < 1){
                                if(strtolower($boardingStatus) === "boarder"){
                                    $house_pointer = 0;     //this is a pointer to the house array provided
                                    if($check_count == $total){
                                        //forcefully exit the function if all houses are full
                                        return null;
                                    }elseif($i+1 == $total){
                                        //current pointer equals last house in array, pick first house in array for checking
                                        $house_pointer = 0;
                                    }elseif($i+1 < $total && $i >= 0){
                                        //next house is not at the end of the array for checking
                                        $house_pointer = $i + 1;
                                    }
    
                                    //get house id, total epected membership and current total membership
                                    $nid = $house[$house_pointer]["id"];
                                    $ttl = $ttl_data[$nid];
                                    $cur_ttl = $house[$house_pointer]["totalHeads"];
                                    
                                    //check immediate available houses
                                    if($i+1 == $total && $ttl < $cur_ttl){
                                        //Give boarder candidate first house in array
                                        $next_house = $house[0]["id"];
                                    }elseif($i+1 < $total && $i >= 0 && $ttl < $cur_ttl){
                                        //Give boarder candidate a next house
                                        $next_house = $house[$house_pointer]["id"];
                                    }
    
                                    //keep track of the number of houses checked
                                    ++$check_count;
                                }elseif(strtolower($boardingStatus) === "day"){
                                    //check immediate available houses
                                    if($i+1 == $total){
                                        //Give day candidate current house in array
                                        $next_house = $house[0]["id"];
                                    }elseif($i+1 < $total && $i >= 0){
                                        //Give day candidate a next house
                                        $next_house = $house[$i+1]["id"];
                                    }
                                }
                                
                                //increment i
                                $i++;
        
                                //start from 0 if end is reached but no house
                                if($i+1 > $total && $next_house < 1){
                                    $i = 0;
                                }
                            }
                        }
                        
                        //break the house checking if a house has been allocated
                        if($next_house > 0){
                            break;
                        }
                    }
                }else{
                    //this means it is the first entry
                    $next_house = $house[0]["id"];
                }
            }else{
                $next_house = $house[0]["id"];
            }
        
            return $next_house;
        }
    
    $displaced_studs = $connect->query(
                    "SELECT studentGender, indexNumber, boardingStatus 
                        FROM house_allocation 
                        WHERE schoolID=$user_school_id AND current_data = 1 AND NOT EXISTS (
                            SELECT 1 
                            FROM houses 
                            WHERE houses.id = house_allocation.houseID 
                            AND houses.schoolID = $user_school_id
                        )"
                )->fetch_all(MYSQLI_ASSOC);
    
                $houses = decimalIndexArray(fetchData(...[
                    "columns" => ["id","maleHeadPerRoom", "maleTotalRooms", "femaleHeadPerRoom", "femaleTotalRooms"],
                    "table" => "houses",
                    "where" => ["schoolID=$user_school_id"],
                    "limit" => 0,
                ]));
    
                //add seconds to current time
                $sec = 0;
                foreach($displaced_studs as $student){
                    $student_house = setHouse1($student["studentGender"], $user_school_id, $student["indexNumber"], $houses, $student["boardingStatus"], false);
                    echo $student_house." - ";
                    $now = date("Y-m-d H:i:s", strtotime("+".$sec++." seconds"));
    
                    // update student data
                    try {
                        // $connect->query("UPDATE house_allocation SET houseID=$student_house, updated_at='$now' WHERE indexNumber='{$student['indexNumber']}'");
                        $sql = "UPDATE house_allocation SET houseID=?, updated_at=? WHERE indexNumber=?";
                        $stmt = $connect->prepare($sql);
                        $stmt->bind_param("iss", $student_house, $now, $student["indexNumber"]);
                        $response = $stmt->execute();
    
                        if(!$response){
                            exit("{$student['indexNumber']} could not be processed. Error: ".$connect->error);
                        }
                    } catch (\Throwable $th) {
                        exit(throwableMessage($th));
                    }
                }*/
        /*$affected = decimalIndexArray(fetchData1("DISTINCT result_token", "results", "position = 0", 0));
        
        if($affected){
            flush_php_start();
            
            $affected = array_column($affected, "result_token");
            
            echo "<table border='1'>\n
                    <thead>\n
                        <td>Token</td>\n
                        <td>Total Results</td>\n
                        <td>Success</td>\n
                        <td>Failed</td>\n
                    </thead>\n
                    <tbody>\n
            ";
            foreach($affected as $token){
                $data = assignPositions($token);

                echo "<tr>\n
                        <td>{$data['token']}</td>\n
                        <td>{$data['initial']}</td>\n
                        <td>{$data['success']}</td>\n
                        <td>{$data['failed']}</td>\n
                    </tr>\n
                ";
                
                // Flush the output buffer
                flush_output();
            }
            echo "</tbody>\n</table>";
        }else{
            echo "no affected tokens to be processed";
        }*/

        close_connections();
    ?>
</html>