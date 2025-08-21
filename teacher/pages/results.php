<?php 
    include_once("compSession.php"); 
    $_SESSION["active-page"] = "results";

    //useful variables in this page
    $result_type = fetchData("school_result","admissiondetails","schoolID=".$teacher["school_id"])["school_result"] ?? null;
    $can_enter_results = checkResultsEntry($teacher["school_id"]);
?>

<?php if(!$can_enter_results) : ?>
<section class="d-section lt-shade white">
    <p class="txt-al-c txt-fl sp-xlg">Results entry session is currently closed</p>
</section>

<?php else: ?>
<input type="hidden" name="result_type" id="result_type" value="<?= $result_type ?>">
<section class="d-section lt-shade white">
    <div class="head txt-al-c sm-med-b m-sm-b">
        <h2>Draw out Class List</h2>
        <p>Please select a class list to draw out</p>
    </div>
    <form action="./submit.php" class="flex w-full wmax-md sm-auto flex-column gap-sm">
        <div class="joint gap-sm">
            <label for="class">
                <select class="w-full sp-xlg" name="class" id="class" data-selected-class="null">
                    <option value="">Select Class</option>
                    <?php 
                        $sql = "SELECT DISTINCT t.program_id, p.program_name FROM teacher_classes t JOIN program p ON t.program_id=p.program_id 
                            WHERE t.teacher_id={$teacher['teacher_id']}";
                        $query = $connect2->query($sql);
                        while($class=$query->fetch_assoc()) :
                    ?>
                    <option value="<?= $class["program_id"] ?>"><?= $class["program_name"] ?></option>
                    <?php endwhile; ?>
                </select>
            </label>
            <label for="year">
                <select class="w-full sp-xlg" name="year" id="year" data-selected-year="null">
                    <option value="">Select year</option>
                    <option value="1">Year 1</option>
                    <option value="2">Year 2</option>
                    <option value="3">Year 3</option>
                </select>
            </label>
        </div>
        <div class="btn wmax-xs w-full sm-auto">
            <button class="primary sm-rnd sp-xlg w-full" name="submit" value="search_class">Draw Out</button>
        </div>
    </form>
</section>

<section class="lt-shade white d-section no_disp sm-xlg-tp" id="sub_term_form">
    <div class="form txt-al-c wmax-md sm-auto">
        <?php $show_select = date("m") <= 11 && date("m") >= 9; ?>
        <p>Please provide the subject, academic year and current semester to proceed</p>
        <div class="flex gap-sm sp-med-tp">
            <label for="subject">
                <select class="w-full sp-xlg" name="subject" id="subject">
                    <option value="">Select Subject</option>
                    <?php 
                        $results = decimalIndexArray(fetchData1(
                            "DISTINCT t.course_id, t.program_id, c.course_name",
                            "teacher_classes t JOIN courses c ON t.course_id=c.course_id",
                            "t.teacher_id={$teacher['teacher_id']}", 0));
                        if($results){
                            foreach($results as $result){
                                echo "
                                    <option value=\"{$result['course_id']}\" data-pid=\"{$result['program_id']}\">{$result['course_name']}</option>
                                ";
                            }
                        }else{
                            echo "<option value=\"\">No subjects assigned</option>";
                        }
                    ?>
                </select>
            </label>
            <label for="semester">
                <select name="semester" class="w-full sp-xlg" id="semester">
                    <option value="">Select the semester</option>
                    <option value="1">Semester 1</option>
                    <option value="2">Semester 2</option>
                </select>
            </label>
            <label for="academic_year">
                <?php 
                    // $academic_year = $show_select ? getAcademicYear(date("d-m-Y", strtotime("-1 year")), false) : getAcademicYear(date("d-m-Y"), false);
                    $last_academic_year = getAcademicYear(date("d-m-Y", strtotime("-1 year")));
                    $current_academic_year = getAcademicYear(now(), false);
                ?>
                <!-- <input type="text" class="sp-xlg" name="academic_year" id="academic_year" value="" readonly /> -->
                <select name="academic_year" id="academic_year" class="sp-xlg">
                    <option value="<?= $last_academic_year ?>"><?= $last_academic_year ?></option>
                    <option value="<?= $current_academic_year ?>" <?= !$show_select ? "selected" : "" ?>><?= $current_academic_year ?></option>
                </select>
            </label>
        </div>
    </div>
</section>

<section class="lt-shade white d-section sm-xlg-t m-xlg-tp" id="table_section">
    <div class="head">
        <h2>Result Slip For <span id="head_class_name"></span></h2>
    </div>
    <div class="form sm-xlg-b no_disp" id="search_form">
        <label for="search" class="flex flex-column">
            <span class="label_title">Search for a specific student</span>
            <input type="search" name="search" id="search" placeholder="Search..." autocomplete="off"
                class="w-full sp-lg">
        </label>
    </div>

    <table id="result_slip" class="full light">
        <thead>
            <td></td>
            <td>Index Number</td>
            <td>Full Name</td>
            <td>Class Mark (30)</td>
            <td>Exam Score (70)</td>
            <td>Total Score</td>
            <td>Grade</td>
            <td>Position</td>
        </thead>
        <tbody>
            <tr class="p-lg empty">
                <td colspan="8">Draw out a class list to start entering class details</td>
            </tr>
        </tbody>
        <tfoot class="no_disp">
            <tr>
                <td colspan="6" class="btn p-lg">
                    <button class="green" id="submit_result">Submit Results</button>
                    <button class="teal" id="save_result">Save Results</button>
                    <button class="reset_table red">Reset Table</button>
                </td>
            </tr>
        </tfoot>
    </table>
    <p>Status: <span id="table_status"></span></p>
    <span class="item-event cursor-p no_disp" id="fail-reason">See Fail Reasons</span>
</section>

<?php require_once "reason_block.php" ?>

<script src="<?= "$url/assets/scripts/results.js?v=".time() ?>"></script>
<?php endif; close_connections() ?>