<?php 
    if(isset($_REQUEST["school_id"]) && !empty($_REQUEST["school_id"])){
        $user_school_id = $_REQUEST["school_id"];
        $user_details = getUserDetails($_REQUEST["user_id"]);
        
        include_once("../../includes/session.php");
    }else{
        include_once("../../../includes/session.php");
    
        //set nav_point session
        $_SESSION["nav_point"] = "programs";
    }

    //retrieve programs
    $programs = fetchData1("*","program","school_id=$user_school_id", 0);
    $resultPending = fetchData1(
        "r.record_id, r.result_token, r.submission_date, t.lname, t.oname, p.program_name, p.short_form",
        "recordapproval r JOIN program p ON p.program_id = r.program_id
        JOIN teachers t ON t.teacher_id = r.teacher_id",
        "r.school_id=$user_school_id AND r.result_status='pending' ORDER BY r.submission_date DESC", 0
    );
    $resultAttended = fetchData1(
        "r.record_id, r.result_token, r.result_status, r.submission_date, t.lname, t.oname, p.program_name, p.short_form",
        "recordapproval r JOIN program p ON p.program_id = r.program_id
        JOIN teachers t ON t.teacher_id = r.teacher_id",
        "r.school_id=$user_school_id AND r.result_status != 'pending' ORDER BY r.submission_date DESC", 0
    );
?>

<section class="section_container">
    <div class="content orange">
        <div class="head">
            <h2>
                <?= is_array($programs) ? (isset($programs[0]) ? count($programs) : 1) : 0; ?>
            </h2>
        </div>
        <div class="body">
            <span>Total Classes</span>
        </div>
    </div>

    <div class="content <?= $resultPending == "empty" ? "green" : "red" ?>">
        <div class="head">
            <h2>
                <?= is_array($resultPending) ? (isset($resultPending[0]) ? count($resultPending) : 1) : 0; ?>
            </h2>
        </div>
        <div class="body">
            <span>Pending Result Approvals</span>
        </div>
    </div>
</section>

<section class="flex-column flex-all-center">
    <h1 class="txt-primary">Controls</h1>
    <div class="body btn flex flex-wrap gap-md">
        <button class="control_btn sp-lg xs-rnd primary" data-section="allPrograms" data-refresh="false" id="viewAll">View All Classes</button>
        <button class="control_btn sp-lg xs-rnd plain secondary" data-section="newProgram">Add new Class</button>
        <button class="control_btn sp-lg xs-rnd plain yellow color-dark" data-section="pendingResults">Pending results</button>
        <button class="control_btn sp-lg xs-rnd plain teal" data-section="reviewedResults">Reviewed results</button>
    </div>
</section>

<section id="allPrograms" class="sp-xlg-tp section_box">
    <?php if(is_array($programs)) : ?>
    <div class="head">
        <h2 class="txt-al-c">Your Classes</h2>
    </div>
    <div class="body">
        <table>
            <thead>
                <td>Class ID</td>
                <td>Class Name</td>
                <td>Alias Name</td>
                <td>Class Course Count</td>
            </thead>
            <tbody>
                <?php for($counter = 0; $counter < (isset($programs[0]) ? count($programs) : 1); $counter++) : $program = isset($programs[0]) ? $programs[$counter] : $programs ?>
                <tr>
                    <td><?= formatItemId($program["program_id"], "PID") ?></td>
                    <td><?= $program["program_name"] ?></td>
                    <td><?= is_null($program["short_form"]) ? "Not Set" : $program["short_form"] ?></td>
                    <td><?= count(explode(" ", $program["course_ids"])) - 1 ?></td>
                    <td>
                        <span class="item-event edit" data-item-id="<?= $program["program_id"] ?>">Edit</span>
                        <span class="item-event delete" data-item-id="<?= $program["program_id"] ?>">Delete</span>
                    </td>
                </tr>
                <?php endfor; ?>
            </tbody>
        </table>
    </div>
    <?php else : ?>
    <p class="txt-al-c">No Programs have been added yet</p>
    <?php endif; ?>
</section>

