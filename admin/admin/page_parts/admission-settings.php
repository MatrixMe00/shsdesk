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
    <section class="btn_section txt-al-c p-xlg-lr p-lg-tp">
        <p>Use this section to make settings to for the next admission phase</p>
    </section>
</div>

<div class="main_section no_disp" id="student">
    <section class="btn_section txt-al-c p-xlg-lr p-lg-tp">
        <p>Use this section to search for the details of a specific student</p>
    </section>
</div>

<script>
    $(document).ready(function(){
        $(".main_btn").click(function(){
            $(".main_section").addClass("no_disp");
            $(".main_section#" + $(this).attr("data-section")).removeClass("no_disp");

            $(this).siblings("button:not(.plain-r)").addClass("plain-r");
            $(this).removeClass("plain-r");
        })

        $("form").submit(function(e){
            e.preventDefault();
            const button = $(this).find("button[name=submit]");

            // const response = formSubmit($(this), button, false);
            const form_data = new FormData($(this)[0], button[0]);
            if($(this).attr("name") == "enrolment-data"){
                location.href = "./admin/excelFile.php?" + jsonToURL(FormDataToJSON(form_data));
            }
        })
    })
</script>

<?php else: ?>
    <section class="txt-al-c p-xlg-lr p-xxlg-tp stud_list">
        <p>You current have no student data uploaded from the CSSPS</p>
    </section>
<?php endif; ?>