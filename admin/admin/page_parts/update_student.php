<?php //include_once("auth.php");
    $admin_mode = $_SESSION["admin_mode"] ?? "no-data";
    $programs = decimalIndexArray(fetchData("DISTINCT programme", "cssps","schoolID=$user_school_id",0));
?>

<div class="flex flex-column flex-center-align flex-center-content">
    <div id="getLoader"></div>
    <span class="item-event" style="color: white; margin-top: 10px; padding-left: 10px; text-align: center">Cancel</span>
</div>

<form action="<?php echo $url?>/admin/admin/submit.php" method="get" class="fixed" name="adminUpdateStudent">
    <div class="head">
        <h2>Update Student Details</h2>
    </div>
    <div class="body">
        <div class="message_box success no_disp">
            <span class="message"></span>
            <div class="close"><span>&cross;</span></div>
        </div>
        <div class="joint">
            <input type="hidden" name="school_id" value="<?php echo $user_school_id?>">
            <input type="hidden" name="admin_mode" value="<?= $admin_mode ?>">
            <label for="student_index">
                <span class="label_image">
                    <img src="<?php echo $url?>/assets/images/icons/index.png" alt="index">
                </span>
                <input type="text" name="student_index" id="student_index" required
                autocomplete="off" placeholder="Index Number*" readonly title="Enter the index number of the student here">
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
            <?php if($admin_mode == "admission"): ?>
            <label for="enrolCode" class="no_disp">
                <span class="label_image">
                    <img src="<?php echo $url?>/assets/images/icons/reader-outline.svg" alt="firstname">
                </span>
                <input type="text" name="enrolCode" id="enrolCode"
                autocomplete="off" placeholder="Enrolment Code*" title="Provide Student's enrolment code">
            </label>
            <?php endif; ?>
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
            <label for="boarding_status">
                <span class="label_image">
                    <img src="<?php echo $url?>/assets/images/icons/bed-outline.svg" alt="boarding_status">
                </span>
                <select name="boarding_status" id="boarding_status" disabled>
                    <option value="">Select Boarding Status</option>
                    <option value="Day">Day</option>
                    <option value="Boarder">Boarding</option>
                </select>
            </label>

            <label for="house" class="no_disp">
                <span class="label_image">
                    <img src="<?php echo $url?>/assets/images/icons/home.png" alt="house">
                </span>
                <select name="house" id="house" disabled>
                    <option value="0">Select House</option>
                    <?php 
                        $query = $connect->query("SELECT id, title, Gender FROM houses WHERE schoolID = $user_school_id");

                        while($row = $query->fetch_assoc()){
                    ?>
                    <option value="<?php echo $row["id"]?>"><?php echo $row["title"]." - ".$row["Gender"] ?></option>
                    <?php } ?>
                </select>
            </label>
        </div>
        <div class="joint">
            <label for="student_course">
                <span class="label_image">
                    <img src="<?php echo $url?>/assets/images/icons/book-outline.svg" alt="course">
                </span>
                <input type="text" name="student_course" id="student_course" title="Enter the selected course"
                autocomplete="off" list="program-list" placeholder="Enter the selected course*" required>
                
                <?php if($programs): ?>
                <datalist id="program-list">
                    <?php foreach($programs as $program): ?>
                        <option value="<?= $program["programme"] ?>">
                    <?php endforeach; ?>
                </datalist>
                <?php endif; ?>
            </label>
            <?php if($admin_mode == "records") : ?>
            <label for="program_id">
                <span class="label_image">
                    <img src="<?php echo $url?>/assets/images/icons/book-outline.svg" alt="course">
                </span>
                <select name="program_id" id="program_id">
                    <?php 
                        $sql = "SELECT program_id, program_name, short_form FROM program WHERE school_id=$user_school_id";
                        $query = $connect2->query($sql);
                        if($query->num_rows > 0){
                            echo <<<HTML
                            <option value="0">Select Class</option>
                            HTML;

                            while($row = $query->fetch_assoc()):
                    ?>
                    <option value="<?= $row["program_id"] ?>"><?= "{$row['program_name']} [{$row['short_form']}]" ?></option>
                    <?php  endwhile;
                        }else{
                            echo "<option value=''>No Class Uploaded</option>";
                        }
                    ?>
                </select>
            </label>
            <label for="year_level">
                <span class="label_image">
                    <img src="<?php echo $url?>/assets/images/icons/book-outline.svg" alt="course">
                </span>
                <select name="year_level" id="year_level">
                    <option value="">Select Form Year</option>
                    <option value="1">Form 1</option>
                    <option value="2">Form 2</option>
                    <option value="3">Form 3</option>
                </select>
            </label>
            <label for="guardianContact">
                <span class="label_image"><img src="<?= "$url/assets/images/icons/phone-portrait-outline.svg" ?>" alt="guardian contact"></span>
                <input type="tel" name="guardianContact" placeholder="Phone number of guardian" maxlength="10">
            </label>
            <?php endif; ?>
            <?php if($admin_mode == "admission"): ?>
            <label for="aggregate">
                <span class="label_image">
                    <img src="<?php echo $url?>/assets/images/icons/hashtag.png" alt="aggregate">
                </span>
                <input type="text" name="aggregate" id="aggregate" pattern="[0-9]+" 
                maxlength="2" minlength="2" title="Enter the aggregate score of the student here" autocomplete="off" placeholder="Aggregate Score">
            </label>
            <label for="jhs">
                <span class="label_image">
                    <img src="<?php echo $url?>/assets/images/icons/city hall.png" alt="jhs">
                </span>
                <input type="text" name="jhs" id="jhs" title="Enter the name of the student's jhs school"
                autocomplete="off" placeholder="JHS School Attended*">
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
                <input type="text" name="track_id" id="track_id" title="Enter the track id here"
                autocomplete="off" placeholder="Student's Track Id*">
            </label>
            <label for="guardian_contact">
                <span class="label_image">
                    <img src="<?php echo $url?>/assets/images/icons/phone-portrait-outline.svg" alt="contno">
                </span>
                <input type="tel" name="guardian_contact" id="guardian_contact" title="Enter a contact number"
                autocomplete="off" placeholder="Contact Number">
            </label>
            <?php endif; ?>
        </div>
    </div>
    <div class="foot btn w-full wmax-sm sm-auto">
        <div class="flex flex-wrap gap-sm flex-eq">
            <label for="submit" class="w-full sm-unset sp-unset">
                <button type="submit" name="submit" class="primary w-fluid sp-med" value="adminUpdateStudent">Update</button>
            </label>
            <label for="cancel" class="w-full sm-unset sp-unset">
                <button type="reset" name="cancel" class="red w-fluid sp-med">Cancel</button>
            </label>
        </div>
    </div>
</form>