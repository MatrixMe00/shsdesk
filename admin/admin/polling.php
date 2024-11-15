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

function count_issues(){
    global $user_school_id;

    $issues = fetchData("COUNT(c.indexNumber) as total", 
                ["join" => "enrol_table cssps", "on" => "indexNumber indexNumber", "alias" => "e c"],
                ["c.schoolID=$user_school_id", "c.current_data=TRUE", "c.Enroled=FALSE"], 
                0, "AND", "left")["total"];
    
    return $issues;
}

$message = [
    "notification" => count_notifications(), 
    "displaced" => count_displaced(), 
    "transfers" => get_transfers(true)
];

header("Content-type: application/json");
echo json_encode($message);
flush();
close_connections();
