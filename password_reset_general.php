<?php
    require "includes/session.php";
    
    $types = [
        "admin" => [
            "col" => "user_id, password", "table" => "admins_table", "function" => "fetchData", "def_password" => "1234567890",
            "password_col" => "password", "id_col" => "user_id", "bind" => "si", "connection" => $connect
        ],
        "students" => [
            "col" => "indexNumber, password", "table" => "students_table", "function" => "fetchData1", "def_password" => "Password@1",
            "password_col" => "password", "id_col" => "indexNumber", "bind" => "ss", "connection" => $connect2
        ],
        "admin" => [
            "col" => "user_id, user_password", "table" => "teacher_login", "function" => "fetchData1", "def_password" => "Password@1",
            "password_col" => "user_password", "id_col" => "user_id", "bind" => "si", "connection" => $connect2
        ],
    ];

    $type = "students";

    $user = $types[$type] ?? null;

    if(!$user){
        exit("Invalid user type: $type");
    }

    $users = decimalIndexArray($user["function"](
        $user["col"], $user["table"], $user["password_col"]." NOT LIKE '$2y%'", 0
    ));

    if($users){
        $connection = $user["connection"];
        $connection->begin_transaction();
        $sql = "UPDATE {$user['table']} SET {$user['password_col']} = ? WHERE {$user['id_col']} = ?";

        $affected = 0;

        try{
            $stmt = $connection->prepare($sql);

            if(!$stmt){
                throw new Exception("Statement could not be prepared: ".$connection->error);
            }

            foreach($users as $user_){
                // $default_password = password_hash("1234567890", PASSWORD_DEFAULT);
                $default_password = password_hash($user["def_password"], PASSWORD_DEFAULT);

                $stmt->bind_param($user["bind"], $default_password, $user_[$user["password_col"]]);

                if($stmt->execute()){
                    ++$affected;
                }else{
                    throw new Exception("Error: ".$stmt->error);
                }

                // sleep(1);
            }
            $connection->commit();

            echo "$affected users have their passwords reset";
        }catch(Throwable $th){
            $connection->rollback();
            echo throwableMessage($th);
        }
    }

    close_connections();
    