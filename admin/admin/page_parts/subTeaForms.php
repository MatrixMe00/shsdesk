<?php 
    include_once("auth.php");
    global $courses, $teachers, $classes;


    if(!isset($courses))
        $courses = decimalIndexArray(fetchData1("*","courses","school_id=$user_school_id", 0));
    
    if(!isset($teachers))
        $teachers = decimalIndexArray(fetchData1("*","teachers","school_id=$user_school_id", 0));
    
    if(!isset($classes))
        $classes = decimalIndexArray(fetchData1("*","program","school_id=$user_school_id", 0));
    
    switch($form_type) :
        case "course_add":
?>
    <form action="<?= $url ?>/admin/admin/submit.php" name="addCourseForm" method="GET">
        <div class="head">
            <h2>Add a New Subject</h2>
        </div>
        <div class="message_box no_disp sticky top">
            <span class="message"></span>
            <div class="close"><span>&cross;</span></div>
        </div>
        <div class="body">
            <div class="joint gap-sm">
                <label for="course_name" class="flex-column gap-sm">
                    <span class="label_title">Subject Name</span>
                    <input type="text" name="course_name" id="course_name" placeholder="Subject Name">
                </label>
                <label for="course_alias" class="flex-column gap-sm">
                    <span class="label_title">Subject Alias [Short Form]</span>
                    <input type="text" name="course_alias" id="course_alias" placeholder="Subject Alias">
                </label>
                <label for="course_credit" class="flex-column gap-sm">
                    <span class="label_title">Course Credit Hours</span>
                    <input type="text" name="course_credit" id="course_credit" placeholder="Course Credit Hours">
                </label>
            </div>

            <!-- hidden controls -->
            <input type="hidden" name="school_id" value="<?= $user_school_id ?>">
            
            <div class="btn w-full">
                <button class="primary sp-lg w-full" name="submit" value="addNewCourse" type="submit">Add Course</button>
            </div>
        </div>
    </form>
<?php break; case "course_update": ?>
    <form action="<?= $url ?>/admin/admin/submit.php" name="updateCourseForm" method="GET">
        <div class="head">
            <h2>Update <span id="courseID"></span></h2>
        </div>
        <div class="message_box no_disp sticky top">
            <span class="message"></span>
            <div class="close"><span>&cross;</span></div>
        </div>
        <div class="body">
            <div class="joint gap-sm">
                <label for="u_course_name" class="flex-column gap-sm">
                    <span class="label_title">Subject Name</span>
                    <input type="text" name="course_name" id="u_course_name" placeholder="Subject Name">
                </label>
                <label for="u_course_alias" class="flex-column gap-sm">
                    <span class="label_title">Subject Alias [Short Form]</span>
                    <input type="text" name="course_alias" id="u_course_alias" placeholder="Subject Alias">
                </label>
                <label for="u_course_credit" class="flex-column gap-sm">
                    <span class="label_title">Course Credit Hours</span>
                    <input type="text" name="course_credit" id="u_course_credit" placeholder="Course Credit Hours">
                </label>
            </div>

            <!-- hidden controls -->
            <input type="hidden" name="course_id" value="">
            
            <div class="flex flex-wrap w-full gap-sm flex-eq wmax-xs sm-auto">
                <label for="submit" class="btn w-full sm-unset sp-unset">
                    <button type="submit" name="submit" class="primary w-fluid sp-med xs-rnd" value="updateCourse">Update</button>
                </label>
                <label for="cancel" class="btn w-full sm-unset sp-unset">
                    <button type="reset" name="cancel" class="red w-fluid sp-med xs-rnd" onclick="$('#updateItem').addClass('no_disp')">Cancel</button>
                </label>
            </div>
        </div>
    </form>
