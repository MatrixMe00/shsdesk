<?php
    require_once("includes/session.php");

    //provide links for admission letter and prospectus
    if(isset($_SESSION["ad_stud_index"]) && !empty($_SESSION["ad_stud_index"])){
?>
<head>
    <title>Download Documents</title>
    <script src="<?php echo $url?>/assets/scripts/jquery/uncompressed_jquery.js"></script>
    <meta name="nofollow" content="nofollow">
    <style>
        #container{
            width: 100vw;
            height: 100vh;
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }
        body{
            overflow: hidden;
        }
        button{
            width: 70vw;
            height: 10vh;
            padding: 0.3em 0.5em;
            cursor: pointer;
            outline: unset;
            border: 1px solid lightgrey;
            transition: all 0.6s;
            font-size: 13pt;
            background-color: #eee;
            color: #222;
        }
        button:hover{
            background-color: #28a745;
            color: #eee;
        }
        .member_div{
            padding: 0.5em;
        }
    </style>
</head>

<body>
    <div id="container">
        <div class="member_div">
            <a href="<?php echo $_SESSION["ad_school_prospectus"] ?>">
                <button id="btn_pros">Prospectus is ready for download | Download [PDF]</button>
            </a>
        </div>
        <div class="member_div">
            <a href="<?php echo $url?>/customPDFGenerator.php">
                <button id="btn_ad">Admission letter is ready for download | Download [PDF]</button>
            </a>
        </div>
    </div>

    <script>
    </script>
</body>
<?php
    }else{
        echo "No result to deliver";
    }
?>