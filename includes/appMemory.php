<?php 
    /**
     * This enum defines the constants for api keys
     * @const string ADMISSION constant for the admission key
     * @const string MANAGEMENT constant for the management key
     * @const string BOTH constant for both keys
     */
    class APIKEY{
        const ADMISSION = "split_code_admission";
        const MANAGEMENT = "split_code_management";
        const BOTH = "split_code_admission, split_code_management";
    }

    $serverName = $_SERVER['SERVER_NAME'];
    $serverDown = false;

    // last figure of index number is current year
    $index_end = date("m") > 7 ? date("y") : date("y") - 1; 

    $sqlServer = array();
    $priceKeys = array(
        "development"=>"pk_test_3a5dff723cbd3fe22c4770d9f924d05c77403fca",
        "live"=>"pk_live_056157b8c9152eb97c1f04b2ed60e7484cd0d955"
    );
    $splitKeys = array(
        "live" => [
            // "matrix_admission" => "SPL_U6mW80wZNH",
            "matrix_admission" => "SPL_kQPErth3bc",
            "matrix_school_management" => "SPL_nDjVFzSGVb",
            "matrix_admission_bulk" => "SPL_3VlEyxtLgF"
        ],
        "development" => [
            "matrix_admission" => "",
            "matrix_school_management" => "",
            "matrix_admission_bulk" => ""
        ]
    );
    $splitKey = [];
    $priceKey = "";

    //determine development server and live server to determine how error codes are shown
    $developmentServer = null;

    if($serverDown === false){
        // cost of using the system
        $system_usage_price = 50;
        
        switch($serverName){
            case "localhost":
            case "shsdesk.local":
            case "www.shsdesk.local":
            case "teacher.shsdesk.local":
            case "www.teacher.shsdesk.local":
            case "student.shsdesk.local":
            case "www.student.shsdesk.local":
            case "www.results.shsdesk.local":
            case "results.shsdesk.local":
                $sqlServer = [
                    "host" => "localhost",
                    "hostpassword" => "",
                    "hostname" => "root",
                    "db1" => "shsdesk",
                    "db2" => "shsdesk2"
                ];

                $priceKey = $priceKeys["development"];
                $splitKey = $splitKeys["development"];

                $developmentServer = true;

                // mail server configuration
                $mailserver_email = "successinnovativehub@gmail.com";
                $mailserver_password = "wzap xjim dvpv bhfe";
                $mailserver = "smtp.gmail.com";

                break;
            case "shsdesk.com":
            case "www.shsdesk.com":
            case "teacher.shsdesk.com":
            case "www.teacher.shsdesk.com":
            case "student.shsdesk.com":
            case "www.student.shsdesk.com":
            case "www.results.shsdesk.com":
            case "results.shsdesk.com":
                $sqlServer = [
                    "host" => "localhost",
                    "hostpassword" => "Password@2020",
                    "hostname" => "shsdeskc_matrixme",
                    "db1" => "shsdeskc_shsdesk",
                    "db2" => "shsdeskc_shsdesk2"
                ];

                $priceKey = $priceKeys["live"];
                $splitKey = $splitKeys["live"];

                $developmentServer = false;
                $server_secret = "sk_live_daaa39f38fa9b693c96a479afd2308d1ee9c2c74";

                // mail server configuration
                $mailserver_email = "_mainaccount@shsdesk.com";
                $mailserver_password = "Junior2020";
                $mailserver = "server13.aveshost.net";

                break;
            case "test.shsdesk.com":
            case "www.test.shsdesk.com":
            case "test.student.shsdesk.com":
            case "www.test.student.shsdesk.com":
            case "test.teacher.shsdesk.com":
            case "www.test.teacher.shsdesk.com":
            case "www.test.results.shsdesk.com":
            case "test.results.shsdesk.com":
                $sqlServer = [
                    "host" => "localhost",
                    "hostpassword" => "Password@2020",
                    "hostname" => "shsdeskc_matrixme",
                    "db1" => "shsdeskc_shsdesk_test",
                    "db2" => "shsdeskc_shsdesk2_test"
                ];

                $priceKey = $priceKeys["development"];
                $splitKey = $splitKeys["development"];

                $developmentServer = true;
                $server_secret = "sk_test_32ddfd2d85a0bc1c9bbe946c28ac9f82069a766f";

                // mail server configuration
                $mailserver_email = "_mainaccount@shsdesk.com";
                $mailserver_password = "Junior2020";
                $mailserver = "server13.aveshost.net";

                break;

        }

        $phoneNumbers = [
            "027","057","026","056","024",
            "025","053","054","055","059",
            "020","050","023"
        ];

        $phoneNumbers1 = [
            "airteltigo" => ["027","057","026","056"],
            "mtn" => ["024","025","053","054","055","059"],
            "vodafone" => ["020","050"],
            "glo" => ["023"]
        ];
    }
?>