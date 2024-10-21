<?php
    require "includes/session.php";

    function isHashed($password) {
        // Check if the password matches bcrypt, argon2i, or argon2id patterns
        return (preg_match('/^\$2y\$/', $password) || // bcrypt
                preg_match('/^\$argon2i\$/', $password) || // argon2i
                preg_match('/^\$argon2id\$/', $password)); // argon2id
    }

    // $users = decimalIndexArray(fetchData("user_id, password", "admins_table", limit: 0));
    $users = decimalIndexArray(fetchData1("indexNumber, password", "students_table", limit: 0));

    if($users){
        $connect->begin_transaction();
        $sql = "UPDATE students_table SET password = ? WHERE indexNumber = ?";
        // $sql = "UPDATE admins_table SET password = ? WHERE user_id = ?";
        // $default_password = password_hash("1234567890", PASSWORD_DEFAULT);
        $default_password = password_hash("Password@1", PASSWORD_DEFAULT);
        $affected = 0;

        try{
            // $stmt = $connect->prepare($sql);
            $stmt = $connect2->prepare($sql);

            if(!$stmt){
                throw new Exception("Statement could not be prepared: ".$connect->error);
            }

            foreach($users as $user){
                if(!isHashed($user["password"])){
                    // $stmt->bind_param("si", $default_password, $user["user_id"]);
                    $stmt->bind_param("ss", $default_password, $user["indexNumber"]);

                    if($stmt->execute()){
                        ++$affected;
                    }else{
                        throw new Exception("Error: ".$stmt->error);
                    }
                }
            }

            echo "$affected users have their passwords reset";
            // $connect->commit();
        }catch(Throwable $th){
            $connect->rollback();
            echo throwableMessage($th);
        }
    }
    