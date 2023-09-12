<?php include_once("auth.php") ?>

<?php if(isset($_GET["db2"])): $user_school_id=$_GET["sid"] ?>
<form action="<?php echo $url?>/admin/admin/submit.php" method="get" class="fixed" name="adminAddStudent">
    <div class="head">
        <h2>Add A Student</h2>
    </div>
    <div class="body">
        <div class="message_box success no_disp">
            <span class="message"></span>
            <div class="close"><span>&cross;</span></div>
        </div>
        <div class="joint">
            <input type="hidden" name="school_id" value="<?php echo $user_school_id?>">
            <label for="student_index">
                <span class="label_image">
                    <img src="<?php echo $url?>/assets/images/icons/index.png" alt="index">
                </span>
                <input type="text" name="student_index" id="student_index"
                autocomplete="off" placeholder="Index Number" title="Leave empty to auto-generate an index number">
            </label>
            <label for="lname">
                <span class="label_image">
                    <img src="<?php echo $url?>/assets/images/icons/person-outline.svg" alt="lname">
                </span>
                <input type="text" name="lname" id="lname" required autocomplete="off" placeholder="Lastname*"
                title="Enter the lastname of the student here">
            </label>
            <label for="oname">
                <span class="label_image">
                    <img src="<?php echo $url?>/assets/images/icons/person-outline.svg" alt="othernames">
                </span>
                <input type="text" name="oname" id="oname" required
                autocomplete="off" placeholder="Other Name(s)*" title="Enter other names of the student">
            </label>
            <label for="gender">
                <span class="label_image">
                    <img src="<?php echo $url?>/assets/images/icons/male-female-outline.svg" alt="gender">
                </span>
                <select name="gender" id="gender" title="Select the gender" required>
                    <option value="">Select the gender</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>
            </label>
        </div>
        <div class="joint">
            <label for="boarding_status">
                <span class="label_image">
                    <img src="<?php echo $url?>/assets/images/icons/bed-outline.svg" alt="boarding_status">
                </span>
                <select name="boarding_status" id="boarding_status" required>
                    <option value="">Select Boarding Status</option>
                    <option value="Day">Day</option>
                    <option value="Boarder">Boarding</option>
                </select>
            </label>
            <label for="house">
                <span class="label_image">
                    <img src="<?php echo $url?>/assets/images/icons/home.png" alt="student house">
                </span>
                <select name="house" id="house" required>
                    <option value="">Select House</option>
                    <?php 
                        $houses = fetchData("id, title, gender","houses","schoolID=$user_school_id",0);
                        if(is_array($houses)):
                            if(array_key_exists("id", $houses)) :
                                if($houses["gender"] == "Male" || $houses["gender"] == "Both") :
                    ?>
                    <option value="<?= $houses["id"] ?>" data-gender="male"><?= $houses["title"]." - Male" ?></option>
                                <?php  endif; if($houses["gender"] == "Female" || $houses["gender"] == "Both") : ?>
                    <option value="<?= $houses["id"] ?>" data-gender="female"><?= $houses["title"]." - Female" ?></option>
                    <?php       endif;
                            elseif(array_key_exists(0,$houses)):
                                foreach($houses as $house):
                                    if($house["gender"] == "Male" || $house["gender"] == "Both") :
                    ?>
                    <option value="<?= $house["id"] ?>" data-gender="male"><?= $house["title"]." - Male" ?></option>
                                <?php  endif; if($house["gender"] == "Female" || $house["gender"] == "Both") : ?>
                    <option value="<?= $house["id"] ?>" data-gender="female"><?= $house["title"]." - Female" ?></option>
                    <?php
                                    endif;
                                endforeach;
                            endif;
                        endif; 
                    ?>
                </select>
            </label>
            <label for="student_course">
                <span class="label_image">
                    <img src="<?php echo $url?>/assets/images/icons/book-outline.svg" alt="course">
                </span>
                <select name="student_course" required id="student_course">
                    <option value="">Select the programme name</option>
                    <?php 
                        $programs = fetchData("DISTINCT programme", "cssps","schoolID=$user_school_id",0);
                        if(is_array($programs) && array_key_exists(0,$programs)) :
                            foreach($programs as $program) :
                    ?>
                    <option value="<?= $program["programme"] ?>"><?= $program["programme"] ?></option>
                    <?php endforeach; elseif(is_array($programs) && array_key_exists("programme", $programs)) : ?>
                    <option value="<?= $programs["programme"] ?>"><?= $programs["programme"] ?></option>
                    <?php endif; ?>
                </select>
            </label>
            <label for="program_id">
                <span class="label_image">
                    <img src="<?php echo $url?>/assets/images/icons/hashtag.png" alt="program">
                </span>
                <select name="program_id" id="program_id" required>
                    <option value="">Select the class of this student</option>
                    <?php
                        $classes = fetchData1("program_id, program_name","program","school_id=$user_school_id",0);
                        if(is_array($classes) && array_key_exists(0, $classes)):
                            foreach($classes as $class) :
                    ?>
                    <option value="<?= $class["program_id"] ?>"><?= $class["program_name"] ?></option>
                    <?php endforeach; elseif(is_array($classes) && array_key_exists("program_id", $classes)) : ?>
                    <option value="<?= $classes["program_id"] ?>"><?= $classes["program_name"] ?></option>
                    <?php endif; ?>
                </select>
            </label>
            <label for="student_year">
                <span class="label_image">
                    <img src="<?php echo $url?>/assets/images/icons/city hall.png" alt="jhs">
                </span>
                <select name="student_year" id="student_year" required>
                    <option value="">Select the year of the student</option>
                    <option value="1">Year 1</option>
                    <option value="2">Year 2</option>
                    <option value="3">Year 3</option>
                </select>
            </label>
            <label for="guardian_contact">
                <span class="label_image">
                    <img src="<?php echo $url?>/assets/images/icons/phone-portrait-outline.svg" alt="contact">
                </span>
                <input type="tel" name="guardian_contact" id="guardian_contact" title="Enter the contact of the student's guardian or parent"
                autocomplete="off" placeholder="Guardian Contact">
            </label>
        </div>
    </div>
    <div class="foot">
        <div class="flex flex-wrap gap-sm flex-eq wmax-xs sm-auto">
            <label for="submit" class="btn w-full sm-unset sp-unset">
                <button type="submit" name="submit" class="primary w-fluid sp-med xs-rnd" value="adminAddStudent1">Save</button>
            </label>
            <label for="cancel" class="btn w-full sm-unset sp-unset">
                <button type="reset" name="cancel" class="red w-fluid sp-med xs-rnd">Cancel</button>
            </label>
        </div>
    </div>
