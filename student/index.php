<?php 
    include_once("../includes/session.php");
    
    //boolean value to determine if page should render in student login mode or admission mode
    $isStudentLogin = false;
    if(str_contains($rootPath,"student")){
        $isStudentLogin = true;
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include_once($rootPath."/blocks/generalHead.php")?>
    
    <title><?= $isStudentLogin ? "SHSDesk | Student Login":"Student Portal" ?></title>

    <?php if($isStudentLogin) : ?>
        <meta name="description" content="This is the SHSDesk students portal. Students are able to access details
        about themselves and also about their various schools. This section serves students with their terminal
        reports and other information from the schools.">
    <?php else : ?>
        <meta name="description" content="This is the portal for students on the SHSDesk system. Use this portal to attain documents
        and also results. Provide your index number and proceed to the session of the system you will want delivered to you. SHSDesk
        is ready to provide all that you need.">
    <?php endif; ?>

    <style>
        body{
            background-color: #eee;
            background-image: url("<?php echo $url?>/assets/images/backgrounds/HAN4890-web-sq_fb2957a9-958f-47c8-aeb1-8bf211bfbbfb.jpg");
            background-position: center;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        form{
            width: 80vw;
        }

        @media screen and (orientation: landscape) and (min-width:770px){
            body{
                background-size: cover;
                background-position: center;
                background-repeat: no-repeat;
                justify-content: flex-end;
            }

            form{
                margin-right: 45px;
                width: 45vw;
                background-color: rgba(255,255,255,0.85);
            }
        }
    </style>
</head>
<body>
    <main>
        <form action="<?php echo $url?>/submit.php" method="post" name="studentForm" class="img-label">
            <div class="head">
                <h2><?= $isStudentLogin ? "Student Login":"Student Portal" ?></h2>
            </div>
            <div class="body">
                <div class="message_box no_disp">
                    <span class="message"></span>
                    <div class="close"><span>&cross;</span></div>
                </div>
                <p style="font-size: small; color: #444; text-align: center">
                <?php if($isStudentLogin) : ?>
                    This is the SHSDesk students login portal. Please provide your student index number and click enter
                    to access your dashboard.
                <?php else : ?>
                    This is the SHSDesk students portal. You can access your admission details using the admission button
                    below.
                <?php endif; ?>
                </p>
                <label for="indexNumber">
                    <span class="label_image">
                        <img src="<?php echo $url?>/assets/images/icons/person-outline.svg" alt="username_logo">
                    </span>
                    <input type="text" name="indexNumber" id="indexNumber" class="text_input" placeholder="<?= $isStudentLogin ? "Index Number or Username" : "Your JHS index number" ?>" autocomplete="off"
                    title="Enter your jhs index number to continue">
                </label>
                <?php if($isStudentLogin): ?>
                <label for="password">
                <span class="label_image">
                    <img src="<?php echo $url?>/assets/images/icons/key-outline.svg" alt="password">
                </span>
                <input type="password" name="password" id="password" class="text_input" placeholder="Your Password" autocomplete="off">
                <?php endif; ?>
            </label>
            </div>
            <?php if ($isStudentLogin): ?>
            <div class="btn_label">
                <button type="submit" name="submit" class="teal sp-lg" title="Proceed to your portal. Currently disabled" disabled value="studentLogin">Enter</button>
            </div>
            <?php else : ?>
            <div class="btn_label">
                <button type="button" name="admission" class="cyan sp-lg" title="Proceed to download admission documents" disabled>Admission Documents</button>
            </div>
            <?php endif; ?>
        </form>
    </main>

    <script src="<?= $url ?>/assets/scripts/functions.js"></script>
    <script src="<?php echo $url?>/assets/scripts/general.min.js?v=<?php echo time()?>" async></script>
    <script>
        $("input[name=<?= $isStudentLogin ? "password":"indexNumber" ?>]").keyup(function(){
            let number_of_characters = $(this).val().length;

            if(number_of_characters >= <?= $isStudentLogin ? 3:6 ?>){
                $("button").prop("disabled",false)
            }else{
                $("button").prop("disabled",true)
            }
        })
        //admission details button
        $("button[name=<?= $isStudentLogin ? "submit":"admission" ?>]").click(function(e){
            e.preventDefault()

            //take index number
            indexNumber = $("input#indexNumber").val();

            if(indexNumber == null || indexNumber == ""){
                messageBoxTimeout("studentForm", "No index number provided. Please provide an index number", "error");
            }else if(indexNumber.length < 5){
                messageBoxTimeout("studentForm", "Index number length too short. Please provide at least 5 character long index number", "error");
            }else{
                <?php if(!$isStudentLogin) : ?>
                $.ajax({
                    url: $("form").attr("action"),
                    data:{
                        submit: "getStudentIndex", index_number: indexNumber
                    },
                    dataType: "json",
                    beforeSend: function(){
                        message = "Searching details, please wait...";
                        type = "load";
                        time = 0;

                        messageBoxTimeout("studentForm", message, type, time);
                    },
                    success: function(json){
                        json = JSON.parse(JSON.stringify(json));

                        if(json["status"] == "student_success"){
                            message = "Details were found";
                            type = "success";
                            time = 3;

                            setTimeout(function(){
                                $(".message_box").addClass("load").fadeIn();
                                $(".message_box .message").html("Preparing Documents...");
                            }, 2000);

                            setTimeout(function(){
                                $(".message_box").removeClass("load").addClass("success").show();
                                $(".message_box .message").html("Documents are ready! Redirecting...");
                            }, 3500);

                            //redirect timeout
                            setTimeout(function(){
                                location.href = "../pdf_handle.php?indexNumber=" + indexNumber;
                            },4500)
                        }else if(json["status"] == "not-registered"){
                            message = "Sorry, you are not a registered student. Please go to the home page to enrol";
                            type = "error";
                            time = 6;
                        }else if(json["status"] == "wrong-index"){
                            message = "An invalid index number has been identified. Please check and try again";
                            type = "error";
                            time = 5;
                        }else{
                            message = json;
                        }

                        messageBoxTimeout("studentForm", message, type, time);
                    },
                    error: function(r){
                        messageBoxTimeout("studentForm", JSON.stringify(r), "load", 0);
                    }
                })
                <?php else : ?>
                    if($("input#password").val() === ""){
                        message = "No password provided"
                        type = "error"
                        time = 5

                        messageBoxTimeout(message,type,time)
                    }else{
                        const response = formSubmit($("form"), $("form").find("button[name=submit]"))
                        
                        if(typeof response === "boolean"){
                            messageBoxTimeout("studentForm","Login was successful", "success")
                            setTimeout(()=>{location.href="./main"},3500)
                        }else{
                            messageBoxTimeout("studentForm",response,"error")
                        }
                    }
                <?php endif; ?>
            }
        })
    </script>
</body>
</html>