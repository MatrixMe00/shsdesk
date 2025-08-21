<?php 
    include_once("compSession.php"); 
    $_SESSION["active-page"] = "res_sum";

    $result_type = fetchData("school_result","admissiondetails","schoolID=".$teacher["school_id"])["school_result"] ?? null;
?>

<input type="hidden" name="result_type" id="result_type" value="<?= $result_type ?>">
<section id="cards-section" class="flex d-section flex-wrap gap-sm p-lg card-section">
    <div class="card v-card gap-lg green p-med sm-rnd flex-wrap">
        <span class="">Records Approved</span>
        <span class="txt-fl3 txt-bold">
            <?= fetchData1("COUNT(result_token) as total","recordapproval","teacher_id={$teacher['teacher_id']} AND result_status='accepted'")["total"]; ?>
        </span>
    </div>
    <div class="card v-card gap-lg orange p-med sm-rnd flex-wrap">
        <span class="">Pending Results</span>
        <span class="txt-fl3 txt-bold"><?= fetchData1("COUNT(result_token) as total","recordapproval","teacher_id={$teacher['teacher_id']} AND result_status='pending'")["total"]; ?></span>
    </div>
    <div class="card v-card gap-lg pink p-med sm-rnd flex-wrap">
        <span class="">Rejected Results</span>
        <span class="txt-fl3 txt-bold"><?= fetchData1("COUNT(result_token) as total","recordapproval","teacher_id={$teacher['teacher_id']} AND result_status='rejected'")["total"]; ?></span>
    </div>
    <div class="card v-card gap-lg yellow color-dark p-med sm-rnd flex-wrap">
        <span class="">Results Saved for later</span>
        <span class="txt-fl3 txt-bold"><?= fetchData1("COUNT(DISTINCT(token)) as total","saved_results","teacher_id={$teacher['teacher_id']}")["total"]; ?></span>
    </div>
</section>

