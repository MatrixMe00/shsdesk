<?php include_once("../includes/session.php"); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Results Menu</title>
    <script src="<?= $url ?>/assets/scripts/jquery/compressed_jquery.js"></script>
    <link rel="stylesheet" href="<?= $url ?>/assets/styles/general.min.css?v=<?= time() ?>">
    <link rel="stylesheet" href="<?= $url ?>/assets/styles/admin_form.min.css?v=<?= time() ?>">
    <style>
        button.menu-btn:hover img{
            filter: invert(1);
        }
        body{
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url("<?= $url ?>/assets/images/backgrounds/carousel/joanna-kosinska-LAaSoL0LrYs-unsplash.jpg");
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
        }
    </style>
</head>
<body class="light flex-all-center sp-med" style="height: 100vh;">
    <main class="wmax-lg sm-auto w-full">
        <div id="main" class="sm-rnd flex-all-center wmax-md sm-auto lt-shade flex-wrap white gap-lg sp-xxlg p-xxlg">
            <h2 class="w-full txt-al-c color-primary">SELECT AN ITEM</h2>
            <button name="index_mode" class="menu-btn b-unset cursor-p xs-rnd lt-shade-h txt-al-c wmin-2xs light txt-fl">
                <div class="icon-sm img sm-auto">
                    <img src="../assets/images/icons/person-outline.svg" alt="">
                </div>
                <p class="sm-xlg-t">Get Results By Index Number</p>
            </button>
            <button name="filter_mode" class="menu-btn b-unset cursor-p xs-rnd lt-shade-h txt-al-c wmin-2xs light txt-fl">
                <div class="icon-sm img sm-auto">
                    <img src="../assets/images/icons/people-outline.svg" alt="">
                </div>
                <p class="sm-xlg-t">Get Results by Names</p>
            </button>
        </div>

        <form action="submit.php" id="index_mode" name="index_form" class="no_disp white wmax-md sm-auto sp-lg lt-shade">
            <div class="head">
                <h2>Provide an index number</h2>
            </div>
            <div class="body sm-xlg-tp">
                <p class="txt-al-c sm-xlg-b sm-med-t">Please provide the index number of the students below</p>
                <label for="index_number" class="flex-column gap-sm">
                    <span class="label_title">Student Index Number</span>
                    <input type="text" name="index_number" id="index_number" placeholder="Index Number of Student" required>
                </label>
            </div>
            <div class="btn sp-unset flex-eq-3xs w-full flex flex-wrap gap-md flex-space-content p-lg">
                <button class="cancel b-secondary plain cursor-p secondary" type="reset">Back</button>
                <button class="plain primary b-primary cursor-p" type="submit" value="get_results" name="submit">Proceed</button>
            </div>
            <input type="hidden" name="mode" value="index">
        </form>

        <form action="submit.php" id="filter_mode" name="filter_form" class="no_disp white wmax-md sm-auto sp-lg lt-shade">
            <div class="head">
                <h2>Use the filters</h2>
            </div>
            <div class="body sm-xlg-tp">
                <p class="txt-al-c sm-xlg-b sm-med-t">Use the options below to find your student</p>
                <div class="joint">
                    <label for="school" class="flex-column">
                        <span class="label_title">Select the school of the ward</span>
                        <select name="school" id="school">
                            <option value="">Select School</option>
                            <?php 
                                if(is_array($schools = decimalIndexArray(fetchData("id, schoolName","schools","",0)))):
                                    foreach($schools as $school): ?>
                            <option value="<?= $school["id"] ?>"><?= $school["schoolName"] ?></option>
                            <?php endforeach; endif; ?>
                        </select>
                    </label>
                    <label for="program" class="flex-column">
                        <span class="label_title">Select the programme</span>
                        <select name="program" id="program"></select>
                    </label>
                    <label for="class" class="flex-column">
                        <span class="label_title">Student's Class</span>
                        <select name="class" id="class"></select>
                    </label>
                    <label for="program_year" class="flex-column">
                        <span class="label_title">Program Year</span>
                        <select name="program_year" id="program_year">
                            <option value="">Select student's year level</option>
                            <option value="1">Form 1</option>
                            <option value="2">Form 2</option>
                            <option value="3">Form 3</option>
                        </select>
                    </label>
                </div>
                <label for="f_index_number" class="flex-column">
                    <span class="label_title">Index Number</span>
                    <input type="text" name="index_number" id="f_index_number" list="students" placeholder="Enter Student name or index number">
                    <datalist id="students"></datalist>
                </label>
                <input type="hidden" name="mode" value="filter">
                <p class="txt-al-c txt-fs no_disp" id="f_status"></p>
            </div>
            <div class="btn sp-unset flex-eq-3xs w-full flex flex-wrap gap-md flex-space-content p-lg">
                <button class="cancel filter b-secondary plain cursor-p secondary" type="reset">Back</button>
                <button class="plain primary b-primary cursor-p" type="submit" value="get_results" name="submit">Proceed</button>
            </div>
        </form>
    </main>

    <script src="../assets/scripts/functions.min.js"></script>
    <script>
        let mode = "";
        $(".menu-btn").click(function(){
            $("#main").addClass("no_disp").removeClass("flex-all-center");
            mode = $(this).attr("name");
            $("form").addClass("no_disp")
            $("#" + mode).removeClass("no_disp")
        })

        $(".cancel").click(function(){
            $("#main").removeClass("no_disp").addClass("flex-all-center");
            mode = "";
            $("form").addClass("no_disp")
        })

        //filter mode variables
        $(document).ready(function(){
            let showProgram = false;
            let showClass = false;
            let showIndex = false;
            let showYear = false;

            let school_id = 0;
            let programme = "";
            let class_id = 0;
            let year = 0;
            let indexNum = "";

            formControl();

            $(".cancel.filter").click(function(){
                showProgram = false;
                showClass = false;
                showIndex = false;
                showYear = false;

                school_id = 0;
                programme = "";
                class_id = 0;
                year = 0;
                indexNum = "";

                $("#f_status").addClass("no_disp").html("");

                formControl();
            })

            $("select").change(async function(){
                const value = $(this).val();
                const select_id = $(this).attr("id");
                let ajax = null;
                const loader = $("#f_status");
                
                switch (select_id) {
                    case "school":
                        if(value != ""){
                            ajax = await $.ajax({
                                url: "submit.php",
                                data: {submit: "getPrograms",school_id:value},
                                method: "GET",
                                timeout: 30000,
                                beforeSend: function(){
                                    loader.removeClass("no_disp").html("Fetching programs, please wait...")
                                },
                                success: function(response){
                                    response = JSON.parse(JSON.stringify(response));
                                    if(response["success"] === true){
                                        const first_option = "Select a programme of the student";
                                        populateSelect("program",response["response"], first_option);
                                        showProgram = true;
                                        loader.html("").addClass("no_disp");
                                        school_id = value;
                                    }else{
                                        loader.html(response["response"] ?? response);
                                        showProgram = false;
                                    }
                                },
                                error: function(xhr){
                                    let message = xhr.responseText;

                                    if(xhr.statusText == "timeout"){
                                        message = "Connection was timed out due to a slow network connection. Please try again later";
                                    }

                                    loader.html(message);
                                }
                            })
                        }else{
                            showProgram = false;
                        }

                        break;
                    
                    case "program":
                        const first_option = "Select Student's Class";

                        if(value != ""){
                            ajax = await $.ajax({
                                url: "submit.php",
                                data: {submit: "getClasses",school_id:school_id, program: value},
                                method: "GET",
                                timeout: 30000,
                                beforeSend: function(){
                                    loader.removeClass("no_disp").html("Fetching classes, please wait...")
                                },
                                success: function(response){
                                    response = JSON.parse(JSON.stringify(response));
                                    if(response["success"] === true){
                                        showClass = true;
                                        populateSelect("class",response["response"], first_option, "program_id, program_name")
                                        loader.html("").addClass("no_disp");
                                    }else{
                                        loader.html(response["response"] ?? response);
                                        showClass = false;
                                    }
                                },
                                error: function(xhr){
                                    let message = xhr.responseText;

                                    if(xhr.statusText == "timeout"){
                                        message = "Connection was timed out due to a slow network connection. Please try again later";
                                    }

                                    loader.html(message);
                                }
                            })
                        }else{
                            showClass = false;
                        }
                        break; 
                    case "class":
                        if(value != ""){
                            class_id = value;
                            showYear = true;
                        }else{
                            showYear = false;
                        }
                        break;
                    case "program_year":
                        showIndex = false;
                        formControl();

                        if(value != ""){
                            ajax = await $.ajax({
                                url: "submit.php",
                                data: {submit: "getStudents", program_id: class_id, year_id: value},
                                method: "GET",
                                timeout: 30000,
                                beforeSend: function(){
                                    loader.removeClass("no_disp").html("Fetching student data, please wait...")
                                },
                                success: function(response){
                                    response = JSON.parse(JSON.stringify(response));
                                    if(response["success"] === true){
                                        showIndex = true;
                                        populateDatalist(response["response"]);
                                        loader.html("").addClass("no_disp");
                                    }else{
                                        loader.html(response["response"] ?? response)
                                    }
                                },
                                error: function(xhr){
                                    let message = xhr.responseText;

                                    if(xhr.statusText == "timeout"){
                                        message = "Connection was timed out due to a slow network connection. Please try again later";
                                    }

                                    loader.html(message);
                                }
                            })
                        }else{
                            showIndex = false;
                        }
                        break;
                
                    default:
                        break;
                }

                formControl();
            })

            function formControl(){
                const program_label = $("label[for=program]");
                const class_label = $("label[for=class]");
                const index_number_label = $("label[for=f_index_number]");
                const year_label = $("label[for=program_year]")
                
                if(showProgram){
                    program_label.removeClass("no_disp");
                }else{
                    program_label.addClass("no_disp");
                    showClass = false; showYear = false; showIndex = false;
                }

                if(showClass){
                    class_label.removeClass("no_disp");
                }else{
                    class_label.addClass("no_disp");
                    showYear = false; showIndex = false;
                }

                if(showIndex){
                    index_number_label.removeClass("no_disp");
                }else{
                    index_number_label.addClass("no_disp");
                    showIndex = false;
                }

                if(showYear){
                    year_label.removeClass("no_disp");
                }else{
                    year_label.addClass("no_disp");
                }
            }

            function populateSelect(select_id, data, first_message, tags = ""){
                const first = "<option value=\"\">" + first_message + "</option>";
                const select = $("select#" + select_id);

                select.html(first);

                if(tags == ""){
                    for(var i = 0; i < data.length; i++){
                        const option = "<option value=\"" + data[i] + "\">" + data[i] + "</option>";
                        select.append(option);
                    }
                }else{
                    tags = tags.split(", ");

                    for(var i = 0; i < data.length; i++){
                        const option = "<option value=\"" + data[i][tags[0]] + "\">" + data[i][tags[1]] + "</option>";
                        select.append(option);
                    }
                }                
            }

            function populateDatalist(data){
                const data_list = $("datalist#students");
                data_list.html("");

                for(var i = 0; i < data.length; i++){
                    for(var i = 0; i < data.length; i++){
                        const option = "<option value=\"" + data[i]["indexNumber"] + "\">" + data[i]["fullname"] + "</option>";
                        data_list.append(option);
                    }
                }
            }

            $("form").submit(function(e){
                e.preventDefault();

                const response = formSubmit($(this), $(this).find("button[name=submit]"), false);

                if(response === true){
                    location.href = "/main";
                }else{
                    alert_box(response, "red");
                }
            })
        })
    </script>
</body>
</html>