<?php break; case "teacher_add" :  ?>
    <form action="<?= $url ?>/admin/admin/submit.php" name="addTeacherForm" method="GET">
        <div class="head">
            <h2>Add a New Teacher</h2>
        </div>
        <div class="message_box no_disp sticky top">
            <span class="message"></span>
            <div class="close"><span>&cross;</span></div>
        </div>
        <div class="body">
            <?php if($classes): ?>
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
                    <input type="tel" class="light sp-med" name="teacher_phone" id="teacher_phone" 
                        placeholder="Teacher's Mobile Number" maxlength="13" minlength="10">
                </label>
                <label for="teacher_email" class="flex-column gap-sm">
                    <span class="label_title">Email</span>
                    <input type="text" name="teacher_email" id="teacher_email" placeholder="Teacher's Email">
                </label>
            </div>
            
            <div class="joint gap-sm" id="teacher_program">
                <label for="class_id" class="w-full flex-column">
                    <span class="label_title">Select the class taught</span>
                    <select name="class_id" id="class_id" multiple>
                        <option value="" disabled selected>Select a class</option>
                        <?php foreach($classes as $counter => $class) : ?>
                            <option value="<?= $class['program_id'] ?>" data-courses="<?= $class["course_ids"] ?>"><?= is_null($class["short_form"]) || empty($class["short_form"]) ? $class["program_name"] : $class["short_form"] ?></option>
                        <?php endforeach; ?>
                    </select>
                    <span class="w-full txt-al-c item-event info">Hold down the control key to select multiple classes</span>
                </label>
            
                <label for="course_id" class="w-full flex-column">
                    <span class="label_title">Select the subject taught</span>
                    <select name="course_id" id="course_id">
                        <option value="" disabled selected>Select the subject</option>
                        <?php foreach($courses as $course) : ?>
                        <option value="<?= $course['course_id'] ?>">
                            <?= empty($course["short_form"]) || is_null($course["short_form"]) ? $course["course_name"] : $course["short_form"] ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </label>

                <label for="class_year" class="w-full flex-column">
                    <span class="label_title">Select the Year Level</span>
                    <select name="class_year" id="class_year">
                        <option value="">Select the year level</option>
                        <option value="1">Year 1</option>
                        <option value="2">Year 2</option>
                        <option value="3">Year 3</option>
                    </select>
                </label>
            </div>

            <div class="btn">
                <button type="button" class="light add_detail">Add Detail</button>
            </div>

            <!-- hidden controls -->
            <input type="hidden" name="school_id" value="<?= $user_school_id ?>">
            <input type="hidden" name="course_ids" placeholder="Course Ids" value="">
            
            <label for="">
                <table class="full" id="class_sub_table" name="class_sub_table">
                    <thead>
                        <td>Class ID</td>
                        <td>Class Name</td>
                        <td>Subject ID</td>
                        <td>Subject Name</td>
                        <td>Class Level</td>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </label>

            <div class="btn w-full">
                <button class="primary sp-lg w-full" name="submit" value="addNewTeacher" type="submit">Add Teacher</button>
            </div>
            <?php else: ?>
            <p class="txt-al-c sp-xlg">Please make sure you have added at least one class to add a new teacher</p>
            <?php endif; ?>
        </div>
    </form>
