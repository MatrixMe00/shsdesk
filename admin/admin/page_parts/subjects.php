<?php 
    if(isset($_REQUEST["school_id"]) && !empty($_REQUEST["school_id"])){
        $user_school_id = $_REQUEST["school_id"];
        $user_details = getUserDetails($_REQUEST["user_id"]);
        
        include_once("../../includes/session.php");
    }else{
        include_once("../../../includes/session.php");
    
        //set nav_point session
        $_SESSION["nav_point"] = "subjects";
    }

    $courses = fetchData1("*","courses","school_id=$user_school_id", 0);
    $teachers = fetchData1("*","teachers","school_id=$user_school_id", 0);
?>

<section class="section_container">
    <div class="content orange">
        <div class="head">
            <h2>
            <?= is_array($courses) ? (isset($courses[0]) ? count($courses) : 1) : 0 ?>
            </h2>
        </div>
        <div class="body">
            <span>Total Courses</span>
        </div>
    </div>

    <div class="content purple">
        <div class="head">
            <h2>
            <?= is_array($teachers) ? (isset($teachers[0]) ? count($teachers) : 1) : 0 ?>
            </h2>
        </div>
        <div class="body">
            <span>Total Teachers</span>
        </div>
    </div>
</section>

<section class="flex-column flex-all-center">
    <div class="head">
        <h1 class="txt-primary">Controls</h1>
    </div>    
    <div class="body control btn flex flex-wrap gap-md">
        <button class="sp-lg xs-rnd primary" data-refresh="false" data-section="courses">View All Courses</button>
        <?php if(is_array($courses)) :?>
        <button class="sp-lg xs-rnd plain indigo" data-refresh="false" data-section="teachers">View All Teachers</button>
        <?php endif; ?>
        <button class="sp-lg xs-rnd plain dark" data-section="add_course">Add new Course</button>
        <?php if (is_array($courses)) : ?>
        <button class="sp-lg xs-rnd plain cyan" data-section="add_teacher">Add new Teacher</button>
        <?php endif; ?>
    </div>
</section>

<section class="section_box sp-xlg-tp hmax-unset-child" id="courses">
    <?php if(is_array($courses)) : ?>
    <div class="head">
        <h1 class="txt-al-c">Courses</h1>
    </div>
    <div class="body">
        <table>
            <thead>
                <td>Course ID</td>
                <td>Course Name</td>
                <td>Course Alias</td>
                <td>Programs Offering</td>
            </thead>
            <tbody>
                <?php for($counter = 0; $counter < (isset($courses[0]) ? count($courses) : 1); $counter++) : $course = isset($courses[0]) ? $courses[$counter] : $courses ?>
                <tr>
                    <td><?= formatItemId($course["course_id"], "CID") ?></td>
                    <td><?= $course["course_name"] ?></td>
                    <td><?= $course["short_form"] ?></td>
                    <td><?= fetchData1("COUNT(*) AS total","program","course_ids LIKE '%".$course["course_id"]."% '")["total"] ?></td>
                    <td>
                        <span class="item-event edit" data-course-id="<?= $course["course_id"] ?>">Edit</span>
                        <span class="item-event delete" data-course-id="<?= $course["course_id"] ?>">Delete</span>
                    </td>
                </tr>
                <?php endfor; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <p class="txt-al-c">No Courses has been uploaded</p>
    <?php endif; ?>
</section>

<section class="sp-xlg-tp hmax-unset-child section_box no_disp" id="add_course">
    <div class="body">
        <form action="<?= $url ?>/admin/admin/submit.php" name="addCourseForm" method="GET">
            <div class="head">
                <h2>Add a New Course</h2>
            </div>
            <div class="message_box no_disp sticky top">
                <span class="message"></span>
                <div class="close"><span>&cross;</span></div>
            </div>
            <div class="body">
                <div class="joint gap-sm">
                    <label for="course_name" class="flex-column gap-sm">
                        <span class="label_title">Course Name</span>
                        <input type="text" name="course_name" id="course_name" placeholder="Course Name">
                    </label>
                    <label for="course_alias" class="flex-column gap-sm">
                        <span class="label_title">Course Alias [Short Form]</span>
                        <input type="text" name="course_alias" id="course_alias" placeholder="Course Alias">
                    </label>
                </div>

                <!-- hidden controls -->
                <input type="hidden" name="school_id" value="<?= $user_school_id ?>">
                
                <div class="btn w-full">
                    <button class="primary sp-lg w-full" name="submit" value="addNewCourse" type="submit">Add Course</button>
                </div>
            </div>
        </form>
    </div>
</section>

