<?php
    include_once("auth.php");
    //set nav_point session
    $_SESSION["nav_point"] = "students";

    $stud_sum = [
        "total" => fetchData("COUNT(indexNumber) AS ttl", "cssps", "current_data=TRUE")["ttl"],
        "enroled" => fetchData("COUNT(indexNumber) AS ttl", "cssps", ["current_data=TRUE", "enroled=TRUE"], 1, "AND")["ttl"],
        "not_enroled" => fetchData("COUNT(indexNumber) AS ttl", "cssps", ["current_data=TRUE", "enroled=FALSE"], 1, "AND")["ttl"]
    ];

?>

<section class="section_container">
    <div class="content primary">
        <div class="head">
            <h2><?= number_format(floatval($stud_sum["total"])) ?></h2>
        </div>
        <div class="body">
            <span>Total Admitted Student [Current]</span>
        </div>
    </div>
    <div class="content secondary">
        <div class="head">
            <h2><?= number_format(floatval($stud_sum["enroled"])) ?></h2>
        </div>
        <div class="body">
            <span>Enroled Students [Current]</span>
        </div>
    </div>
    <div class="content orange">
        <div class="head">
            <h2><?= number_format(floatval($stud_sum["not_enroled"])) ?></h2>
        </div>
        <div class="body">
            <span>Not Enroled Students [Current]</span>
        </div>
    </div>
</section>

<section class="txt-al-c">
    <p>Use this section to make searches and edits to information of an individual student</p>
</section>

<section>
    <!-- make search here -->
    <form action="<?= "$url/admin/superadmin/submit.php" ?>" method="GET" name="search-student">
        <div class="head">
            <h2>Search a student data</h2>
        </div>
        <div class="body">
            <div class="search flex flex-wrap">
                <label for="txt_search" style="flex: 1; border: 1px solid grey">
                    <input type="text" name="txt_search" id="txt_search" placeholder="Enter search item [default is index number]">
                </label>
                <label for="submit" class="btn">
                    <button type="submit" name="submit" value="search_student">Search</button>
                </label>
            </div>
            <div class="search_options flex flex-wrap">
                <label for="enrolCode" class="checkbox gap-sm">
                    <input type="checkbox" name="enrolCode" id="enrolCode">
                    <span class="span_title">Search By Enrolment Code</span>
                </label>
                <label for="current_ad" class="checkbox gap-sm">
                    <input type="checkbox" name="current_ad" id="current_ad">
                    <span class="span_title">Current Admission Only</span>
                </label>
            </div>
        </div>
    </form>
</section>

<section id="loader" class="txt-al-c no_disp">
    <div class="body empty p-xlg-tp p-lg-lr">
        <p id="message"></p>
    </div>
</section>