<?php break; case "teacher_update" : ?>
    <form action="<?= $url ?>/admin/admin/submit.php" name="updateTeacherForm" class="wmax-md relative" method="GET">
        <div class="head">
            <h2>Update Teacher <span id="teacherID"></span></h2>
        </div>
        <div class="body">
            <?php if($classes) : ?>
            <div class="joint gap-sm">
                <label for="u_teacher_lname" class="flex-column gap-sm">
                    <span class="label_title">Lastname</span>
                    <input type="text" name="teacher_lname" id="u_teacher_lname" placeholder="Teacher's Lastname">
                </label>
                <label for="u_teacher_oname" class="flex-column gap-sm">
                    <span class="label_title">Othername(s)</span>
                    <input type="text" name="teacher_oname" id="u_teacher_oname" placeholder="Teacher's Othername(s)">
                </label>
                <label for="u_teacher_gender" class="flex-column gap-sm">
                    <span class="label_title">Select Teacher's Gender</span>
                    <select name="teacher_gender" id="u_teacher_gender">
                        <option value="">Please select the gender of the user</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                </label>
                <label for="u_teacher_phone" class="flex-column gap-sm">
                    <span class="label_title">Mobile Number</span>
                    <input type="tel" class="light sp-med" name="teacher_phone" id="u_teacher_phone" placeholder="Teacher's Mobile Number">
                </label>
                <label for="u_teacher_email" class="flex-column gap-sm">
                    <span class="label_title">Email</span>
                    <input type="text" name="teacher_email" id="u_teacher_email" placeholder="Teacher's Email">
                </label>
            </div>
            
            <div class="joint gap-sm" id="u_teacher_program">
                <label for="u_class_id" class="w-full flex-column">
                    <span class="label_title">Select the class taught</span>
                    <select name="class_id" id="u_class_id" multiple>
                        <option value="" disabled selected>Select a class</option>
                        <?php foreach($classes as $counter => $class) : ?>
                            <option value="<?= $class['program_id'] ?>" data-courses="<?= $class["course_ids"] ?>"><?= is_null($class["short_form"]) || empty($class["short_form"]) ? $class["program_name"] : $class["short_form"] ?></option>
                        <?php endforeach; ?>
                    </select>
                    <span class="w-full txt-al-c item-event info">Hold down the control key to select multiple classes</span>
                </label>
            
                <label for="u_course_id" class="w-full flex-column">
                    <span class="label_title">Select the subject taught</span>
                    <select name="course_id" id="u_course_id">
                        <option value="" disabled selected>Select the subject</option>
                        <?php foreach($courses as $counter => $course): ?>
                        <option value="<?= $course['course_id'] ?>">
                            <?= empty($course["short_form"]) || is_null($course["short_form"]) ? $course["course_name"] : $course["short_form"] ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </label>

                <label for="u_class_year" class="w-full flex-column">
                    <span class="label_title">Select the Year Level</span>
                    <select name="class_year" id="u_class_year">
                        <option value="">Select the year level</option>
                        <option value="1">Year 1</option>
                        <option value="2">Year 2</option>
                        <option value="3">Year 3</option>
                    </select>
                </label>
            </div>

            <div class="btn">
                <button type="button" class="light add_detail">Add Detail</button>
            </div>

            <!-- hidden controls -->
            <input type="hidden" name="teacher_id" value="">
            <input type="hidden" name="course_ids" value="">
            
            <label for="">
                <table class="full" id="u_class_sub_table" name="class_sub_table">
                    <thead>
                        <td>Class ID</td>
                        <td>Class Name</td>
                        <td>Subject ID</td>
                        <td>Subject Name</td>
                        <td>Class Level</td>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </label>

            <!-- message box -->
            <div class="message_box no_disp sticky top sm-lg-b">
                <span class="message"></span>
                <div class="close"><span>&cross;</span></div>
            </div>

            <div class="flex flex-wrap w-full gap-sm flex-eq wmax-xs sm-auto">
                <label for="submit" class="btn w-full sm-unset sp-unset">
                    <button type="submit" name="submit" class="primary w-fluid sp-med xs-rnd" value="updateTeacher">Update</button>
                </label>
                <label for="cancel" class="btn w-full sm-unset sp-unset">
                    <button type="reset" name="cancel" class="red w-fluid sp-med xs-rnd" onclick="$('#updateItem').addClass('no_disp')">Cancel</button>
                </label>
            </div>
            <?php else: ?>
            <p class="txt-al-c sp-xlg">Please make sure you have added at least one class to update teacher data</p>
            <?php endif; ?>
        </div>
    </form>