<section id="content_wrapper">
    <section class="border b-primary primary sm-med-tp sp-xlg-tp sp-lg-lr">
        <h3 class="txt-al-c sm-med-b">Controls</h3>
        <div class="btn flex flex-eq flex-wrap gap-sm wmax-sm sm-auto p-med">
            <button class="secondary section_btn" data-section-id="approve">Approved Records</button>
            <button class="secondary section_btn" data-section-id="pending">Pending Records</button>
            <button class="secondary section_btn" data-section-id="reject">Rejected Records</button>
            <button class="secondary section_btn" data-section-id="saved">Saved Records</button>
        </div>
    </section>
    <?php 
        $approved_results = decimalIndexArray(fetchData1(
            "r.result_token, r.program_id, r.exam_year, p.program_name, c.course_name, r.submission_date",
            "recordapproval r JOIN program p ON r.program_id = p.program_id JOIN courses c ON r.course_id = c.course_id",
            "r.teacher_id={$teacher['teacher_id']} AND r.result_status='accepted'", 0
        ));
        $pending_results = decimalIndexArray(fetchData1(
            "r.result_token, r.program_id, r.exam_year, p.program_name, c.course_name, r.submission_date",
            "recordapproval r JOIN program p ON r.program_id = p.program_id JOIN courses c ON r.course_id = c.course_id",
            "r.teacher_id={$teacher['teacher_id']} AND r.result_status='pending'", 0
        ));
        $rejected_results = decimalIndexArray(fetchData1(
            "r.result_token, r.program_id, r.exam_year, p.program_name, c.course_name, r.submission_date",
            "recordapproval r JOIN program p ON r.program_id = p.program_id JOIN courses c ON r.course_id = c.course_id",
            "r.teacher_id={$teacher['teacher_id']} AND r.result_status='rejected'", 0
        ));
        $saved_results = decimalIndexArray(fetchData1(
            "DISTINCT s.token, p.program_name, s.exam_year, s.program_id, s.exam_year, s.semester, c.course_name, s.from_reject, DATE(s.save_date) as submission_date",
            "saved_results s JOIN program p ON p.program_id = s.program_id JOIN courses c ON c.course_id = s.course_id",
            "s.teacher_id={$teacher['teacher_id']}",0
        ));
    ?>

    <section class="white p-section lt-shade btn_section" id="approve">
        <div class="head flex flex-column gap-md">
            <h3 class="txt-al-c">Approved Records</h3>
            <?php 
                if(is_array($approved_results) && count($approved_results) > 0) :
            ?><label for="approve_search" class="flex sp-med-tp sp-xlg-lr flex-column gap-sm white">
                <span class="label_title">Search</span>
                <input type="search" id="approve_search" name="search" data-search-type="card" class="sp-med b-secondary border" placeholder="Enter your search here...">
            </label><?php endif; ?>
        </div>
        <?php if(is_array($approved_results) && count($approved_results) > 0) : ?>
        <div class="body flex flex-center-content flex-wrap gap-md sm-lg-t">
        <?php foreach($approved_results as $result) : ?>
            <div class="card light v-card sm-rnd m-med-tp sp-lg">
                <div class="self-align-start flex gap-sm flex-column">
                    <h2><?= $result["program_name"] ?></h2>
                    <h4><?= $result["course_name"] ?></h4>
                    <p class="txt-fs"><?= getAcademicYear($result["submission_date"]) ?> | <?= fetchData1("CONCAT('Sem ',semester,' | Year ',exam_year) as year_sem","results","result_token='{$result['result_token']}'")["year_sem"] ?></p>
                </div>
                <div class="foot btn self-align-end">
                    <button class="light plain-r sp-lg class_single"
                        data-table-id="class_list_table" data-type="approved" 
                        data-index="<?= $result['program_id'] ?>" data-program-name="<?= $result['program_name'] ?>" 
                        data-token="<?= $result['result_token'] ?>" data-student-year="<?= $result["exam_year"] ?>">
                        View Data
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
            <div class="no-result p-lg-lr p-xlg-tp white no_disp txt-al-c border self-align-center w-full">
                <p>No results returned from search</p>
            </div>
        </div>
        <?php else : ?>
        <div class="no-result p-lg-lr p-xlg-tp sm-xlg-t white txt-al-c border self-align-center w-full">
            <p>You have no results approved yet</p>
        </div>
        <?php endif; ?>
    </section>

    <section class="white p-section lt-shade btn_section" id="pending">
        <div class="head flex flex-column gap-md">
            <h3 class="txt-al-c">Pending Records</h3>
            <?php 
                if(is_array($pending_results) && count($pending_results) > 0) :
            ?><label for="pending_search" class="flex sp-med-tp sp-xlg-lr flex-column gap-sm white">
                <span class="label_title">Search</span>
                <input type="search" id="pending_search" name="search" data-search-type="card" class="sp-med b-secondary border" placeholder="Enter your search here...">
            </label><?php endif; ?>
        </div>
        <?php if(is_array($pending_results) && count($pending_results) > 0) : ?>
        <div class="body flex flex-center-content flex-wrap gap-md sm-lg-t">
            <?php foreach($pending_results as $result) : ?>
            <div class="card light v-card sm-rnd m-med-tp sp-lg">
                <div class="self-align-start flex gap-sm flex-column">
                    <h2><?= $result["program_name"] ?></h2>
                    <h4><?= $result["course_name"] ?></h4>
                    <p class="txt-fs"><?= getAcademicYear($result["submission_date"]) ?> | <?php 
                    $sem_year = fetchData1("CONCAT('Sem ',semester,' | Year ',exam_year) as year_sem","results","result_token='{$result['result_token']}'");
                    echo $sem_year == "empty" ?"Invalid" : $sem_year["year_sem"];
                    ?></p>
                </div>
                <div class="foot btn self-align-end">
                    <button class="light plain-r sp-lg class_single" 
                        data-table-id="class_list_table" data-type="pending" 
                        data-index="<?= $result['program_id'] ?>" data-program-name="<?= $result['program_name'] ?>" 
                        data-token="<?= $result['result_token'] ?>" data-student-year="<?= $result["exam_year"] ?>" >
                        View Data
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
            <div class="no-result p-lg-lr p-xlg-tp white no_disp txt-al-c border self-align-center w-full">
                <p>No results returned from search</p>
            </div>
        </div>
        <?php else : ?>
        <div class="no-result p-lg-lr p-xlg-tp sm-xlg-t white txt-al-c border self-align-center w-full">
            <p>You have no pending results</p>
        </div>
        <?php endif; ?>
    </section>

    <section class="white p-section lt-shade btn_section" id="reject">
        <div class="head flex flex-column gap-md">
            <h3 class="txt-al-c">Rejected Records</h3>
            <?php 
                if(is_array($rejected_results) && count($rejected_results) > 0) :
            ?><label for="rejected_search" class="flex sp-med-tp sp-xlg-lr flex-column gap-sm white">
                <span class="label_title">Search</span>
                <input type="search" id="rejected_search" name="search" data-search-type="card" class="sp-med b-secondary border" placeholder="Enter your search here...">
            </label><?php endif; ?>
        </div>
        <?php if(is_array($rejected_results) && count($rejected_results) > 0) : ?>
        <div class="body flex flex-center-content flex-wrap gap-md sm-lg-t">
            <?php foreach($rejected_results as $result) : ?>
            <div class="card light v-card sm-rnd m-med-tp sp-lg">
                <div class="self-align-start flex gap-sm flex-column">
                    <h2><?= $result["program_name"] ?></h2>
                    <h4><?= $result["course_name"] ?></h4>
                    <p class="txt-fs"><?= getAcademicYear($result["submission_date"]) ?> | <?= fetchData1("CONCAT('Sem ',semester,' | Year ',exam_year) as year_sem","results","result_token='{$result['result_token']}'")["year_sem"] ?></p>
                </div>
                <div class="foot btn self-align-end">
                    <button class="primary plain-r sp-lg pass_to_save" data-token="<?= $result["result_token"] ?>">Save For Editing</button>
                    <button class="light plain-r sp-lg class_single"
                        data-table-id="class_list_table" data-type="rejected" 
                        data-index="<?= $result['program_id'] ?>" data-program-name="<?= $result['program_name'] ?>" 
                        data-token="<?= $result['result_token'] ?>" data-student-year="<?= $result["exam_year"] ?>" >
                        View Data
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
            <div class="no-result p-lg-lr p-xlg-tp white no_disp txt-al-c border self-align-center w-full">
                <p>No results returned from search</p>
            </div>
        </div>
        <?php else : ?>
        <div class="no-result p-lg-lr p-xlg-tp sm-xlg-t white txt-al-c border self-align-center w-full">
            <p>You have no results rejected yet</p>
        </div>
        <?php endif; ?>
    </section>

    <section class="white p-section lt-shade btn_section" id="saved">
        <div class="head flex flex-column gap-md">
            <h3 class="txt-al-c">Saved Records</h3>
            <?php 
                if(is_array($saved_results) && count($saved_results) > 0) :
            ?><label for="saved_search" class="flex sp-med-tp sp-xlg-lr flex-column gap-sm white">
                <span class="label_title">Search</span>
                <input type="search" id="saved_search" name="search" data-search-type="card" class="sp-med b-secondary border" placeholder="Enter your search here...">
            </label><?php endif; ?>
        </div>
        <?php if(is_array($saved_results) && count($saved_results) > 0) : ?>
        <div class="body flex flex-center-content flex-wrap gap-md sm-lg-t">
            <?php foreach($saved_results as $result) : ?>
            <div class="card light v-card sm-rnd m-med-tp sp-lg">
                <div class="self-align-start flex gap-sm flex-column">
                    <h2><?= $result["program_name"] ?></h2>
                    <h4><?= $result["course_name"] ?></h4>
                    <p class="txt-fs"><?= getAcademicYear($result["submission_date"]) ?> | <?= "Sem {$result['semester']} | Year {$result['exam_year']}" ?></p>
                </div>
                <div class="foot btn self-align-end">
                    <button class="light plain-r sp-lg class_single"
                        data-table-id="save_data_table" data-type="saved" 
                        data-index="<?= $result['program_id'] ?>" data-program-name="<?= $result['program_name'] ?>" 
                        data-token="<?= $result['token'] ?>" data-student-year="<?= $result["exam_year"] ?>" >
                        View Data
                    </button><?php if($result["from_reject"] == false): ?>
                    <button class="red plain-r sp-lg del_save" data-token="<?= $result["token"] ?>">Delete</button><?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
            <div class="no-result p-lg-lr p-xlg-tp white no_disp txt-al-c border self-align-center w-full">
                <p>No results returned from search</p>
            </div>
        </div>
        <?php else : ?>
        <div class="no-result p-lg-lr p-xlg-tp sm-xlg-t white txt-al-c border self-align-center w-full">
            <p>You have no results saved for later yet</p>
        </div>
        <?php endif; ?>
    </section>
