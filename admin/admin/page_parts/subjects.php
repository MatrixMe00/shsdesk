<?php 
    if(isset($_REQUEST["school_id"]) && !empty($_REQUEST["school_id"])){
        $user_school_id = $_REQUEST["school_id"];
        $user_details = getUserDetails($_REQUEST["user_id"]);
        
        include("../../includes/session.php");
    }else{
        include("../../../includes/session.php");
    
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

<section class="section_box sp-xlg-tp hmax-unset-child" id="courses" data-table="courses" data-table-col="course_id">
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
                <td>Credit Hours</td>
            </thead>
            <tbody>
                <?php for($counter = 0; $counter < (isset($courses[0]) ? count($courses) : 1); $counter++) : $course = isset($courses[0]) ? $courses[$counter] : $courses ?>
                <tr>
                    <td><?= formatItemId($course["course_id"], "CID") ?></td>
                    <td><?= $course["course_name"] ?></td>
                    <td><?= $course["short_form"] ?></td>
                    <td><?= fetchData1("COUNT(*) AS total","program","course_ids LIKE '%".$course["course_id"]."% '")["total"] ?></td>
                    <td><?= is_null($course["credit_hours"]) ? "Not Set" : $course["credit_hours"] ?></td>
                    <td>
                        <span class="item-event edit" data-item-id="<?= $course["course_id"] ?>">Edit</span>
                        <span class="item-event delete" data-item-id="<?= $course["course_id"] ?>">Delete</span>
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
    <?php require "$url/admin/admin/page_parts/subTeaForms.php?form_type=course_add&school_id=$user_school_id" ?>
    </div>
</section>

<?php if(is_array($courses)) : ?>
    <section class="sp-xlg-tp hmax-unset-child section_box no_disp" id="teachers" data-table="teachers" data-table-col="teacher_id">
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
                        <span class="item-event edit" data-item-id="<?= $teacher["teacher_id"] ?>">Edit</span>
                        <span class="item-event delete" data-item-id="<?= $teacher["teacher_id"] ?>">Delete</span>
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
    <?php require "$url/admin/admin/page_parts/subTeaForms.php?form_type=teacher_add&school_id=$user_school_id" ?>
    </div>
</section>
<?php endif; ?>

<div id="table_del" class="modal_yes_no fixed flex flex-center-content flex-center-align form_modal_box no_disp">
    <?php require_once("$rootPath/admin/admin/page_parts/item_del.php") ?>
</div>

<div id="updateItem" class="modal_yes_no fixed flex flex-center-content flex-center-align form_modal_box no_disp">
    <div id="updateLoader" class="no_disp flex-column flex-center-align flex-center-content">
        <div id="getLoader"></div>
        <span class="item-event" id="cancelUpdate" style="color: white; margin-top: 10px; padding-left: 10px; text-align: center">Cancel</span>
    </div>
    <?php require "$url/admin/admin/page_parts/subTeaForms.php?form_type=course_update&school_id=$user_school_id" ?>
    <?php require "$url/admin/admin/page_parts/subTeaForms.php?form_type=teacher_update&school_id=$user_school_id" ?>
</div>

<script>
    //variable to hold the current active section
    current_section = $("#lhs .item.active[data-tab]").attr("data-tab")
    ajaxCall = null

    $(document).ready(function(){
        $(".control button[data-section=" + current_section + "]").click()
    })

    //effect a change in view depending on the button clicked
    $(".control button").click(function(){
        $(".control button:not(.plain)").addClass("plain")
        $(this).removeClass("plain")

        $(".section_box").addClass("no_disp")
        $("#lhs .item.active").attr("data-tab", $(this).attr("data-section"))
        $("#" + $(this).attr("data-section")).removeClass("no_disp")

        if($(this).attr("data-refresh") && $(this).attr("data-refresh") == "true"){
            location.href = location.href
        }
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

        const formName = $(this).attr("name")

        if(formName === "delete_form"){
            const response = formSubmit($(this), $(this).find("input[name=submit]"), false)
            
            if(response === true || response === "true"){
                $("tr.remove_marker").remove()

                num = parseInt($(".content.orange").find("h2").html()) - 1
                $(".content.orange").find("h2").html(num)

                $("#table_del").addClass("no_disp")
            }else{
                $("tr.remove_marker").removeClass("remove_marker")
            }
        }else{
            const response = jsonFormSubmit($(this), $(this).find("button[name=submit]"))
            response.then((return_data)=>{
                return_data = JSON.parse(JSON.stringify(return_data))
                
                messageBoxTimeout($(this).prop("name"), return_data["message"], return_data["status"] ? "success" : "error")

                if(return_data["status"] === true || return_data["status"] === "true"){
                    if(!formName.includes("update")){
                        setTimeout(()=> {
                            $(this).find("input:not([type=checkbox], [name=school_id])").val("")
                            $(this).find("input[type=checkbox]").prop("checked", false)
                        }, 3000)
                    }
                    
                    $(".control button").attr("data-refresh","true")
                }

                //refresh page if first course
                if($("form").prop("name") === "addCourseForm" && return_data["isFirst"]){
                    location.href = location.href
                }
            })
        }
        
    })

    $(".item-event").click(function(){
        const item_id = $(this).attr("data-item-id")
        const table = $(this).parents("section").attr("data-table")
        const table_col = $(this).parents("section").attr("data-table-col")
        const this_eventBtn = $(this)

        if($(this).hasClass("edit")){
            let formName = ""

            if(table == "courses"){
                formName = "updateCourseForm"
            }else if(table == "teachers"){
                formName = "updateTeacherForm"
            }
            
            $("#updateItem form").addClass("no_disp")
            $("#updateItem").removeClass("no_disp")

            ajaxCall = $.ajax({
                url: $("#updateItem form[name=" + formName + "]").attr("action"),
                data: {
                    item_id: item_id,
                    item_table: table,
                    item_table_col: table_col,
                    submit: "getItem"
                },
                beforeSend: function(){
                    $("#updateLoader").addClass("flex").removeClass("no_disp")
                    $("#updateItem #getLoader").html(loadDisplay({
                        circular: true, 
                        circleColor: "light"
                    }));
                },
                success: function(data){
                    $("#updateItem #getLoader").html("")
                    $("#updateLoader").addClass("no_disp").removeClass("flex")

                    data = JSON.parse(JSON.stringify(data))
                    
                    const results = data["results"][0]
                    if(data["status"] === true){
                        if(table === "courses"){
                            $("#updateItem span#courseID").html($(this_eventBtn).parents("tr").children("td:first-child").html())
                            $("#updateItem form[name=" + formName + "] input[name=course_name]").val(results["course_name"])
                            $("#updateItem form[name=" + formName + "] input[name=course_alias]").val(results["short_form"])
                            $("#updateItem form[name=" + formName + "] input[name=course_id]").val(results["course_id"])
                            $("#updateItem form[name=" + formName + "] input[name=course_credit]").val(results["credit_hours"] == null ? 0 : results["credit_hours"])
                        }else{
                            $("#updateItem form[name=" + formName + "] span#teacherID").html($(this_eventBtn).parents("tr").children("td:first-child").html())
                            $("#updateItem form[name=" + formName + "] input[name=teacher_lname]").val(results["lname"])
                            $("#updateItem form[name=" + formName + "] input[name=teacher_oname]").val(results["oname"])
                            $("#updateItem form[name=" + formName + "] select[name=teacher_gender]").val(results["gender"]).change()
                            $("#updateItem form[name=" + formName + "] input[name=teacher_phone]").val(results["phone_number"])
                            $("#updateItem form[name=" + formName + "] input[name=course_ids]").val(results["course_id"])
                            $("#updateItem form[name=" + formName + "] input[name=teacher_email]").val(results["email"])
                            $("#updateItem form[name=" + formName + "] input[name=teacher_id]").val(results["teacher_id"])

                            $("#updateItem form[name=" + formName + "] input[type=checkbox]").each((index, element)=>{
                                element_val = $(element).val() + " "
                                $(element).prop('checked', false)
                                if(results["course_id"].includes(element_val)){
                                    $(element).prop('checked', true)
                                }
                            })
                        }

                        $("#updateItem form[name="+ formName + "]").removeClass("no_disp")
                    }else{
                        alert(results)
                    }

                },
                error: function(e){
                    alert(e)
                }
            })
            
        }else if($(this).hasClass("delete")){
            let item_name = $(this).parents("tr").children("td:nth-child(2)").html()
            $("#table_del").removeClass("no_disp")

            //message to display
            $("#table_del p#warning_content").html("Do you want to remove <b>" + item_name + "</b> from your records?");

            //fill form with needed details
            $("#delete_form input[name=item_id]").val(item_id)
            $("#delete_form input[name=table_name]").val(table)
            $("#delete_form input[name=column_name]").val(table_col)

            $(this).parents("tr").addClass("remove_marker");
        }
    })

    $("#cancelUpdate").click(function(){
        if(ajaxCall){
            ajaxCall.abort()
        }

        $("#updateItem").addClass("no_disp")
    })
</script>