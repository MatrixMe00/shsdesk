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

    $sqlServer = array();
    $priceKeys = array(
        "development"=>"pk_test_3a5dff723cbd3fe22c4770d9f924d05c77403fca",
        "live"=>"pk_live_056157b8c9152eb97c1f04b2ed60e7484cd0d955"
    );
    $splitKeys = array(
        "live" => [
            "matrix_admission" => "SPL_U6mW80wZNH",
            "matrix_school_management" => "SPL_nDjVFzSGVb"
        ],
        "development" => [
            "matrix_admission" => "",
            "matrix_school_management" => ""
        ]
    );
    $splitKey = [];
    $priceKey = "";

    //determine development server and live server to determine how error codes are shown
    $developmentServer = null;

    if($serverDown === false){
        switch($serverName){
            case "localhost":
            case "shsdesk.local":
            case "www.shsdesk.local":
            case "teacher.shsdesk.local":
            case "www.teacher.shsdesk.local":
            case "student.shsdesk.local":
            case "www.student.shsdesk.local":
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

                break;
            case "shsdesk.com":
            case "www.shsdesk.com":
            case "teacher.shsdesk.com":
            case "www.teacher.shsdesk.com":
            case "student.shsdesk.com":
            case "www.student.shsdesk.com":
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

                break;
            case "test.shsdesk.com":
            case "www.test.shsdesk.com":
            case "test.student.shsdesk.com":
            case "www.test.student.shsdesk.com":
            case "test.teacher.shsdesk.com":
            case "www.test.teacher.shsdesk.com":
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