</section>

<section id="class_single" class="fixed flex flex-center-content flex-center-align form_modal_box no_disp">
    <div class="wmax-md sp-lg light w-full window txt-fl">
        <div class="head flex flex-space-content light sp-med">
            <div class="title">
                <span class="txt-bold">Student Data</span>
            </div>
            <div class="controls border sp-sm-lr" onclick="$('#class_single').addClass('no_disp')">
                <div class="mini-o" title="Close" >
                    <span></span>
                </div>
            </div>
        </div>
        <div class="body white sp-lg flex flex-wrap gap-md">
            <div class="flex flex-column sp-med gap-sm">
                <span class="txt-bold">Name</span>
                <span class="name"></span>
            </div>
            <div class="flex flex-column sp-med gap-sm">
                <span class="txt-bold">Index Number</span>
                <span class="index"></span>
            </div>
            <div class="flex flex-column sp-med gap-sm">
                <span class="txt-bold">Class Score</span>
                <span class="class_mark"></span>
            </div>
            <div class="flex flex-column sp-med gap-sm">
                <span class="txt-bold">Exam Score</span>
                <span class="exam_mark"></span>
            </div>
            <div class="flex flex-column sp-med gap-sm">
                <span class="txt-bold">Total Score</span>
                <span class="mark"></span>
            </div>
            <div class="flex flex-column sp-med gap-sm">
                <span class="txt-bold">Subject Position</span>
                <span class="position"></span>
            </div>
            <div class="flex flex-column sp-med gap-sm">
                <span class="txt-bold">Grade</span>
                <span class="grade"></span>
            </div>
            <div class="flex flex-column sp-med gap-sm">
                <span class="txt-bold">Gender</span>
                <span class="gender"></span>
            </div>
        </div>
    </div>
