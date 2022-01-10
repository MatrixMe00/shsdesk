<?php @include_once('../../../includes/session.php')?>

<form action="<?php echo $url?>/admin/admin/submit.php" method="get" class="fixed" name="adminAddStudent">
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
                placeholder="Index Number*" title="Enter the index number of the student here">
            </label>
            <label for="lname">
                <span class="label_image">
                    <img src="<?php echo $url?>/assets/images/icons/person-outline.svg" alt="lname">
                </span>
                <input type="text" name="lname" id="lname" required placeholder="Lastname*"
                title="Enter the lastname of the student here">
            </label>
            <!-- <label for="fname">
                <span class="label_image">
                    <img src="<?php echo $url?>/assets/images/icons/person-outline.svg" alt="firstname">
                </span>
                <input type="text" name="fname" id="fname" required
                placeholder="Firstname*" title="Enter the firstname of the student">
            </label> -->
            <label for="oname">
                <span class="label_image">
                    <img src="<?php echo $url?>/assets/images/icons/person-outline.svg" alt="othernames">
                </span>
                <input type="text" name="oname" id="oname"
                placeholder="Other Name(s)" title="Enter other names of the student">
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
                placeholder="Enter the selected course*" required>
            </label>
            <label for="aggregate">
                <span class="label_image">
                    <img src="<?php echo $url?>/assets/images/icons/hashtag.png" alt="aggregate">
                </span>
                <input type="text" name="aggregate" id="aggregate" required pattern="[0-9]+" 
                maxlength="2" minlength="2" title="Enter the aggregate score of the student here" placeholder="Aggregate Score">
            </label>
            <label for="jhs">
                <span class="label_image">
                    <img src="<?php echo $url?>/assets/images/icons/city hall.png" alt="jhs">
                </span>
                <input type="text" name="jhs" id="jhs" required title="Enter the name of the student's jhs school"
                placeholder="JHS School Attended*">
            </label>
            <label for="dob">
                <span class="label_image">
                    <img src="<?php echo $url?>/assets/images/icons/calendar-number-outline.svg" alt="dob">
                </span>
                <input type="date" name="dob" id="dob" required title="Enter the birthdate of the student"
                placeholder="Student's Birthday (mm-dd-yyyy)*">
            </label>
            <label for="track_id">
                <span class="label_image">
                    <img src="<?php echo $url?>/assets/images/icons/id card.png" alt="trackid">
                </span>
                <input type="text" name="track_id" id="track_id" required title="Enter the track id here"
                placeholder="Student's Track Id*">
            </label>
        </div>
    </div>
    <div class="foot">
        <label for="submit" class="btn">
            <button type="submit" name="submit" value="adminAddStudent">Save</button>
        </label>
        <label for="cancel" class="btn">
            <button type="reset" name="cancel" onclick="$(this).parents('#modal').addClass('no_disp')">Cancel</button>
        </label>
    </div>
</form>

<script>
    $("form[name=adminAddStudent]").submit(function(event){
        event.preventDefault();

        //prepare defaults for message box
        form_name="adminAddStudent";
        time = 5;

        //send data
        response = formSubmit($(this), $("form[name=adminAddStudent] button[name=submit]"));
        
        if(response == true){
            message = "Data for " + $("form[name=adminAddStudent] #lname").val() + " was added successfully";
            message_type = "success";
        }else{
            message_type = "error";

            if(response == "index-number-empty"){
                message = "No index number was provided";
            }else if(response == "lastname-empty"){
                message = "No lastname was provided";
            }else if(response == "no-other-name"){
                message = "Othername field is empty";
            }else if(response == "gender-not-set"){
                message = "Please select a gender";
            }else if(response == "boardin-status-not-set"){
                message = "Please select a boarding status";
            }else if(response == "no-student-program-set"){
                message = "You have not specified student's program";
            }else if(response == "no-aggregate-set"){
                message = "No aggregate score has been provided";
            }else if(response == "aggregate-wrong"){
                message = "Aggregate score is invalid";
            }else if(response == "no-jhs-set"){
                message = "You have not provided student's JHS school";
            }else if(response == "no-dob"){
                message = "Please provide student's date of birth";
            }else if(response == "no-track-id"){
                message = "Please provide student's track id";
            }else{
                message = "An unknown error has occured. Please try again later";
            }
        }

        messageBoxTimeout(form_name, message, message_type, time);
    })
</script>