<section id="pendingResults" class="section_box no_disp">
    <?php
        if(is_array($resultPending)) :
    ?>
    <table class="relative">
        <thead>
            <td>No.</td>
            <td>Class</td>
            <td>Teacher</td>
            <td>Submission Date</td>
        </thead>
        <tbody>
        <?php for($counter = 0; $counter < (isset($resultPending[0]) ? count($resultPending) : 1); $counter++) : $result = isset($resultPending[0]) ? $resultPending[$counter] : $resultPending ?>
            <tr>
                <td><?= ($counter + 1) ?></td>
                <td><?= is_null($result["short_form"]) ? $result["program_name"] : $result["short_form"] ?></td>
                <td><?= $result["lname"]." ".$result["oname"] ?></td>
                <td><?= date("m d, Y H:i:s", strtotime($result["submission_date"])) ?></td>
                <td>
                    <span class="item-event approve" data-item-id="<?= $result["record_id"] ?>" data-item-token="<?= $result["result_token"] ?>">Approve</span>
                    <span class="item-event reject" data-item-id="<?= $result["record_id"] ?>" data-item-token="<?= $result["result_token"] ?>">Reject</span>
                </td>
            </tr>
            <?php $counter++; endfor; ?>
        </tbody>
        <tfoot>
            <td colspan="5" class="res_stat">Status: </td>
        </tfoot>
    </table>
    <?php else : ?>
    <div class="empty txt-al-c p-xxlg p-med">
        <p class="border b-secondary">There are no pending results requiring approval yet</p>
    </div>
    <?php endif; ?>
</section>

<section id="reviewedResults" class="section_box no_disp">
<?php
        if(is_array($resultAttended)) :
    ?>
    <table class="relative">
        <thead>
            <td>No.</td>
            <td>Class</td>
            <td>Teacher</td>
            <td>Submission Date</td>
        </thead>
        <tbody>
            <?php for($counter = 0; $counter < (isset($resultAttended[0]) ? count($resultAttended) : 1); $counter++) : $result = isset($resultAttended[0]) ? $resultAttended[$counter] : $resultAttended ?>
            <tr <?= $result["result_status"] === "rejected" ? 'class="red"' : '' ?>>
                <td><?= ($counter+1) ?></td>
                <td><?= is_null($result["short_form"]) ? $result["program_name"] : $result["short_form"] ?></td>
                <td><?= $result["lname"]." ".$result["oname"] ?></td>
                <td><?= date("m d, Y H:i:s", strtotime($result["submission_date"])) ?></td>
                <td>
                    <?php if($result["result_status"] === "rejected") : ?>
                    <span class="item-event approve" data-item-id="<?= $result["record_id"] ?>" data-item-token="<?= $result["result_token"] ?>">Approve</span>
                    <?php else : ?>
                    <span class="item-event reject" data-item-id="<?= $result["record_id"] ?>" data-item-token="<?= $result["result_token"] ?>">Reject</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endfor; ?>
        </tbody>
        <tfoot>
            <td colspan="5" class="res_stat">Status: </td>
        </tfoot>
    </table>
    <?php else : ?>
    <div class="empty txt-al-c p-xxlg p-med">
        <p class="border b-secondary">There are no reviewed results yet</p>
    </div>
    <?php endif; ?>
</section>

<section id="newProgram" class="sp-xlg-tp section_box no_disp">
    <?php eval("?>".file_get_contents("$url/admin/admin/page_parts/subTeaForms.php?form_type=addProgram&school_id=$user_school_id")) ?>
</section>

<div id="table_del" class="modal_yes_no fixed flex flex-center-content flex-center-align form_modal_box no_disp">
    <?php require_once("$rootPath/admin/admin/page_parts/item_del.php") ?>
</div>

<div id="updateProgram" class="modal_yes_no fixed flex flex-center-content flex-center-align form_modal_box no_disp">
    <div id="updateLoader" class="flex flex-column flex-center-align flex-center-content">
        <div id="getLoader"></div>
        <span class="item-event" id="cancelUpdate" style="color: white; margin-top: 10px; padding-left: 10px; text-align: center">Cancel</span>
    </div>
    <?php eval("?>".file_get_contents("$url/admin/admin/page_parts/subTeaForms.php?form_type=updateProgram&school_id=$user_school_id")) ?>
</div>