<?php break; case "addProgram" : ?>
    <form action="<?= $url ?>/admin/admin/submit.php" name="addProgramForm" class="wmax-md w-full sm-auto" method="GET">
        <div class="head">
            <h2>Add a new Class</h2>
        </div>
        <div class="message_box no_disp">
            <span class="message"></span>
            <div class="close"><span>&cross;</span></div>
        </div>
        <div class="body flex flex-column gap-md">
            <?php if($courses) : ?>
            <div class="joint">
                <label for="program_name" class="flex-column gap-sm">
                    <span class="label_title">Name of Class</span>
                    <input type="text" name="program_name" id="program_name" placeholder="Name of Class">
                </label>
                <label for="short_form" class="flex-column gap-sm">
                    <span class="label_title">Short Name of Class</span>
                    <input type="text" name="short_form" id="short_form" placeholder="Alias name of Class">
                </label>
                <label for="associate_program" class="flex-column gap-sm">
                    <span class="label_title">Associated Program</span>
                    <select name="associate_program" id="associate_program">
                        <option value="">Select the Class' program</option>
                        <?php $programs = decimalIndexArray(fetchData("DISTINCT programme", "cssps", "schoolID=$user_school_id", 0)); if($programs): foreach($programs as $program): ?>
                        <option value="<?= $program["programme"] ?>"><?= $program["programme"] ?></option>
                        <?php endforeach; endif; ?>
                    </select>
                    <span class="item-event info">This helps automatically assign students to a class based on their program. Electives can be chosen during admission once set.</span>
                </label>
            </div>

            <label for="">
                <p>Select the subjects taught in this class</p>
            </label>

            <?php if($courses) : ?>
            <div class="joint" id="courseIDs">
            <?php foreach($courses as $counter => $course) : ?>
                <label for="course_id<?= $counter ?>" class="checkbox" title="<?= $course["course_name"] ?>">
                    <input type="checkbox" name="course_id" id="course_id<?= $counter ?>" value="<?= $course['course_id'] ?>">
                    <span class="label_title"><?= empty($course["short_form"]) || is_null($course["short_form"]) ? $course["course_name"] : $course["short_form"] ?></span>
                </label>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
                <label for="">
                    <p>Oops, no courses were found. Please add one to continue</p>
                </label>
            <?php endif; ?>

            <!-- hidden inputs -->
            <input type="hidden" name="school_id" value="<?= $user_school_id ?>">
            <input type="hidden" name="course_ids" value="">

            <div class="btn sm-unset w-full">
                <button type="submit" class="primary w-full sp-med" name="submit" value="addProgram">Submit</button>
            </div>
            <?php else: ?>
            <p class="txt-al-c sp-xlg">Please make sure you have added at least one subject to add a new class</p>
            <?php endif; ?>
        </div>
    </form>
