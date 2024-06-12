<?php   
    include_once("auth.php");
    
    //set nav_point session
    $_SESSION["nav_point"] = "documents";
?>

<section id="main_menu" class="sp-xlg-tp">
    <p class="txt-al-c">Select the type of document template you want to retrieve</p>
    <div class="btn sm-auto p-lg m-sm">
        <button class="plain-r primary section_btn" data-section="stud_list">Student Lists</button>
        <button class="plain-r primary section_btn" data-section="attendance_list">Attendance List</button>
        <button class="plain-r primary section_btn" data-section="upload">Upload Documents</button>
    </div>
</section>

<section class="btn_section txt-al-c p-xlg-lr p-xxlg-tp txt-fl stud_list">
    <p>Use this section to retrieve document for the list of your students</p>
</section>
<section class="btn_section stud_list">
    <div class="form" name="student_list">
        <h3 class="txt-al-c">Select Your filters</h3>
        <div class="joint gap-sm">
            <label for="program" class="flex-column gap-sm">
                <span class="label_title">Select Program</span>
                <select name="program" id="program">
                    <?php 
                        $sql = "SELECT DISTINCT(programme) FROM students_table WHERE school_id=$user_school_id";
                        $results = $connect2->query($sql);

                        if($results->num_rows > 0){
                            echo "<option value=\"all\">All Programmes</option>";
                            while($row = $results->fetch_assoc()){
                                echo "<option value=\"{$row['programme']}\">{$row['programme']}</option>";
                            }
                        }else{
                            echo "<option value=\"\">No student uploaded</option>";
                        }
                    ?>
                </select>
            </label>
            <label for="program_year" class="flex-column gap-sm">
                <span class="label_title">Select Year</span>
                <select name="program_year" id="program_year">
                    <?php 
                        $sql = "SELECT DISTINCT(studentYear) FROM students_table WHERE school_id=$user_school_id";
                        $results = $connect2->query($sql);

                        if($results->num_rows > 0){
                            echo "<option value=\"all\">All Years</option>";
                            while($row = $results->fetch_assoc()){
                                echo "<option value=\"{$row['studentYear']}\">{$row['studentYear']}</option>";
                            }
                        }else{
                            echo "<option value=\"\">No student uploaded</option>";
                        }
                    ?>
                </select>
            </label>
            <label for="student_gender" class="flex-column gap-sm">
                <span class="label_title">Select Gender</span>
                <select name="student_gender" id="student_gender">
                    <?php 
                        $sql = "SELECT DISTINCT(Gender) FROM students_table WHERE school_id=$user_school_id";
                        $results = $connect2->query($sql);

                        if($results->num_rows > 0){
                            echo "<option value=\"all\">All Genders</option>";
                            while($row = $results->fetch_assoc()){
                                echo "<option value=\"{$row['Gender']}\">{$row['Gender']}</option>";
                            }
                        }else{
                            echo "<option value=\"\">No student uploaded</option>";
                        }
                    ?>
                </select>
            </label>
        </div>
    </div>
</section>

<section class="btn_section txt-al-c p-xlg-lr p-xxlg-tp txt-fl attendance_list">
    <p>Use this section to retrieve class attendance list templates</p>
</section>
<section class="btn_section attendance_list">
    <div class="form" name="attendance_form">
        <h3 class="txt-al-c">Select your filters</h3>
        <div class="joint gap-sm">
            <label for="program_id" class="flex-column gap-sm">
                <span class="label_title">Select the choice of class</span>
                <select name="program_id" id="program_id">
                    <?php 
                        $programs = fetchData1("program_id, program_name","program","school_id=$user_school_id", 0);

                        if(is_array($programs)){
                            echo "<option value=\"all\">All Classes</option>";

                            if(array_key_exists("program_id",$programs)){
                                $pr = $programs;
                                $programs = null;
                                $programs[0] = $pr;
                            }

                            foreach($programs as $program){
                                echo "<option value=\"{$program['program_id']}\">{$program['program_name']}</option>";
                            }
                        }else{
                            echo "<option value=\"\">No student uploaded</option>";
                        }
                    ?>
                </select>
            </label>
            <label for="a_student_year" class="flex-column gap-sm">
                <span class="label_title">Select the program year</span>
                <select name="student_year" id="a_student_year">
                    <?php 
                        $sql = "SELECT DISTINCT(studentYear) FROM students_table WHERE school_id=$user_school_id";
                        $results = $connect2->query($sql);

                        if($results->num_rows > 0){
                            echo "<option value=\"all\">All Years</option>";
                            while($row = $results->fetch_assoc()){
                                echo "<option value=\"{$row['studentYear']}\">{$row['studentYear']}</option>";
                            }
                        }else{
                            echo "<option value=\"\">No student uploaded</option>";
                        }
                    ?>
                </select>
            </label>
            <label for="a_student_semester" class="flex-column gap-sm">
                <span class="label_title">Select a semester</span>
                <select name="student_semester" id="a_student_semester">
                    <option value="">Select a Semester</option>
                    <option value="1">Semester 1</option>
                    <option value="2">Semester 2</option>
                </select>
            </label>
        </div>
    </div>