<script>
    current_section = $("#lhs .item.active[data-tab]").attr("data-tab")
    ajaxCall = null

    $(document).ready(function(){
        $(".control_btn[data-section=" + current_section + "]").click()
    })

    $("#courseIDs label input").change(function(){
        let active_value = $(this).prop("checked")
        let check_value = $(this).prop("value")
        let parentForm = $(this).parents("form")

        //get content of course ids
        let course_ids = $(parentForm).find("input[name=course_ids]").val()
        
        if(active_value){
            course_ids = course_ids.concat(check_value," ")
        }else{
            course_ids = course_ids.replace(check_value + " ", '')
        }

        //push new response into course ids
        $("input[name=course_ids]").val(course_ids)
    })

    $("form[name=addProgramForm]").submit(function(e){
        e.preventDefault()
        
        const response = jsonFormSubmit($(this), $(this).find("button[name=submit]"))
        response.then((return_data)=>{
            return_data = JSON.parse(JSON.stringify(return_data))
            
            messageBoxTimeout($(this).prop("name"), return_data["message"], return_data["status"] ? "success" : "error")

            if(return_data["status"] === true){
                num = parseInt($(".content.orange").find("h2").html()) + 1
                $(".content.orange").find("h2").html(num)

                $("#viewAll").attr("data-refresh","true")

                setTimeout(()=> {
                    $(this).find("input:not([type=checkbox], [name=school_id])").val("")
                    $(this).find("input[type=checkbox]").prop("checked", false)
                }, 3000)
            }         
        })
    })

    //direct displays to the control buttons
    $(".control_btn").click(function(){
        const section = $(this).attr("data-section")

        $(".section_box:not(.no_disp)").addClass("no_disp")
        $("#lhs .item.active").attr("data-tab", section)
        $("#" + $(this).attr("data-section")).removeClass("no_disp")

        $(".control_btn:not(.plain)").addClass("plain")
        $(this).removeClass("plain")

        if($(this).attr("data-refresh") && $(this).attr("data-refresh") === "true"){
            $("#lhs.menu .item.active").click()
        }
    })

    $(".item-event").click(function(){
        item_id = $(this).attr("data-item-id")
        
        if($(this).hasClass("edit")){
            $("#updateProgram").removeClass("no_disp")
            
            ajaxCall = $.ajax({
                url: $("#updateProgram form").attr("action"),
                data: {
                    program_id: item_id,
                    submit: "getProgram"
                },
                timeout: 15000,
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
                        $("#updateProgram input[name=short_form]").val(results["short_form"])
                        $("#updateProgram input[name=course_ids]").val(results["course_ids"])  
                    }else{
                        alert(results)
                    }
                },
                error: function(xhr, textStatus){
                    let message = ""

                    if(textStatus === "timeout"){
                        message = "Connection was timed out due to a slow network. Please check your internet connection and try again"
                    }else if(textStatus === "parsererror"){
                        message = "Status: Data returned cannot be parsed. Please try again later else contact admin for help"
                    }else{
                        message = "Status: " + xhr.responseText
                    }

                    alert_box(message, "warning color-dark", 10)
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
        }else if($(this).hasClass("approve") || $(this).hasClass("reject")){
            const item_status = $(this).hasClass("approve") ? "accepted" : "rejected"
            const item_token = $(this).attr("data-item-token")
            const table_foot = $(this).parents("table").find("tfoot")
            const this_row = $(this).parents("tr")
            
            $.ajax({
                url: $("#updateProgram form").attr("action"),
                data: {
                    record_id: item_id, record_token: item_token,
                    record_status: item_status, submit: "result_status_change"
                },
                type: "POST",
                dataType: "json",
                timeout: 15000,
                cache: false,
                beforeSend: function(){
                    $(table_foot).addClass("sticky top secondary w-full")
                    $(table_foot).find(".res_stat").html("Status: Updating...")
                },
                success: function(response){
                    $(table_foot).removeClass("sticky top secondary w-full")

                    if(typeof response["status"] && response['status'] === true){
                        $(this_row).remove()
                        $(table_foot).find(".res_stat").html("The record was " + response["rec_stat"])
                        
                        //refresh this page
                        $("#lhs .item.active").click()
                    }else{
                        $(table_foot).find(".res_stat").html(response["message"])
                    }
                },
                error: function(xhr, textStatus){
                    let message = ""
                    if(textStatus === "timeout"){
                        message = "Status: Connection was timed out. Please check your network connection and try again"
                    }else if(textStatus === "parsererror"){
                        message = "Status: Data returned cannot be parsed. Please try again later else contact admin for help"
                    }else{
                        message = "Status: " + xhr.responseText
                    }
                    
                    $(table_foot).find(".res_stat").html(message)
                }
            })
        }
    })

    $("form[name=delete_form]").submit(function(e){
        e.preventDefault()

        const response = formSubmit($(this), $(this).find("input[name=submit]"), false)
        
        if(response === true || response === "true"){
            $("tr.remove_marker").remove()

            num = parseInt($(".content.orange").find("h2").html()) - 1
            $(".content.orange").find("h2").html(num)

            $("#table_del").addClass("no_disp")
        }else{
            $("tr.remove_marker").removeClass("remove_marker")
        }
    })

    $("#cancelUpdate").click(function(){
        if(ajaxCall){
            ajaxCall.abort()
        }else{
            alert("no ajax")
        }

        $("#updateProgram").addClass("no_disp")
    })

    $("form[name=updateProgramForm]").submit(function(e){
        e.preventDefault()

        const response = jsonFormSubmit($(this), $(this).find("button[name=submit]"))
        response.then((return_data)=>{
            return_data = JSON.parse(JSON.stringify(return_data))
            
            messageBoxTimeout($(this).prop("name"), return_data["message"], return_data["status"] ? "success" : "error")

            if(return_data["status"] === true || return_data["status"] === "true"){                
                $("button.control_btn").attr("data-refresh","true")
            }

            //refresh page if first course
            if($("form").prop("name") === "addCourseForm" && return_data["isFirst"]){
                location.href = location.href
            }
        })
    })
</script>