<?php if(is_array($courses)) : ?>
    <section class="sp-xlg-tp hmax-unset-child section_box no_disp" id="teachers">
    <?php if(is_array($teachers)) : ?>
    <div class="head">
        <h2 class="txt-al-c">Teachers</h2>
    </div>
    <div class="body">
        <table>
            <thead>
                <td>Teacher ID</td>
                <td>Teacher Name</td>
                <td>Courses Teaching (Number)</td>
            </thead>
            <tbody>
                <?php 
                    for($counter = 0; $counter < (isset($teachers[0]) ? count($teachers) : 1); $counter++) : $teacher = isset($teachers[0]) ? $teachers[$counter] : $teachers ?>
                <tr>
                   <td><?= formatItemId($teacher["teacher_id"], "TID") ?></td>
                    <td><?= strtoupper($teacher["lname"])." ".ucwords($teacher["oname"]) ?></td>
                    <td><?= count(explode(" ",$teacher["course_id"])) - 1 ?></td>
                    <td>
                        <span class="item-event edit">Edit</span>
                        <span class="item-event delete">Delete</span>
                    </td> 
                </tr>
                <?php endfor; ?>
            </tbody>
        </table>
    </div>
    <?php else : ?>
    <p class="txt-al-c">No Teachers have been added to the system</p>
    <?php endif; ?>
</section>

<section class="sp-xlg-tp hmax-unset-child section_box no_disp" id="add_teacher">
    <div class="body">
        <form action="<?= $url ?>/admin/admin/submit.php" name="addTeacherForm" method="GET">
            <div class="head">
                <h2>Add a New Teacher</h2>
            </div>
            <div class="message_box no_disp sticky top">
                <span class="message"></span>
                <div class="close"><span>&cross;</span></div>
            </div>
            <div class="body">
                <div class="joint gap-sm">
                    <label for="teacher_lname" class="flex-column gap-sm">
                        <span class="label_title">Lastname</span>
                        <input type="text" name="teacher_lname" id="teacher_lname" placeholder="Teacher's Lastname">
                    </label>
                    <label for="teacher_oname" class="flex-column gap-sm">
                        <span class="label_title">Othername(s)</span>
                        <input type="text" name="teacher_oname" id="teacher_oname" placeholder="Teacher's Othername(s)">
                    </label>
                    <label for="teacher_gender" class="flex-column gap-sm">
                        <span class="label_title">Select Teacher's Gender</span>
                        <select name="teacher_gender" id="teacher_gender">
                            <option value="">Please select the gender of the user</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </label>
                    <label for="teacher_phone" class="flex-column gap-sm">
                        <span class="label_title">Mobile Number</span>
                        <input type="tel" class="light sp-med" name="teacher_phone" id="teacher_phone" placeholder="Teacher's Mobile Number">
                    </label>
                    <label for="teacher_email" class="flex-column gap-sm">
                        <span class="label_title">Email [Optional]</span>
                        <input type="text" name="teacher_email" id="teacher_email" placeholder="Teacher's Email">
                    </label>
                </div>

                <label for="">
                    <p>Select the courses taught by this teacher</p>
                </label>
                
                <div class="joint" id="courseIDs">
                    <?php for($counter=0; $counter < count($courses); $counter++) : $course = $courses[$counter]; ?>
                    <label for="course_id<?= $counter ?>" class="checkbox">
                        <input type="checkbox" name="course_id" id="course_id<?= $counter ?>" value="<?= $course['course_id'] ?>">
                        <span class="label_title"><?= empty($course["short_form"]) || is_null($course["short_form"]) ? $course["course_name"] : $course["short_form"] ?></span>
                    </label>
                    <?php endfor; ?>
                </div>

                <!-- hidden controls -->
                <input type="hidden" name="school_id" value="<?= $user_school_id ?>">
                <input type="hidden" name="course_ids" value="">
                
                <div class="btn w-full">
                    <button class="primary sp-lg w-full" name="submit" value="addNewTeacher" type="submit">Add Teacher</button>
                </div>
            </div>
        </form>
    </div>
</section>
<?php endif; ?>

<div id="table_del" class="modal_yes_no fixed flex flex-center-content flex-center-align form_modal_box no_disp">
    <?php require_once("$rootPath/admin/admin/page_parts/item_del.php") ?>
</div>

