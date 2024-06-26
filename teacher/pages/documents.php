<?php include_once("compSession.php"); $_SESSION["active-page"] = "documents" ?>
<section class="d-section white lt-shade sp-xlg txt-al-c">
    <p>This is the documents section. Use this section to download a class list for manual recording in pen or upload a result directly</p>
</section>
<section id="main_menu" class="sp-xlg-tp sm-lg-t lt-shade d-section white">
    <p class="txt-al-c">Select the type of document template you want to retrieve</p>
    <div class="btn sm-auto-lr sm-lg-tp flex-all-center p-lg m-sm">
        <button class="plain-r primary section_btn" data-section="stud_list">Class Lists</button>
        <button class="plain-r primary section_btn" data-section="upload">Upload Documents</button>
    </div>
</section>

<section id="stud_list" class="sm-lg-t lt-shade white d-section no_disp section_block">
    <form action="<?= "$url/docs.php" ?>" class="sp-lg m-lg-tp" method="post" name="getDocument">
        <h2 class="txt-al-c">Select Your Filters</h2>
        <p class="txt-al-c">Use the filters below to generate your desired class record sheet</p>
        <?php 
            //get details
            $classes = decimalIndexArray(fetchData1(
                "DISTINCT t.program_id, p.program_name",
                "teacher_classes t JOIN program p ON t.program_id=p.program_id",
                "t.teacher_id={$teacher["teacher_id"]}", 0
            ));

            $subjects = decimalIndexArray(fetchData1(
                "DISTINCT t.program_id, t.course_id, c.course_name",
                "teacher_classes t JOIN courses c ON t.course_id = c.course_id",
                "t.teacher_id={$teacher["teacher_id"]}", 0
            ));
        ?>
        <div class="joint gap-sm">
            <label for="program" class="flex-column gap-sm flex">
                <span class="label_title">Select Class</span>
                <select name="program" id="program" class="w-full">
                    <option value="">Select Class</option>
                    <?php foreach($classes as $class): ?>
                    <option value="<?= $class["program_id"] ?>"><?= $class["program_name"] ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label for="course" class="flex-column gap-sm flex">
                <span class="label_title">Select Subject</span>
                <select name="course" id="course" class="w-full">
                    <option value="" class="first-child">Select Subject</option>
                    <?php foreach($subjects as $subject): ?>
                    <option value="<?= $subject["course_id"] ?>" data-program-id="<?= $subject["program_id"] ?>" class="no_disp"><?= $subject["course_name"] ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label for="class_year" class="flex-column gap-sm flex">
                <span class="label_title">Select Year</span>
                <select name="class_year" id="class_year" class="w-full">
                    <option value="" class="first-child">Select Year</option>
                    <option value="1" class="no_disp">Year 1</option>
                    <option value="2" class="no_disp">Year 2</option>
                    <option value="3" class="no_disp">Year 3</option>
                </select>
            </label>
        </div>
        <div class="btn wmax-sm sm-auto p-lg w-full">
            <button class="primary w-full" type="submit" name="submit" value="get_class_list">Get Class List</button>
        </div>
    </form>
</section>

<section id="upload" class="d-section sm-lg-t lt-shade white section_block no_disp">
    <form action="<?= "$url/docs.php" ?>" method="post" class="m-lg-tp wmax-md sm-auto" name="upload_results_form" enctype="multipart/form-data">
        <h2 class="txt-al-c">Upload your document here</h2>
        <div class="joint gap-sm">
            <label for="semester_u" class="flex flex-column gap-sm">
                <span class="label_title">Select the Semester</span>
                <select name="semester" id="semester_u">
                    <option value="">Select Semester</option>
                    <option value="1">Semester 1</option>
                    <option value="2">Semester 2</option>
                </select>
            </label>
            <label for="program_u" class="flex-column gap-sm flex">
                <span class="label_title">Select Class</span>
                <select name="program" id="program_u" class="w-full">
                    <option value="">Select Class</option>
                    <?php foreach($classes as $class): ?>
                    <option value="<?= $class["program_id"] ?>"><?= $class["program_name"] ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label for="course_u" class="flex-column gap-sm flex">
                <span class="label_title">Select Subject</span>
                <select name="course" id="course_u" class="w-full">
                    <option value="" class="first-child">Select Subject</option>
                    <?php foreach($subjects as $subject): ?>
                    <option value="<?= $subject["course_id"] ?>" data-program-id="<?= $subject["program_id"] ?>" class="no_disp"><?= $subject["course_name"] ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label for="class_year_u" class="flex-column gap-sm flex">
                <span class="label_title">Select Year</span>
                <select name="class_year" id="class_year_u" class="w-full">
                    <option value="" class="first-child">Select Year</option>
                    <option value="1" class="no_disp">Year 1</option>
                    <option value="2" class="no_disp">Year 2</option>
                    <option value="3" class="no_disp">Year 3</option>
                </select>
            </label>
            <label for="academic_year" class="flex flex-column gap-sm">
                <span class="label_title">Academic Year</span>
                <input type="text" readonly name="academic_year" value="<?= getAcademicYear(now(), false) ?>">
            </label>
        </div>
        <label for="document_file" class="relative gap-md flex flex-column">
            <span class="label_title self-align-center">Provide the document to upload</span>
            <div class="fore_file_display flex-all-center flex-column lda-border sp-lg-tp light">
                <input type="file" name="document_file" id="document_file" class="no_disp" accept=".xlsx">
                <span class="plus txt-fl3">+</span>
                <span class="display_file_name">Choose or drag your file here</span>
            </div>
        </label>

        <div class="btn w-full wmax-sm w-fluid-child sm-auto">
            <button class="primary sp-lg" type="submit" name="submit" value="upload_result">Upload Results</button>
        </div>
    </form>
</section>

<script src="<?= "$url/assets/scripts/docs.js?v=".time() ?>"></script>