</section>


<section class="btn_section txt-al-c p-xlg-lr p-xxlg-tp txt-fl upload">
    <p>Use this section to upload your documents such as the attendance list</p>
</section>

<section class="btn_section upload">
    <form class="form" action="admin/excelRead.php" name="upload_form" enctype="multipart/form-data">
        <h3 class="txt-al-c">Please select your documents to upload</h3>
        <div class="joint gap-sm">
            <label for="document_type" class="flex-column self-align-end">
                <span class="label_title">Select type of document</span>
                <select name="document_type" id="document_type">
                    <option value="">Select Document Type</option>
                    <option value="attendance_list">Attendance List</option>
                    <option value="students_list">Students List</option>
                </select>
            </label>
            <label for="document_file" class="file_label">
                <span class="label_title self-align-center">Provide the document to upload</span>
                <div class="fore_file_display">
                    <input type="file" name="document_file" id="document_file" accept=".xlsx">
                    <span class="plus">+</span>
                    <span class="display_file_name">Choose or drag your file here</span>
                </div>
            </label>
        </div>
    </form>
</section>

<section class="btn_section attendance_list stud_list">
    <div class="btn wmax-sm flex flex-eq flex-wrap gap-sm w-full sm-auto p-lg">
        <button class="cyan w-full" name="submit" value="get_document">Get Document</button>
        <button class="success w-full no_disp" name="download_btn">Download</button>
        <a href="" id="download_anchor" class="no_disp"></a>
    </div>
</section>

<section class="btn_section upload">
    <div class="btn wmax-sm w-full sm-auto p-lg">
        <button class="cyan w-full upload_btn" name="submit" value="upload_document">Upload Document</button>
    </div>
</section>

<script>
    $(document).ready(function(){
        $(".btn_section").addClass("no_disp")

        $(".section_btn").click(function(){
            const section_id = $(this).attr("data-section");

            $(".btn_section").addClass("no_disp")
            $("." + section_id).removeClass("no_disp")

            $(".section_btn:not(.plain-r)").addClass("plain-r")
            $(this).removeClass("plain-r")

            $("button[name=download_btn]").addClass("no_disp")
        })

        var downloadUrl = ""; downloadTitle = "";

        $("button[name=submit]").click(function(){
            const formName = $(".section_btn:not(.plain-r)").attr("data-section");
            const form = $(".btn_section."+ formName +" .form")
            let form_data = {}
            let title = "";
            
            if($(this).val() == "get_document"){
                switch(formName){
                    case "stud_list":
                        form_data = {
                            submit: "student_list",
                            program_name: $(form).find("select[name=program]").val(),
                            program_year: $(form).find("select[name=program_year]").val(),
                            gender: $(form).find("select[name=student_gender]").val()
                        }
                        title = "Student List"
                        break;
                    case "attendance_list":
                        form_data = {
                            submit: "attendance_list",
                            program_id: $(form).find("select[name=program_id]").val(),
                            program_year: $(form).find("select[name=student_year]").val(),
                            semester: $(form).find("select[name=student_semester]").val()
                        }
                        title = "Attendance List"
                        break;
                }
                location.href = "./admin/excelFile.php?" + jsonToURL($form_data);
            }else if($(this).val() == "upload_document"){
                $("form[name=upload_form]").submit()
            }
        })

        $("button[name=download_btn]").click(function(){
            $("#download_anchor").attr("href",downloadUrl)
            $("#download_anchor").attr("download", downloadTitle)
            $("#download_anchor")[0].click()
        })

        //concerning the files that will be chosen
        $("input[type=file]").change(function(){
            //get the value of the image name
            const image_path = $(this).val();

            //strip the path name to file name only
            const image_name = image_path.split("C:\\fakepath\\");

            //store the name of the file into the display div
            if(image_path != ""){
                $(this).siblings(".plus").hide();
                $(this).siblings(".display_file_name").html(image_name);       
            }else{
                $(this).siblings(".plus").css("display","initial");
                $(this).siblings(".display_file_name").html("Choose or drag your file here");
            }
        })

        $("form[name=upload_form]").submit(async function(e){
            e.preventDefault();

            response = await fileUpload($(this), $("button.upload_btn"), false)
            if(response == true){
                alert_box("upload finish")
            }else{
                alert_box(response, "danger")
            }
        })
    })
</script>