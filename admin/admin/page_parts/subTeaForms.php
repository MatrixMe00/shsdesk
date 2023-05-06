<?php 
    $form_type = $_GET["form_type"];
    $user_school_id = $_GET["school_id"];

    include_once("../../../includes/session.php");
    $courses = fetchData1("*","courses","school_id=$user_school_id", 0);
    $teachers = fetchData1("*","teachers","school_id=$user_school_id", 0);
    $classes = fetchData1("*","program","school_id=$user_school_id", 0);
    
    switch($form_type) :
        case "course_add":
?>
    <form action="<?= $url ?>/admin/admin/submit.php" name="addCourseForm" method="GET">
        <div class="head">
            <h2>Add a New Course</h2>
        </div>
        <div class="message_box no_disp sticky top">
            <span class="message"></span>
            <div class="close"><span>&cross;</span></div>
        </div>
        <div class="body">
            <div class="joint gap-sm">
                <label for="course_name" class="flex-column gap-sm">
                    <span class="label_title">Course Name</span>
                    <input type="text" name="course_name" id="course_name" placeholder="Course Name">
                </label>
                <label for="course_alias" class="flex-column gap-sm">
                    <span class="label_title">Course Alias [Short Form]</span>
                    <input type="text" name="course_alias" id="course_alias" placeholder="Course Alias">
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
                    <span class="label_title">Course Name</span>
                    <input type="text" name="course_name" id="u_course_name" placeholder="Course Name">
                </label>
                <label for="u_course_alias" class="flex-column gap-sm">
                    <span class="label_title">Course Alias [Short Form]</span>
                    <input type="text" name="course_alias" id="u_course_alias" placeholder="Course Alias">
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

            <label for="">
                <p>Select the courses taught by this teacher</p>
            </label>
            
            <div class="joint" id="courseIDs">
                <?php for($counter=0; $counter < count($courses); $counter++) : $course = $courses[$counter]; ?>
                <label for="course_id<?= $counter ?>" class="checkbox">
                    <input type="checkbox" name="course_id" id="course_id<?= $counter ?>" value="<?= $course['course_id'] ?>">
                    <span class="label_title"><?= empty($course["short_form"]) || is_null($course["short_form"]) ? $course["course_name"] : $course["short_form"] ?></span>
                </label>
                <?php endfor; ?>
            </div>

            <label for="">
                <p>Select the classes taught by this teacher</p>
            </label>
            
            <div class="joint" id="classIDs">
                <?php for($counter=0; $counter < count($classes); $counter++) : $class = $classes[$counter]; ?>
                <label for="class_id<?= $counter ?>" class="checkbox" data-course-id="<?= $class["course_ids"] ?>">
                    <input type="checkbox" name="class_id" id="class_id<?= $counter ?>" value="<?= $class['program_id'] ?>">
                    <span class="label_title"><?= is_null($class["short_form"]) || empty($class["short_form"]) ? $class["program_name"] : $class["short_form"] ?></span>
                </label>
                <?php endfor; ?>
            </div>

            <!-- hidden controls -->
            <input type="hidden" name="school_id" value="<?= $user_school_id ?>">
            <input type="hidden" name="course_ids" value="">
            <input type="hidden" name="class_ids" value="">
            
            <div class="btn w-full">
                <button class="primary sp-lg w-full" name="submit" value="addNewTeacher" type="submit">Add Teacher</button>
            </div>
        </div>
    </form>