</section>

<section id="single_class" class="d-section lt-shade no_disp">
    <div class="head flex flex-space-content sp-med-lr sp-lg-tp">
        <div class="back class_single" data-table-id="" data-type="" 
            data-index="0" data-student-year="" data-program-name="" data-token="">Back</div>
        <div class="title"><span id="single_class_name"></span> Records</div>
    </div>
    <div class="form-element w-full sm-auto flex-all-center gap-sm flex-wrap">
        <label for="search_table" id="search_table_label" class="w-fluid-child w-fit no_disp flex-column">
            <span class="label_title">Make your search </span>
            <input type="search" name="search" data-search-type="table" id="search_table" placeholder="Search..." autocomplete="off"
                class="w-full sp-lg" data-active-table = "">
        </label>
    </div>
    <div class="body sm-xlg-t">
        <table class="full no_disp" id="class_list_table">
            <thead>
                <td>Index Number</td>
                <td>Student Name</td>
                <td>Gender</td>
                <td>Total Score</td>
                <td>Grade</td>
                <td>Position</td>
            </thead>
            <tbody>
                <tr class="empty">
                    <td colspan="6">No data has been processed yet</td>
                </tr>
            </tbody>
        </table>

        <table class="full light no_disp" id="save_data_table">
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
                    <td colspan="8">No data has been processed yet</td>
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
    </div>
</section>

<section id="confirm_box" class="fixed flex-all-center form_modal_box no_disp">
            <form action="./submit.php" name="confirm_box" method="get"
                class="light sp-xlg-lr sp-xxlg-tp sm-rnd wmax-sm wmin-unset w-full sm-auto">
                <div class="message white p-lg txt-al-c">
                    <p>Do you want to delete this?</p>
                </div>
                
                <!-- contents to send -->
                <input type="hidden" name="token">
                <input type="hidden" name="mode">
                
                <div class="btn p-lg flex-all-center w-full flex-eq sm-xlg-t gap-md">
                    <button class="plain-r green" type="submit" name="submit" value="confirm_box_response">Yes</button>
                    <button class="plain-r red" type="reset" onclick="$('#confirm_box').addClass('no_disp')">No</button>
                </div>
            </form>
</section>

<?php require_once "reason_block.php" ?>

<script src="<?= "$url/assets/scripts/uploaded.js?v=".time() ?>"></script>
<?php close_connections() ?>