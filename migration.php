<?php
// connect to database
require_once "includes/session.php";

// 1. Fetch all rows
$sql = "SELECT id, prospectusPath FROM schools";
$result = $connect->query($sql);

while ($row = $result->fetch_assoc()) {
    $id = $row['id'];
    $oldValue = $row['prospectusPath'];

    // if it's not already JSON, convert
    $decoded = json_decode($oldValue, true);
    if ($decoded === null) {
        $newJson = json_encode([
            "type" => "single",
            "files" => $oldValue
        ], JSON_UNESCAPED_SLASHES);

        // update row
        $update = $connect->prepare("UPDATE schools SET prospectusPath=? WHERE id=?");
        $update->bind_param("si", $newJson, $id);
        $update->execute();
        $update->close();
    }
}

// 2. Alter column to JSON
$alter = "ALTER TABLE schools MODIFY prospectusPath JSON";
if ($connect->query($alter) === TRUE) {
    echo "Column successfully converted to JSON!";
} else {
    echo "Error altering column: " . $connect->error;
}

$connect->close();