<?php break; case "teacher_update" : ?>
    <form action="<?= $url ?>/admin/admin/submit.php" name="updateTeacherForm" class="wmax-md" method="GET">
        <div class="head">
            <h2>Update Teacher <span id="teacherID"></span></h2>
        </div>
        <div class="message_box no_disp sticky top">
            <span class="message"></span>
            <div class="close"><span>&cross;</span></div>
        </div>
        <div class="body">
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

            <label for="">
                <p>Select the courses taught by this teacher</p>
            </label>
            
            <div class="joint" id="courseIDs">
                <?php for($counter=0; $counter < count($courses); $counter++) : $course = $courses[$counter]; ?>
                <label for="u_course_id<?= $counter ?>" class="checkbox">
                    <input type="checkbox" name="course_id" id="u_course_id<?= $counter ?>" value="<?= $course['course_id'] ?>">
                    <span class="label_title"><?= empty($course["short_form"]) || is_null($course["short_form"]) ? $course["course_name"] : $course["short_form"] ?></span>
                </label>
                <?php endfor; ?>
            </div>

            <label for="">
                <p>Select the classes taught by this teacher</p>
            </label>
            
            <div class="joint" id="classIDs">
                <?php for($counter=0; $counter < count($classes); $counter++) : $class = $classes[$counter]; ?>
                <label for="u_class_id<?= $counter ?>" class="checkbox" data-course-id="<?= $class["course_ids"] ?>">
                    <input type="checkbox" name="class_id" id="u_class_id<?= $counter ?>" value="<?= $class['program_id'] ?>">
                    <span class="label_title"><?= empty($class["short_form"]) || is_null($class["short_form"]) ? $class["program_name"] : $class["short_form"] ?></span>
                </label>
                <?php endfor; ?>
            </div>

            <!-- hidden controls -->
            <input type="hidden" name="teacher_id" value="">
            <input type="hidden" name="course_ids" value="">
            <input type="hidden" name="class_ids" value="">
            
            <div class="flex flex-wrap w-full gap-sm flex-eq wmax-xs sm-auto">
                <label for="submit" class="btn w-full sm-unset sp-unset">
                    <button type="submit" name="submit" class="primary w-fluid sp-med xs-rnd" value="updateTeacher">Update</button>
                </label>
                <label for="cancel" class="btn w-full sm-unset sp-unset">
                    <button type="reset" name="cancel" class="red w-fluid sp-med xs-rnd" onclick="$('#updateItem').addClass('no_disp')">Cancel</button>
                </label>
            </div>
        </div>
    </form>
<?php break; case "addProgram" : ?>
    <form action="<?= $url ?>/admin/admin/submit.php" name="addProgramForm" class="wmax-md w-full sm-auto" method="GET">
        <div class="head">
            <h2>Add a new program</h2>
        </div>
        <div class="message_box no_disp">
            <span class="message"></span>
            <div class="close"><span>&cross;</span></div>
        </div>
        <div class="body flex flex-column gap-md">
            <div class="joint">
                <label for="program_name" class="flex-column gap-sm">
                    <span class="label_title">Name of Class</span>
                    <input type="text" name="program_name" id="program_name" placeholder="Name of Class">
                </label>
                <label for="short_form" class="flex-column gap-sm">
                    <span class="label_title">Short Name of Class</span>
                    <input type="text" name="short_form" id="short_form" placeholder="Alias name of Class">
                </label>
            </div>

            <label for="">
                <p>Select the courses taught in this program</p>
            </label>

            <?php $courses = fetchData1("*","courses","school_id=$user_school_id", 0); 
                if(is_array($courses)) : ?>
            <div class="joint" id="courseIDs">
                <?php for($counter=0; $counter < count($courses); $counter++) : $course = $courses[$counter]; ?>
                <label for="course_id<?= $counter ?>" class="checkbox">
                    <input type="checkbox" name="course_id" id="course_id<?= $counter ?>" value="<?= $course['course_id'] ?>">
                    <span class="label_title"><?= empty($course["short_form"]) || is_null($course["short_form"]) ? $course["course_name"] : $course["short_form"] ?></span>
                </label>
                <?php endfor; ?>
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
            <div class="joint">
                <label for="u_program_name" class="flex-column gap-sm">
                    <span class="label_title">Name of Class</span>
                    <input type="text" name="program_name" id="u_program_name" placeholder="Name of Class">
                </label>
                <label for="u_short_form" class="flex-column gap-sm">
                    <span class="label_title">Short Name of Class</span>
                    <input type="text" name="short_form" id="u_short_form" placeholder="Alias name of Class">
                </label>
            </div>

            <label for="">
                <p>Select the courses taught in this program</p>
            </label>

            <?php $courses = fetchData1("*","courses","school_id=$user_school_id", 0); 
                if(is_array($courses)) : ?>
            <div class="joint" id="courseIDs">
                <?php for($counter=0; $counter < count($courses); $counter++) : $course = $courses[$counter]; ?>
                <label for="u_course_id<?= $counter ?>" class="checkbox">
                    <input type="checkbox" name="course_id" id="u_course_id<?= $counter ?>" value="<?= $course['course_id'] ?>">
                    <span class="label_title"><?= empty($course["short_form"]) || is_null($course["short_form"]) ? $course["course_name"] : $course["short_form"] ?></span>
                </label>
                <?php endfor; ?>
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
        </div>
    </form>
<?php endswitch; ?>