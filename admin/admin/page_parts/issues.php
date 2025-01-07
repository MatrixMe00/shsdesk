<?php
    require "auth.php";

    $_SESSION["nav_point"] = "issues";
    $academic_year = getAcademicYear(now(), false);

    $n_enroled = decimalIndexArray(fetchData("c.indexNumber, c.Lastname, c.Othernames, e.enrolDate", 
        ["join" => "enrol_table cssps", "on" => "indexNumber indexNumber", "alias" => "e c"],
        ["c.schoolID=$user_school_id", "c.current_data=TRUE", "e.academic_year='$academic_year'", "c.Enroled=FALSE"], 
        0, "AND", "left"));
    $enroled = fetchData("COUNT(indexNumber) as total", "enrol_table", "shsID=$user_school_id AND current_data = TRUE AND academic_year = '$academic_year'")["total"];

    // get students of old students currently admitted
    $old_in_new = decimalIndexArray(fetchData(
        ["c.indexNumber", "c.Lastname", "c.Othernames", "e.enrolDate", "c.accept_old"],
        [
            "join" => "enrol_table cssps", "on" => "indexNumber indexNumber", "alias" => "e c",
        ],
        ["e.shsID=$user_school_id", "e.current_data = TRUE", "e.indexNumber NOT LIKE '%$index_end'", "e.academic_year = '$academic_year'"],
        0, "AND"
    ));

    // boarding issues
    $boarding_issues = decimalIndexArray(fetchData(
        ["indexNumber", "Lastname", "Othernames", "programme"], 
        "cssps", "schoolID=$user_school_id AND boardingStatus = ''", 0));
?>

<section class="section_container">
    <div class="content primary">
        <div class="head">
            <h2><?= $enroled ?></h2>
        </div>
        <div class="body">
            <span>Student Enrolment Recorded</span>
        </div>
    </div>

    <div class="content <?= $n_enroled ? "red":"green" ?>">
        <div class="head">
            <h2><?= $n_enroled ? count($n_enroled) : 0 ?></h2>
        </div>
        <div class="body">
            <span>Enrolment Issues</span>
        </div>
    </div>

    <div class="content <?= $old_in_new ? "pink":"cyan" ?>">
        <div class="head">
            <h2><?= $old_in_new ? count($old_in_new) : 0 ?></h2>
        </div>
        <div class="body">
            <span>Carried-Over Records</span>
        </div>
    </div>

    <div class="content <?= $boarding_issues ? "orange":"purple" ?>">
        <div class="head">
            <h2><?= $boarding_issues ? count($boarding_issues) : 0 ?></h2>
        </div>
        <div class="body">
            <span>No Boarding Status</span>
        </div>
    </div>
</section>

<section class="btn w-full flex-all-center p-lg gap-md">
    <button class="section-btn plain-r <?= $n_enroled ? "red":"green" ?>" data-section="enrolment">Enrolment Issues</button>
    <button class="section-btn plain-r <?= $old_in_new ? "pink":"cyan" ?>" data-section="carried-over">Carried Over Records</button>
    <button class="section-btn plain-r <?= $old_in_new ? "orange":"purple" ?>" data-section="no-boarding">No Boarding Records</button>
</section>

<section class="section_item enrolment no_disp">
    <?php if(!$n_enroled) : ?>
    <div class="body empty">
        <p>You have no enrolment issue</p>
    </div>
    <?php else: ?>
    <div class="body">
        <div class="btn p-lg w-full txt-al-c">
            <button class="wmax-4xs w-full secondary" id="resolve" data-school-id="<?= $user_school_id ?>">Resolve All Issues</button>
        </div>

        <table>
            <thead>
                <tr>
                    <td>Index Number</td>
                    <td>Lastname</td>
                    <td>Othernames</td>
                    <td>Time recorded</td>
                </tr>
            </thead>
            <tbody>
                <?php foreach($n_enroled as $student): ?>
                <tr>
                    <td><?= $student["indexNumber"] ?></td>
                    <td><?= $student["Lastname"] ?></td>
                    <td><?= $student["Othernames"] ?></td>
                    <td><?= date("d M Y, H:i:sa" ,strtotime($student["enrolDate"])) ?></td>
                </tr>
                <?php endforeach ?>
            </tbody>
            <tfoot></tfoot>
        </table>
    </div>
    <?php endif ?>
</section>

<section class="p-xlg txt-al-c section_item carried-over no_disp"><p>Carried over records are students whose registration numbers are older than the current academic year's format. If you want to add an old placed student to the current admission year, click <a href="javascript:void()" onclick="menu_route('admission-settings')">here</a></p></section>
<section class="section_item carried-over no_disp">
    <div class="body <?= !$old_in_new ? "empty" : "" ?>">
        <?php if($old_in_new): ?>
        <table>
            <thead>
                <tr>
                    <td>Index Number</td>
                    <td>Lastname</td>
                    <td>Othernames</td>
                    <td>Enrolment Date</td>
                </tr>
            </thead>
            <tbody>
                <?php foreach($old_in_new as $student): ?>
                <tr data-index="<?= $student["indexNumber"] ?>">
                    <td><?= $student["indexNumber"] ?></td>
                    <td><?= $student["Lastname"] ?></td>
                    <td><?= $student["Othernames"] ?></td>
                    <td><?= date("d M Y, H:i:sa" ,strtotime($student["enrolDate"])) ?></td>
                    <td>
                        <?php if($student["accept_old"] == 0): ?>
                        <span class="item-event old_new" data-value="1">Approve</span>
                        <span class="item-event old_new" data-value="2">Reject</span>
                        <?php else: ?>
                        <span class="item-event info"><?= $student["accept_old"] == 1 ? "Accepted" : "Rejected" ?></span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach ?>
            </tbody>
            <tfoot></tfoot>
        </table>
        <?php else: ?>
            <p>You have no carried over records</p>
        <?php endif; ?>
    </div>
