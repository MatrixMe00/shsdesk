<?php
    require "session.php";

    function create_message($template, $keys, $object){
        foreach($keys as $key){
            $key_search = "{".$key."}";
            if(str_contains($template, $key_search)){
                if(is_null($object[$key])){
                    $object[$key] = "N/A";
                }
                $template = str_replace($key_search, $object[$key], $template);
            }
        }

        return $template;
    }

    // get students whose information needs to be sent
    $academic_year = getAcademicYear(now(), false);
    $reject_schools = [36,93];

    $students = decimalIndexArray(fetchData(
        "CONCAT(c.Lastname, ' ', c.Othernames) as fullname, c.programme, s.schoolName as school_name, g.school_id, g.contact, g.id as row_id, c.boardingStatus AS boarding_status",
        [
            ["join" => "cssps cssps_guardians", "on" => "indexNumber index_number", "alias" => "c g"],
            ["join" => "cssps_guardians schools", "on" => "school_id id", "alias" => "g s"]
        ],
        ["g.is_valid = TRUE", "g.last_messaged IS NULL", "g.academic_year = '$academic_year'", "g.school_id NOT IN (".implode(",", $reject_schools).")"], 600, "AND", order_by: "g.created_at"
    ));

    if($students){
        $total_students = count($students);
        $delivered = 0;

        // get schools who have set up their sms
        // $ussds = decimalIndexArray(fetchData1("school_id, sms_id", "school_ussds", "status = 'approve'", 0));

        if(true){
            $message_template = fetchData("value", "system_variables", "name='sms_broadcast'")["value"] ?? null;
            // $ussds = pluck($ussds, "school_id", "sms_id");

            if($message_template){
                $keys = ["fullname", "school_name", "boarding_status", "programme"];

                // loop through each student and format the template
                foreach($students as $student){
                    // skip invalid phone numbers
                    if(!checkPhoneNumber($student["contact"])){
                        $connect->query("UPDATE cssps_guardians SET is_valid = FALSE WHERE id = {$student['row_id']}");
                        continue;
                    }

                    $message = create_message($message_template, $keys, $student);
                    // $ussd = $ussds[$student["school_id"]] ?? "SHSDesk";
                    $ussd = "SHSDesk";
                    $response = send_sms($ussd, $message, [$student["contact"]], SMS_PROVIDER::MNOTIFY);
                    
                    if(!empty($response["response"]["status"]) && $response["response"]["status"] == "success"){
                        $data = $response["response"]["summary"];

                        // if message was delivered, do not add to next batch of checks
                        if(!empty($data["message_id"])){
                            $connect->query("UPDATE cssps_guardians SET last_messaged = NOW() WHERE id = {$student['row_id']}");
                            ++$delivered;
                        }
                    }
                }

                if($delivered){
                    echo "successfully delivered $delivered messages out of $total_students intended\n";
                    
                    send_email(
                        "Hello Team,<br><br>
                        The placement information SMS broadcast has been completed.<br><br>
                        Delivered successfully to: <b>$delivered</b><br>
                        Total intended recipients: <b>$total_students</b><br><br>
                        Thank you,<br>
                        System Notification",
                        "Placement SMS Broadcast Completed",
                        ["successinnovativehub@gmail.com", "safosah00@gmail.com"]
                    );
                }
            }
        }
    }    