</form>
<?php else: ?>
<form action="<?php echo $url?>/admin/admin/submit.php" method="get" class="fixed" name="adminAddStudent">
    <div class="head">
        <h2>Add A Student</h2>
    </div>
    <div class="body">
        <div class="message_box success no_disp">
            <span class="message"></span>
            <div class="close"><span>&cross;</span></div>
        </div>
        <div class="joint">
            <input type="hidden" name="school_id" value="<?php echo $user_school_id?>">
            <label for="student_index">
                <span class="label_image">
                    <img src="<?php echo $url?>/assets/images/icons/index.png" alt="index">
                </span>
                <input type="text" name="student_index" id="student_index" required
                autocomplete="off" placeholder="Index Number*" title="Enter the index number of the student here">
            </label>
            <label for="lname">
                <span class="label_image">
                    <img src="<?php echo $url?>/assets/images/icons/person-outline.svg" alt="lname">
                </span>
                <input type="text" name="lname" id="lname" required autocomplete="off" placeholder="Lastname*"
                title="Enter the lastname of the student here">
            </label>
            <label for="oname">
                <span class="label_image">
                    <img src="<?php echo $url?>/assets/images/icons/person-outline.svg" alt="othernames">
                </span>
                <input type="text" name="oname" id="oname" required
                autocomplete="off" placeholder="Other Name(s)" title="Enter other names of the student">
            </label>
            <label for="gender">
                <span class="label_image">
                    <img src="<?php echo $url?>/assets/images/icons/male-female-outline.svg" alt="gender">
                </span>
                <select name="gender" id="gender" title="Select the gender" required>
                    <option value="">Select the gender</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>
            </label>
        </div>
        <div class="joint">
            <label for="boarding_status">
                <span class="label_image">
                    <img src="<?php echo $url?>/assets/images/icons/bed-outline.svg" alt="boarding_status">
                </span>
                <select name="boarding_status" id="boarding_status">
                    <option value="">Select Boarding Status</option>
                    <option value="Day">Day</option>
                    <option value="Boarder">Boarding</option>
                </select>
            </label>
            <label for="student_course">
                <span class="label_image">
                    <img src="<?php echo $url?>/assets/images/icons/book-outline.svg" alt="course">
                </span>
                <input type="text" name="student_course" id="student_course" title="Enter the selected course"
                autocomplete="off" placeholder="Enter the selected course*" required>
            </label>
            <label for="aggregate">
                <span class="label_image">
                    <img src="<?php echo $url?>/assets/images/icons/hashtag.png" alt="aggregate">
                </span>
                <input type="text" name="aggregate" id="aggregate" required pattern="[0-9]+" 
                maxlength="2" minlength="2" title="Enter the aggregate score of the student here" autocomplete="off" placeholder="Aggregate Score">
            </label>
            <label for="jhs">
                <span class="label_image">
                    <img src="<?php echo $url?>/assets/images/icons/city hall.png" alt="jhs">
                </span>
                <input type="text" name="jhs" id="jhs" title="Enter the name of the student's jhs school"
                autocomplete="off" placeholder="JHS School Attended">
            </label>
            <label for="dob">
                <span class="label_image">
                    <img src="<?php echo $url?>/assets/images/icons/calendar-number-outline.svg" alt="dob">
                </span>
                <input type="date" name="dob" id="dob" title="Enter the birthdate of the student"
                autocomplete="off" placeholder="Student's Birthday (mm-dd-yyyy)*">
            </label>
            <label for="track_id">
                <span class="label_image">
                    <img src="<?php echo $url?>/assets/images/icons/id card.png" alt="trackid">
                </span>
                <input type="text" name="track_id" id="track_id" required title="Enter the track id here"
                autocomplete="off" placeholder="Student's Track Id*">
            </label>
        </div>
    </div>
    <div class="foot">
        <div class="flex flex-wrap gap-sm flex-eq wmax-xs sm-auto">
            <label for="submit" class="btn w-full sm-unset sp-unset">
                <button type="submit" name="submit" class="primary w-fluid sp-med xs-rnd" value="adminAddStudent">Save</button>
            </label>
            <label for="cancel" class="btn w-full sm-unset sp-unset">
                <button type="reset" name="cancel" class="red w-fluid sp-med xs-rnd">Cancel</button>
            </label>
        </div>
    </div>
</form>
<?php endif; ?>