<section id="form" class="no_disp">
    <form action="<?= "$url/admin/superadmin/submit.php" ?>" method="POST" name="update-student">
        <div class="head">
            <h3>Student Details - <span id="index_number"></span></h3>
        </div>
        <div class="body">
            <div class="message_box success no_disp">
                <span class="message"></span>
                <div class="close"><span>&cross;</span></div>
            </div>
            <fieldset>
                <legend>Non-Changable Fields</legend>
                <div class="joint">
                    <label for="indexNumber" class="flex-column gap-sm">
                        <span class="label_title">Index Number</span>
                        <input type="text" name="indexNumber" id="indexNumber" readonly>
                    </label>
                    <label for="lname" class="flex-column gap-sm">
                        <span class="label_title">Lastname</span>
                        <input type="text" name="lname" id="lname" readonly>
                    </label>
                    <label for="oname" class="flex-column gap-sm">
                        <span class="label_title">Othername(s)</span>
                        <input type="text" name="oname" id="oname" readonly>
                    </label>
                    <label for="gender" class="flex-column gap-sm">
                        <span class="label_title">Gender</span>
                        <input type="text" name="gender" id="gender" readonly>
                    </label>
                    <label for="school" class="flex-column gap-sm">
                        <span class="label_title">School</span>
                        <input type="text" name="school" id="school" readonly>
                        <input type="hidden" name="school_id" id="school_id">
                    </label>
                    <label for="program" class="flex-column gap-sm">
                        <span class="label_title">Program</span>
                        <input type="text" name="program" id="program" readonly>
                    </label>
                    <label for="primary_phone" class="flex-column gap-sm">
                        <span class="label_title">Primary Phone Number</span>
                        <input type="text" name="primary_phone" id="primary_phone" readonly>
                    </label>
                    <label for="secondary_phone" class="flex-column gap-sm">
                        <span class="label_title">Secondary Phone Number</span>
                        <input type="text" name="secondary_phone" id="secondary_phone" readonly>
                    </label>
                    <label for="student_house" class="flex-column gap-sm">
                        <span class="label_title">House Allocated</span>
                        <input type="text" name="student_house" id="student_house" readonly>
                    </label>
                    <label for="boarding_status" class="flex-column gap-sm">
                        <span class="label_title">Boarding Status</span>
                        <input type="text" name="boarding_status" id="boarding_status" readonly>
                    </label>
                </div>
            </fieldset>

            <fieldset class="sm-lg-t">
                <legend>Changeable Fields</legend>
                <div class="joint">
                    <label for="enrol_code" class="flex-column gap-sm">
                        <span class="label_title">Enrolment Code</span>
                        <input type="text" name="enrol_code" id="enrol_code" maxlength="6">
                    </label>
                </div>
            </fieldset>

            <div class="btn w-full flex-wrap flex flex-eq gap-sm sm-auto wmax-3xs sm-lg-t">
                <button name="submit" type="button" class="primary" disabled>Save</button>
                <button type="reset" name="close" class="red">Close</button>
            </div>
        </div>
    </form>
</section>

<script>
    $(document).ready(function(){
        function fillForm(data){
            $("#indexNumber").val(data.indexNumber);
            $("#lname").val(data.Lastname);
            $("#oname").val(data.Othernames);
            $("#gender").val(data.Gender);
            $("#school").val(data.schoolName);
            $("#school_id").val(data.schoolID);
            $("#program").val(data.programme);
            $("#primary_phone").val(data.primaryPhone);
            $("#secondary_phone").val(data.secondaryPhone);
            $("#student_house").val(data.house_name);
            $("#enrol_code").val(data.enrolCode);
            $("#boarding_status").val(data.boardingStatus);

            // index number at top
            const is_enroled = data.enrolCode.toLowerCase() != "not set" ?
                "Enroled" : "Not Enroled";
            
            $("span#index_number").html(data.indexNumber + " [" + is_enroled + "]");
        }

        $("form").submit(async function(e){
            e.preventDefault();
            const form_name = $(this).attr("name");
            let message = null;
            let response = null;
            let type = "danger";
            let time = 5;

            if(form_name == "search-student"){
                const isCode = $("input#enrolCode").prop("checked") ? 1 : 0;
                const isCurrent = $("input#enrolCode").prop("checked") ? 1 : 0;
                
                await $.ajax({
                    url: $(this).attr("action"),
                    data: {
                        submit: $(this).find("button[name=submit]").val(),
                        enrolCode: isCode, current: isCurrent,
                        search: $("#txt_search").val()
                    },
                    dataType: "json",
                    beforeSend: function(){
                        $("section#form").addClass("no_disp");
                        $("section#loader").removeClass("no_disp").find("#message").html("Searching Student...");

                        //reset fillable form
                        $("form[name=update-student]")[0].reset();
                    },
                    success: function(data){
                        if(data.error){
                            $("section#loader").find("#message").html(data.data);
                        }else{
                            $("section#loader").addClass("no_disp").find("#message").html("");

                            // fill form
                            fillForm(data.data);

                            $("section#form").removeClass("no_disp");
                        }                        
                    },
                    error: function(xhr, textStatus, errorThrown){
                        message = errorThrown;
                        console.log(xhr);
                    }
                })
            }else{
                message = formSubmit($(this), $(this).find("button[name=submit]"));
            }
        })

        $("button[name=close]").click(function(){
            $("span#index_number").html("");
            $("#form").addClass("no_disp");
        })
    })
</script>