<div id="updateProgram" class="modal_yes_no fixed flex flex-center-content flex-center-align form_modal_box no_disp">
    <div id="updateLoader" class="flex flex-column flex-center-align flex-center-content">
        <div id="getLoader"></div>
        <span class="item-event" id="cancelUpdate" style="color: white; margin-top: 10px; padding-left: 10px; text-align: center">Cancel</span>
    </div>
    <form action="<?= $url ?>/admin/admin/submit.php" name="updateProgramForm" class="wmax-md w-full sm-auto no_disp" method="GET">
        <div class="head">
            <h2>Update Program</h2>
        </div>
        <div class="message_box no_disp">
            <span class="message"></span>
            <div class="close"><span>&cross;</span></div>
        </div>
        <div class="body flex flex-column gap-md">
            <label for="u_program_name" class="flex-column gap-sm">
                <span class="label_title">Name of Program</span>
                <input type="text" name="program_name" id="u_program_name" placeholder="Name of Program">
            </label>

            <label for="">
                <p>Select the courses taught in this program</p>
            </label>

            <?php $courses = fetchData1("*","courses","school_id=$user_school_id", 0); 
                if(is_array($courses)) : ?>
            <div class="joint" id="courseIDs">
                <?php for($counter=0; $counter < count($courses); $counter++) : $course = $courses[$counter]; ?>
                <label for="u_course_id<?= $counter ?>" class="checkbox">
                    <input type="checkbox" name="course_id" id="u_course_id<?= $counter ?>" value="<?= $course['course_id'] ?>">
                    <span class="label_title"><?= empty($course["short_form"]) || is_null($course["short_form"]) ? $course["course_name"] : $course["short_form"] ?></span>
                </label>
                <?php endfor; ?>
            </div>
            <?php else: ?>
                <label for="">
                    <p>Oops, no courses were found. Please add one to continue</p>
                </label>
            <?php endif; ?>

            <!-- hidden inputs -->
            <input type="hidden" name="program_id" value="">
            <input type="hidden" name="course_ids" value="">

            <div class="flex flex-wrap w-full gap-sm flex-eq wmax-xs sm-auto">
                <label for="submit" class="btn w-full sm-unset sp-unset">
                    <button type="submit" name="submit" class="primary w-fluid sp-med xs-rnd" value="updateProgram">Update</button>
                </label>
                <label for="cancel" class="btn w-full sm-unset sp-unset">
                    <button type="reset" name="cancel" class="red w-fluid sp-med xs-rnd" onclick="$('#updateProgram').addClass('no_disp')">Cancel</button>
                </label>
            </div>
        </div>
    </form>
</div>

<script>
    //variable to hold the current active section
    current_section = "courses"

    //effect a change in view depending on the button clicked
    $(".control button").click(function(){
        $(".control button:not(.plain)").addClass("plain")
        $(this).removeClass("plain")

        $(".section_box").addClass("no_disp")
        $("#" + $(this).attr("data-section")).removeClass("no_disp")
    })

    $("#courseIDs label input").change(function(){
        let active_value = $(this).prop("checked")
        let check_value = $(this).prop("value")

        //get content of course ids
        let course_ids = $("input[name=course_ids]").val()
        
        if(active_value){
            course_ids = course_ids.concat(check_value," ")
        }else{
            course_ids = course_ids.replace(check_value + " ", '')
        }

        //push new response into course ids
        $("input[name=course_ids]").val(course_ids)
    })

    $("form").submit(function(e){
        e.preventDefault()

        const response = jsonFormSubmit($(this), $(this).find("button[name=submit]"))
        response.then((return_data)=>{
            return_data = JSON.parse(JSON.stringify(return_data))
            
            messageBoxTimeout($(this).prop("name"), return_data["message"], return_data["status"] ? "success" : "error")

            if(return_data["status"] === true){
                setTimeout(()=> {
                    $(this).find("input:not([type=checkbox], [name=school_id])").val("")
                    $(this).find("input[type=checkbox]").prop("checked", false)
                }, 3000)
            }

            //refresh page if first course
            if($("form").prop("name") === "addCourseForm" && return_data["isFirst"]){
                location.href = location.href
            }
        })
    })

    $(".item-event").click(function(){
        item_id = $(this).attr("data-item-id")
        
        if($(this).hasClass("edit")){
            alert("Feature under development. Try later")
            return
            
            $("#updateProgram").removeClass("no_disp")

            ajaxCall = $.ajax({
                url: $("#updateProgram form").attr("action"),
                data: {
                    program_id: item_id,
                    submit: "getProgram"
                },
                beforeSend: function(){
                    $("#updateLoader").toggleClass("no_disp flex")
                    $("#updateProgram #getLoader").html(loadDisplay({
                        circular: true, 
                        circleColor: "light"
                    }));
                },
                success: function(data){
                    $("#updateProgram #getLoader").html("")
                    $("#updateProgram #getLoader").toggleClass("no_disp flex")
                    $("#updateProgram form").removeClass("no_disp")

                    data = JSON.parse(JSON.stringify(data))
                    const results = data["results"]

                    if(data["status"] === true){
                        $("#updateProgram input[name=program_id]").val(results["program_id"])
                        $("#updateProgram input[name=program_name]").val(results["program_name"])
                        $("#updateProgram input[type=checkbox]").each((index, element)=>{
                            element_val = $(element).val() + " "
                            if(results["course_ids"].includes(element_val)){
                                $(element).prop('checked', true)
                            }
                        })        
                    }else{
                        alert(results)
                    }
                },
                error: function(e){
                    alert(e)
                }
            })
            
        }else if($(this).hasClass("delete")){
            let program_name = $(this).parents("tr").children("td:nth-child(2)").html()
            $("#table_del").removeClass("no_disp")

            //message to display
            $("#table_del p#warning_content").html("Do you want to remove <b>" + program_name + "</b> from your records?");

            //fill form with needed details
            $("#delete_form input[name=item_id]").val(item_id)
            $("#delete_form input[name=table_name]").val("program")
            $("#delete_form input[name=column_name]").val("program_id")

            $(this).parents("tr").addClass("remove_marker");
        }
    })
</script>