</section>

<section class="p-xlg txt-al-c section_item no-boarding no_disp"><p>This section shows students who were uploaded without a boarding location (status)</p></section>
<section class="section_item no-boarding no_disp">
    <div class="body <?= !$boarding_issues ? "empty" : "" ?>">
        <?php if($boarding_issues): ?>
        <table>
            <thead>
                <tr>
                    <td>Index Number</td>
                    <td>Lastname</td>
                    <td>Othernames</td>
                    <td>Programme</td>
                    <td>Boarding Status</td>
                </tr>
            </thead>
            <tbody>
                <?php foreach($boarding_issues as $student): ?>
                <tr data-index="<?= $student["indexNumber"] ?>">
                    <td><?= $student["indexNumber"] ?></td>
                    <td><?= $student["Lastname"] ?></td>
                    <td><?= $student["Othernames"] ?></td>
                    <td><?= $student["programme"] ?></td>
                    <td>
                        <div class="form inline-form sp-sm-tp" style="box-shadow: unset !important;">
                            <div class="label flex-column w-full">
                                <select name="change_status" id="change_status" data-student = "<?= $student["indexNumber"] ?>">
                                    <option value="" disabled selected>Set Status</option>
                                    <option value="Day">Day</option>
                                    <option value="Boarder">Boarder</option>
                                </select>
                            </div>
                        </div>
                    </td>
                </tr>
                <?php endforeach ?>
            </tbody>
            <tfoot></tfoot>
        </table>
        <?php else: ?>
            <p>You have no student records without boarding status</p>
        <?php endif; ?>
    </div>
</section>

<script>
    $(document).ready(function(){
        $(".section-btn.plain-r").click(function(){
            const section = $(this).attr("data-section");
            $(".section_item").addClass("no_disp");
            $("." + section).removeClass("no_disp");

            $(this).siblings("button:not(.plain-r)").addClass("plain-r");
            $(this).removeClass("plain-r");
        })

        $("#resolve").click(function(){
            const school_id = $(this).attr("data-school-id");
            const button = $(this);

            $.ajax({
                url: "./admin/submit.php",
                data: {submit: "resolve_issues", school_id: school_id},
                method: "POST",
                beforeSend: function(){
                    button.html("Resolving...");
                },
                success: function(data){
                    button.html("Resolve All Issues");

                    if(data == "success"){
                        alert_box("All issues resolved", "success");
                        location.reload();
                    }else{
                        alert_box(data, "danger", 8);
                    }
                },
                error: function(xhr, textStatus, errorThrown){
                    alert_box(errorThrown);
                    console.log(xhr);
                }
            })
        })

        $(".old_new").click(function(){
            const element = $(this);
            const status = element.attr("data-value");
            const index_number = element.parents("tr").first().attr("data-index");

            if(status == 1 || status == 2){
                ajaxCall({
                    url: "./admin/submit.php",
                    formData: {submit: "old_new_status", status: status, index_number: index_number},
                    method: "POST"
                }).then((response) => {
                    if(response == "success"){
                        const message = status == 1 ? "Approved" : "Rejected";
                        element.parent().html("<span class=\"item-event info\">" + message + "</span>\n");
                    }else{
                        alert_box(response, "danger")
                    }
                }).catch((error) => {
                    alert_box("An error has occured");
                    console.error(error, "danger");                    
                })
            }else{
                alert_box("Unapproved status defined");
            }
        })

        $("select[name=change_status]").change(function(){
            const element = $(this);
            const status = element.val();
            const confirmed = confirm("Are you sure you want to set the status as '" + status + "'?");

            if(confirmed){
                const student = element.attr("data-student");
                ajaxCall({
                    url: "./admin/submit.php",
                    formData: {submit: "change_boarding_status", index_number: student, status: status},
                    beforeSend: function(){
                        element.prop("disabled", true);
                    },
                    method: "POST"
                }).then((response) => {
                    element.prop("disabled", false).prop("selectedIndex", 0);

                    if(response == "success"){
                        const parent = element.parents("td").first();
                        parent.html("Status: " + status);
                    }else{
                        alert_box(response, "danger");
                    }
                }).catch((error) => {
                    element.prop("disabled", false).prop("selectedIndex", 0);
                    console.error(error);                    
                })
            }else{
                element.prop("selectedIndex", 0);
            }
        })
    })
</script>
<?php close_connections() ?>