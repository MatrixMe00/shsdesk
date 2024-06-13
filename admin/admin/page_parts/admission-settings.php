<?php 
    require_once "auth.php";

    //set nav_point session
    $_SESSION["nav_point"] = "admission-settings";
?>

<?php 
    $students = decimalIndexArray(fetchData("*", "cssps", "schoolID=$user_school_id", 0));

    if($students):
?>

<section id="main_menu" class="sp-xlg-tp">
    <div class="btn sm-auto p-lg m-sm">
        <button class="primary main_btn" data-section="history">Record History</button>
        <button class="plain-r primary main_btn" data-section="student">Student Detail</button>
        <button class="plain-r primary main_btn" data-section="settings">Admission Settings</button>
    </div>
</section>

<div class="main_section" id="history">
    <section class="txt-al-c p-xlg-lr p-lg-tp">
        <p>Use this section to retrieve document for the list of enroled students</p>
    </section>

    <section class="btn_section">
        <form action="<?= $url ?>/" name="enrolment-data">
            <div class="head">
                <h3>Download Enrolment Data</h3>
            </div>
            <div class="body">
                <?php ?>
                <label for="academic_year" class="flex-wrap flex-column">
                    <span class="label_title">Academic Year</span>
                    <?php 
                        $academic_years = array_unique(array_column($students, "academic_year"));
                    ?>
                    <select name="academic_year" id="academic_year">
                        <?php foreach($academic_years as $year): ?>
                            <option value="<?= $year ?>"><?= $year ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>

                <div class="btn w-full">
                    <button class="primary w-full sp-lg" name="submit" value="get_enrolment_data" data-original-text="Get Enrolment Data">Get Enrolment Data</button>
                </div>
            </div>
        </form>
    </section>
</div>

<div class="main_section no_disp" id="settings">
    <section class="txt-al-c p-xlg-lr p-lg-tp">
        <p>Use this section to make settings to for your school's admission section</p>
    </section>

    <section>
        <?php 
            $new_admission = in_array(1, settingsArrayConvert($students, "current_data"));
            $clean_data = in_array(0, settingsArrayConvert($students, "enroled"));
            $cleanable_data = count(settingsArrayConvert($students, "enroled", false));
            $settings = [
                [
                    "settings" => "New Admission",
                    "info" => "This will prepare your school to take new set of CSSPS data. Use this when its a new admission year",
                    "alert_message" => "Are you sure you want make this update?",
                    "submit_value" => "reset_admission",
                    "action" => [
                        "name" => "Reset Admission",
                        "status" => $new_admission
                    ]
                ],
                [
                    "settings" => "Clean Data",
                    "info" => "Use this when an admission period has ended and you want to tally the number of received students with those enroled. Thus if you registered 1000 students but only 500 enroled at the end of the admission process, the 500 which did not enrol via the system will be removed so that it can tally 500 to 500.",
                    "alert_message" => "This will clean a maximum of $cleanable_data results from your list of records",
                    "submit_value" => "clean_data",
                    "action" => [
                        "name" => "Clean Data",
                        "status" => !$new_admission && $clean_data
                    ]
                ],
                /*[
                    "settings" => "",
                    "info" => "",
                    "alert_message" => "",
                    "submit_value" => "",
                    "action" => [
                        "name" => "",
                        "status" => ""
                    ]
                ],*/
                
            ];
        ?>
        <table class="full">
            <thead>
                <tr>
                    <td>Settings</td>
                    <td>Info</td>
                    <td></td>
                </tr>
            </thead>

            <tbody>
                <?php foreach($settings as $setting): ?>
                    <tr>
                        <td><?= $setting["settings"] ?></td>
                        <td><?= $setting["info"] ?></td>
                        <td>
                            <?php if($setting["action"]["status"]): ?>
                            <span class="item-event action" data-submit="<?= $setting["submit_value"] ?>" data-alert-message="<?= $setting["alert_message"] ?>"><?= $setting["action"]["name"] ?></span>
                            <?php else: ?>
                                <span class="item-event info">No Action</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                
            </tbody>
        </table>
    </section>
</div>

<div class="main_section no_disp" id="student">
    <section class="txt-al-c p-xlg-lr p-lg-tp">
        <p>Use this section to search for the details of a specific student</p>
    </section>

    <?php require_once "$rootPath/admin/student-search.php" ?>
</div>

<script>
    $(document).ready(function(){
        $(".main_btn").click(function(){
            $(".main_section").addClass("no_disp");
            $(".main_section#" + $(this).attr("data-section")).removeClass("no_disp");

            $(this).siblings("button:not(.plain-r)").addClass("plain-r");
            $(this).removeClass("plain-r");
        })

        $("form:not(.student_search)").submit(function(e){
            e.preventDefault();
            const button = $(this).find("button[name=submit]");
            const form_data = new FormData($(this)[0], button[0]);

            if($(this).attr("name") == "enrolment-data"){
                location.href = "./admin/excelFile.php?" + jsonToURL(FormDataToJSON(form_data));
            }
        })

        $(".action").click(function(){
            const submit_value = $(this).attr("data-submit");
            const alert_message = $(this).attr("data-alert-message");
            console.log(submit_value, alert_message);

            const confirmed = confirm(alert_message);

            if(confirmed){
                $.ajax({
                    url: "./admin/submit.php",
                    data: {submit: submit_value},
                    method: "GET",
                    success: function(response){
                        response = JSON.parse(response);

                        if(response.status == true){
                            alert_box(response.message, "success");
                            $("#lhs .item.active").click();
                        }else{
                            alert_box(response.message, "danger");
                        }
                    },
                    error: function(xhr, textStatus, errorThrown){
                        alert(errorThrown);
                        console.log(xhr);
                    }
                })
            }else{
                alert_box("Operation canceled by user", "secondary");
            }
        })
    })
</script>
<script src="<?= "$url/admin/assets/scripts/student-search.js?v=".time() ?>"></script>

<?php else: ?>
    <section class="txt-al-c p-xlg-lr p-xxlg-tp stud_list">
        <p>You current have no student data uploaded from the CSSPS</p>
    </section>
<?php endif; ?>