<?php break; case "updateProgram" : ?>
    <form action="<?= $url ?>/admin/admin/submit.php" name="updateProgramForm" class="wmax-md w-full sm-auto no_disp" method="GET">
        <div class="head">
            <h2>Update Class</h2>
        </div>
        <div class="message_box no_disp">
            <span class="message"></span>
            <div class="close"><span>&cross;</span></div>
        </div>
        <div class="body flex flex-column gap-md">
            <?php if($courses) : ?>
            <div class="joint">
                <label for="u_program_name" class="flex-column gap-sm">
                    <span class="label_title">Name of Class</span>
                    <input type="text" name="program_name" id="u_program_name" placeholder="Name of Class">
                </label>
                <label for="u_short_form" class="flex-column gap-sm">
                    <span class="label_title">Short Name of Class</span>
                    <input type="text" name="short_form" id="u_short_form" placeholder="Alias name of Class">
                </label>
                <label for="u_associate_program" class="flex-column gap-sm">
                    <span class="label_title">Associated Program</span>
                    <select name="associate_program" id="u_associate_program">
                        <option value="">Select the Class' program</option>
                        <?php $programs = decimalIndexArray(fetchData("DISTINCT programme", "cssps", "schoolID=$user_school_id", 0)); if($programs): foreach($programs as $program): ?>
                        <option value="<?= $program["programme"] ?>"><?= $program["programme"] ?></option>
                        <?php endforeach; endif; ?>
                    </select>
                    <span class="item-event info">This helps automatically assign students to a class based on their program. Electives can be chosen during admission once set.</span>
                </label>
            </div>

            <label for="">
                <p>Select the subjects taught in this class</p>
            </label>

            <?php if($courses) : ?>
            <div class="joint" id="courseIDs">
                <?php foreach($courses as $count => $course) : ?>
                <label for="u_course_id<?= $counter ?>" class="checkbox" title="<?= $course["course_name"] ?>">
                    <input type="checkbox" name="course_id" id="u_course_id<?= $counter ?>" value="<?= $course['course_id'] ?>">
                    <span class="label_title"><?= empty($course["short_form"]) || is_null($course["short_form"]) ? $course["course_name"] : $course["short_form"] ?></span>
                </label>
                <?php endforeach; ?>
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
            <?php else: ?>
            <p class="txt-al-c sp-xlg">Please make sure you have added at least one subject to update a class detail</p>
            <?php endif; ?>
        </div>
    </form>
    <?php break; case "updateResult": ?>
        <form action="<?= $url ?>/admin/admin/submit.php" name="updateResultForm" class="wmax-md w-full sm-auto" method="POST">
            <div class="head">
                <h2>Update Result Head</h2>
            </div>
            <div class="message_box no_disp">
                <span class="message"></span>
                <div class="close"><span>&cross;</span></div>
            </div>
            <div class="body flex flex-column gap-md">
                <!-- hidden inputs -->
                <input type="hidden" name="result_id" value="">

                <div class="joint">
                    <label for="r_program_name" class="flex-column gap-sm">
                        <span class="label_title">Name of Class</span>
                        <input type="text" name="program_name" id="r_program_name" readonly>
                    </label>
                    <label for="r_subject" class="flex-column gap-sm">
                        <span class="label_title">Subject</span>
                        <input type="text" name="subject" id="r_subject" readonly>
                    </label>
                    <label for="r_form_level" class="flex-column gap-sm">
                        <span class="label_title">Form level</span>
                        <input type="text" name="form_level" id="r_form_level" readonly>
                    </label>
                    <label for="r_submit_date" class="flex-column gap-sm">
                        <span class="label_title">Semester</span>
                        <input type="text" name="semester" id="r_submit_date" readonly>
                    </label>
                    <label for="r_academic_year" class="flex-column gap-sm">
                        <span class="label_title">Academic Year</span>
                        <?php 
                            $last_academic_year = getAcademicYear(date("d-m-Y", strtotime("-1 year")));
                            $current_academic_year = getAcademicYear(now());
                        ?>
                        <!-- <input type="text" class="sp-xlg" name="academic_year" id="academic_year" value="" readonly /> -->
                        <select name="academic_year" id="r_academic_year">
                            <option value="<?= formatAcademicYear($last_academic_year, false) ?>"><?= $last_academic_year ?></option>
                            <option value="<?= formatAcademicYear($current_academic_year, false) ?>"><?= $current_academic_year ?></option>
                        </select>
                    </label>
                    <label for="r_submit_date" class="flex-column gap-sm">
                        <span class="label_title">Submission Date</span>
                        <input type="text" name="submit_date" id="r_submit_date" readonly>
                    </label>
                    <label for="r_teacher_name" class="flex-column gap-sm">
                        <span class="label_title">Submitted By</span>
                        <input type="text" name="teacher_name" id="r_teacher_name" readonly>
                    </label>
                </div>

                <div class="flex flex-wrap w-full gap-sm flex-eq wmax-xs sm-auto">
                    <label for="submit" class="btn w-full sm-unset sp-unset">
                        <button type="submit" name="submit" class="primary w-fluid sp-med xs-rnd" value="update_result_head">Update</button>
                    </label>
                    <label for="cancel" class="btn w-full sm-unset sp-unset">
                        <button type="reset" name="cancel" class="red w-fluid sp-med xs-rnd" onclick="$('#updateResult').addClass('no_disp')">Cancel</button>
                    </label>
                </div>
            </div>
        </form>
<?php endswitch; ?>