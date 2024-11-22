<?php
require $_SERVER["DOCUMENT_ROOT"]."/includes/session.php";

function count_notifications(){
    global $user_username, $user_details, $connect;

    $response = 0;

    // Unread notifications query
    $result = $connect->query("SELECT *
    FROM notification
    WHERE (Read_by NOT LIKE '%$user_username%' AND Audience='all')
    OR (Audience LIKE '%$user_username%' AND Read_by NOT LIKE '%$user_username%')
    AND '{$user_details['adYear']}' <= DATE
    ORDER BY ID DESC");
    $response += $result->num_rows;

    // New replies query
    $result = $connect->query("SELECT DISTINCT n.* 
    FROM notification n JOIN reply r 
    ON n.ID = r.Comment_id 
    WHERE r.Read_by NOT LIKE '%$user_username%'
    AND n.Read_by LIKE '%$user_username%'
    ORDER BY ID DESC");
    $response += $result->num_rows;

    return $response;
}

function count_displaced(){
    global $connect, $user_school_id;

    $displaced_studs = (int) $connect->query(
        "SELECT COUNT(indexNumber) AS total 
        FROM house_allocation 
        WHERE schoolID=$user_school_id AND current_data = 1 AND NOT EXISTS (
            SELECT 1 
            FROM houses 
            WHERE houses.id = house_allocation.houseID 
            AND houses.schoolID = $user_school_id
        )"
    )->fetch_assoc()["total"];

    return $displaced_studs;
}

function count_old_new(){
    global $user_school_id;

    $academic_year = getAcademicYear(now(), false);
    $year = date("y");

    $total = fetchData(
        ["COUNT(c.indexNumber) AS total"],
        [
            "join" => "enrol_table cssps", "on" => "indexNumber indexNumber", "alias" => "e c",
        ],
        ["e.shsID=$user_school_id", "e.current_data = TRUE", "e.indexNumber NOT LIKE '%$year'", "e.academic_year = '$academic_year'", "accept_old = 0"],
        0, "AND"
    )["total"];

    return $total;
}

function count_issues(){
    global $user_school_id;

    $issues = fetchData("COUNT(c.indexNumber) as total", 
                ["join" => "enrol_table cssps", "on" => "indexNumber indexNumber", "alias" => "e c"],
                ["c.schoolID=$user_school_id", "c.current_data=TRUE", "c.Enroled=FALSE"], 
                0, "AND", "left")["total"];
    
    return $issues;
}

function count_empty_boarding_status(){
    global $user_school_id;
    $total = fetchData("COUNT(indexNumber) AS total", "cssps", "schoolID=$user_school_id AND boardingStatus = ''")["total"];

    return $total;
}

$issues = array_sum([
    count_issues(), count_old_new(), count_empty_boarding_status()
]);

$message = [
    "notification" => count_notifications(), 
    "displaced" => count_displaced(),
    "issues" => $issues, 
    "transfers" => get_transfers(true)
];

header("Content-type: application/json");
echo json_encode($message);
flush();
close_connections();
