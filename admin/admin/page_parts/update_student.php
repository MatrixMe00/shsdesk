<?php @include_once('../../../includes/session.php')?>

<div class="flex flex-column flex-center-align flex-center-content">
    <div id="getLoader"></div>
    <span class="item-event" style="color: white; margin-top: 10px; padding-left: 10px; text-align: center">Cancel</span>
</div>

<form action="<?php echo $url?>/admin/admin/submit.php" method="get" class="fixed" name="adminUpdateStudent">
    <div class="head">
        <h2>Add A New Student</h2>
    </div>
    <div class="body">
        <div class="message_box success no_disp">
            <span class="message"></span>
            <div class="close"><span>&cross;</span></div>
        </div>
        <div class="joint">
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
            <!-- <label for="fname">
                <span class="label_image">
                    <img src="<?php echo $url?>/assets/images/icons/person-outline.svg" alt="firstname">
                </span>
                <input type="text" name="fname" id="fname" required
                autocomplete="off" placeholder="Firstname*" title="Enter the firstname of the student">
            </label> -->
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

            <label for="house" class="no_disp">
                <span class="label_image">
                    <img src="<?php echo $url?>/assets/images/icons/home.png" alt="house">
                </span>
                <select name="house" id="house" disabled>
                    <option value="">Select House</option>
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
                <input type="text" name="jhs" id="jhs" required title="Enter the name of the student's jhs school"
                autocomplete="off" placeholder="JHS School Attended*">
            </label>
            <label for="dob">
                <span class="label_image">
                    <img src="<?php echo $url?>/assets/images/icons/calendar-number-outline.svg" alt="dob">
                </span>
                <input type="date" name="dob" id="dob" required title="Enter the birthdate of the student"
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
    <div class="foot flex">
        <label for="submit" class="btn">
            <button type="submit" name="submit" class="primary" value="" disabled>Update</button>
        </label>
        <label for="cancel" class="btn">
            <button type="reset" name="cancel" class="danger">Cancel</button>
        </label>
    </div>
</form>