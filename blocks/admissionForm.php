<form action="<?php echo $url?>/submit.php" class="fixed" method="post" enctype="multipart/form-data" ng-controller="AdmissionController" name="admissionForm" style="max-height: 95vh">
    <div class="tabs">
        <span class="tab_button" data-views="view1">
            Step 1
        </span>
        <span class="tab_button no_disp" data-views="view2">
            Step 2
        </span>
        <span class="tab_button no_disp" id="sumView" data-views="view3">
            Step 3
        </span>
    </div>
    <div class="form_views" style="max-height: 60vh; overflow: auto">
        <div id="view1" class="view_box">
            <div class="head">
                <h2>Application Form</h2>
            </div>
            <p style="text-align: center;color: #c55; margin-bottom: 7px;" class="para_message">Parts with * means they are required fields</p>
            <div class="body">
                <fieldset id="enrol_field">
                    <legend>CSSPS Details</legend>
                    <div class="joint">
                        <label for="ad_transaction_id" class="no_disp">
                            <span class="label_image">
                                <img src="<?php echo $url?>/assets/images/icons/cash-outline.svg" alt="transaction">
                            </span>
                            <input type="text" name="ad_transaction_id" id="ad_transaction_id"
                            ng-model="ad_transaction_id" readonly placeholder="Transaction ID">
                        </label>
                        <label for="shs_placed" class="no_disp">
                            <span class="label_image">
                                <img src="<?php echo $url?>/assets/images/icons/school-outline.svg" alt="shs">
                            </span>
                            <input type="text" name="shs_placed" id="shs_placed" ng-model="shs_placed" readonly placeholder="SHS Name">
                        </label>
                        <label for="ad_enrol_code" class="no_disp">
                            <span class="label_image">
                                <img src="<?php echo $url?>/assets/images/icons/reader-outline.svg" alt="enrol code">
                            </span>
                            <input type="text" name="ad_enrol_code" id="ad_enrol_code" ng-model="ad_enrol_code"
                                placeholder="Your Enrolment Code*" title="Enter your enrolment code on your placement form"
                                maxlength="12" minlength="6" required ng-model-options="{ updateOn: 'input' }"
                                ng-change="ad_enrol_code = ad_enrol_code.toUpperCase()">
                        </label>
                        <label for="ad_index">
                            <span class="label_image">
                                <img src="<?php echo $url?>/assets/images/icons/index.png" alt="index number">
                            </span>
                            <input type="text" name="ad_index" id="ad_index" ng-model="ad_index" placeholder="Enter Your JHS Index Number*" 
                                title="Enter your index number in this field. It should be only numbers" required>
                        </label>
                        <label for="ad_aggregate" class="no_disp">
                            <span class="label_image">
                                <img src="<?php echo $url?>/assets/images/icons/hashtag.png" alt="aggregate">
                            </span>
                            <input type="text" name="ad_aggregate" id="ad_aggregate"
                            placeholder="Enter your aggregate here" title="Enter your aggregate here" 
                            ng-model="ad_aggregate" readonly>
                        </label>
                        <label for="ad_course" class="no_disp">
                            <span class="label_image">
                                <img src="<?php echo $url?>/assets/images/icons/book-outline.svg" alt="course">
                            </span>
                            <input type="text" name="ad_course" id="ad_course" title="Enter your course here" placeholder="Enter your course here*" 
                            ng-model="ad_course" readonly>
                        </label>
                    </div>
                </fieldset>

                <fieldset>
                    <legend>Personal Details of Candidate</legend>
                    <div class="joint">
                        <label for="ad_lname">
                            <span class="label_image">
                                <img src="<?php echo $url?>/assets/images/icons/person-outline.svg" alt="lastname">
                            </span>
                            <input type="text" name="ad_lname" id="ad_lname" class="text_input" 
                            placeholder="Your Lastname*" autocomplete="off" ng-model="ad_lname" 
                            title="Enter your lastname only, do not add any space" required readonly>
                        </label>
                        <label for="ad_oname">
                            <span class="label_image">
                                <img src="<?php echo $url?>/assets/images/icons/person-outline.svg" alt="othername">
                            </span>
                            <input type="text" name="ad_oname" id="ad_oname" class="text_input" placeholder="Other name(s)" 
                            autocomplete="off" ng-model="ad_oname" title="Enter any other name(s) you have" required readonly>
                        </label>
                    </div>
                    <label for="profile_pic" class="file_label">
                        <span class="label_title">Upload a profile picture [optional]</span>
                        <div class="fore_file_display">
                            <input type="file" name="profile_pic" id="profile_pic" class="file_input" data-show-file="0" data-disp-cont-id="" accept="image/*">
                            <span class="plus">+</span>
                            <span class="display_file_name">Choose or drag your file here</span>
                        </div>
                    </label>
                    <div class="joint">
                        <label for="ad_gender">
                            <span class="label_image">
                                <img src="<?php echo $url?>/assets/images/icons/male-female-outline.svg" alt="gender">
                            </span>
                            <select name="ad_gender" id="ad_gender" ng-model="ad_gender" required>
                                <option value="">Select Gender*</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </label>
                        <label for="ad_jhs">
                            <span class="label_image">
                                <img src="<?php echo $url?>/assets/images/icons/city hall.png" alt="jhs">
                            </span>
                            <input type="text" name="ad_jhs" id="ad_jhs" 
                            placeholder="Name of JHS School*" ng-model="ad_jhs" 
                            title="Enter the name of your JHS school" required>
                        </label>
                        <label for="ad_jhs_town">
                            <span class="label_image">
                                <img src="<?php echo $url?>/assets/images/icons/bismarck.png" alt="town">
                            </span>
                            <input type="text" name="ad_jhs_town" id="ad_jhs_town" placeholder="Town where JHS is found*" 
                            title="Enter the name of the city where your JHS is found" required ng-model="ad_jhs_town">
                        </label>
                        <label for="ad_jhs_district">
                            <span class="label_image">
                                <img src="<?php echo $url?>/assets/images/icons/bismarck.png" alt="district">
                            </span>
                            <input type="text" name="ad_jhs_district" id="ad_jhs_district" 
                            placeholder="District of JHS school*" ng-model="ad_jhs_district" 
                            title="Enter the name of your JHS's district" required>
                        </label>
                    </div>
                </fieldset>

                <fieldset>
                    <legend>Date of Birth*</legend>
                    <div class="joint">
                        <label for="ad_year">
                            <span class="label_image">
                                <img src="<?php echo $url?>/assets/images/icons/calendar-number-outline.svg" alt="year">
                            </span>
                            <select name="ad_year" id="ad_year" ng-model="ad_year" ng-click="birthDateArrange()" required>
                                <option value="">Select Year of Birth</option>
                                <?php
                                    $year1 = intval(date("Y") - 25);
                                    $year2 = intval(date("Y") - 8);
                                    
                                    for($i = $year1; $i <= $year2; $i++){
                                        echo "<option value='$i'>$i</option>";
                                    }
                                ?>
                            </select>
                        </label>
                        <label for="ad_birthdate" style="display: none;">
                            <input type="hidden" name="ad_birthdate" id="ad_birthdate" ng-model="ad_birthdate">
                        </label>
                        <label for="ad_month">
                            <span class="label_image">
                                <img src="<?php echo $url?>/assets/images/icons/calendar-number-outline.svg" alt="month">
                            </span>
                            <select name="ad_month" id="ad_month" ng-model="ad_month" ng-click="birthDateArrange()" required>
                                <option value="">Select Month of Birth</option>
                                <option value="1">January</option>
                                <option value="2">February</option>
                                <option value="3">March</option>
                                <option value="4">April</option>
                                <option value="5">May</option>
                                <option value="6">June</option>
                                <option value="7">July</option>
                                <option value="8">August</option>
                                <option value="9">September</option>
                                <option value="10">October</option>
                                <option value="11">November</option>
                                <option value="12">December</option>
                            </select>
                        </label>
                        <label for="ad_day">
                            <span class="label_image">
                                <img src="<?php echo $url?>/assets/images/icons/calendar-number-outline.svg" alt="day">
                            </span>
                            <select name="ad_day" id="ad_day" ng-model="ad_day" ng-click="birthDateArrange()" required>
                                <option value="">Select Your Day of Birth</option>
                            </select>
                        </label>
                        <label for="ad_birth_place">
                            <span class="label_image">
                                <img src="<?php echo $url?>/assets/images/icons/home.png" alt="birth_place">
                            </span>
                            <input type="text" name="ad_birth_place" id="ad_birth_place" placeholder="Place of Birth*" 
                             required ng-model="ad_birth_place">
                        </label>
                    </div>
                </fieldset>
            </div>
        </div>
        <div id="view2" class="view_box">
            <div class="head">
                <h2>Application Form</h2>
            </div>
            <p style="text-align: center;color: #c55; margin-bottom: 7px;" class="para_message">Parts with * means they are required fields</p>
            <div class="body">
                <fieldset>
                    <legend>Particulars of Parents/Guardians</legend>
                    <p><strong>Provide at least one parent or guardian's detail</strong></p>
                    <div class="joint">
                        <label for="ad_father_name">
                            <span class="label_image">
                                <img src="<?php echo $url?>/assets/images/icons/man-outline.svg" alt="father">
                            </span>
                            <input type="text" name="ad_father_name" id="ad_father_name" minlength="6" autocomplete="off" 
                            placeholder="Name of Father" ng-model="ad_father_name" class="required"
                            title="Enter the full name of your father">
                        </label>
                        <label for="ad_father_occupation" ng-show="ad_father_name">
                            <span class="label_image">
                                <img src="<?php echo $url?>/assets/images/icons/bag-handle-outline.svg" alt="occupation">
                            </span>
                            <input type="text" name="ad_father_occupation" id="ad_father_occupation" minlength="2" ng-model="ad_father_occupation" 
                            title="Enter the occupation of your father" class="required"
                            placeholder="Father's Occupation*">
                        </label>
                        <label for="ad_mother_name">
                            <span class="label_image">
                                <img src="<?php echo $url?>/assets/images/icons/woman-outline.svg" alt="mother">
                            </span>
                            <input type="text" name="ad_mother_name" id="ad_mother_name" minlength="6" autocomplete="off" 
                            placeholder="Name of Mother" ng-model="ad_mother_name" class="required"
                            title="Enter the full name of your mother">
                        </label>
                        <label for="ad_mother_occupation" ng-show="ad_mother_name">
                            <span class="label_image">
                                <img src="<?php echo $url?>/assets/images/icons/bag-handle-outline.svg" alt="occupation">
                            </span>
                            <input type="text" name="ad_mother_occupation" id="ad_mother_occupation" minlength="2" ng-model="ad_mother_occupation" 
                            title="Enter the occupation of your mother" class="required"
                            placeholder="Mother's Occupation*">
                        </label>
                        <label for="ad_guardian_name">
                            <span class="label_image">
                                <img src="<?php echo $url?>/assets/images/icons/man_woman.png" alt="">
                            </span>
                            <input type="text" name="ad_guardian_name" id="ad_guardian_name" minlength="6" autocomplete="off" 
                            placeholder="Name of Guardian" ng-model="ad_guardian_name" class="required"
                            title="Enter the full name of your guardian">
                        </label>
                        <label for="ad_postal_address">
                            <span class="label_image">
                                <img src="<?php echo $url?>/assets/images/icons/Sign Post.png" alt="postal address">
                            </span>
                            <input type="text" name="ad_postal_address" id="ad_postal_address" ng-model="ad_postal_address" 
                            placeholder="Postal Address" title="Enter your postal address">
                        </label>
                        <label for="ad_resident">
                            <span class="label_image">
                                <img src="<?php echo $url?>/assets/images/icons/home.png" alt="residence">
                            </span>
                            <input type="text" name="ad_resident" ng-model="ad_resident" id="ad_resident"
                            placeholder="Residential Address*" title="Enter the address of where you live" required>
                        </label>
                        <label for="ad_phone">
                            <span class="label_image">
                                <img src="<?php echo $url?>/assets/images/icons/phone-portrait-outline.svg" alt="phone">
                            </span>
                            <input type="tel" name="ad_phone" ng-model="ad_phone" id="ad_phone" maxlength="16" minlength="10"
                            placeholder="Primary Phone Number*" title="Enter the phone number easily accessible to you" pattern="[0-9+()\-\s]{10,15}"
                            required>
                        </label>
                        <label for="ad_other_phone">
                            <span class="label_image">
                                <img src="<?php echo $url?>/assets/images/icons/phone-portrait-outline.svg" alt="">
                            </span>
                            <input type="text" name="ad_other_phone" id="ad_other_phone" ng-model="ad_other_phone" maxlength="16" minlength="10"
                            placeholder="Secondary Phone number" title="Enter an alternative phone number" pattern="[0-9+()\-\s]{10,15}" />
                        </label>
                    </div>
                </fieldset>

                <fieldset>
                    <legend>Interests</legend>
                    <p>Indicate your interest</p>
                    <div class="joint">
                        <label for="ad_interest1" class="checkbox ad_interest">
                            <input type="checkbox" id="ad_interest1"  data-value="Athletics">
                            <span class="label_title">Athletics</span>
                        </label>
                        <label for="ad_interest2" class="checkbox ad_interest">
                            <input type="checkbox" id="ad_interest2"  data-value="Football">
                            <span class="label_title">Football</span>
                        </label>
                        <label for="ad_interest3" class="checkbox ad_interest">
                            <input type="checkbox" id="ad_interest3"  data-value="Debating Club">
                            <span class="label_title">Debating Club</span>
                        </label>
                        <label for="ad_interest4" class="checkbox ad_interest">
                            <input type="checkbox" id="ad_interest4"  data-value="Others">
                            <span class="label_title">Others</span>
                        </label>
                        <input type="hidden" name="interest" id="interest">
                    </div>
                </fieldset>
                <fieldset>
                    <legend>Others</legend>
                    <label for="ad_awards">
                        <span class="label_image">
                            <img src="<?php echo $url?>/assets/images/icons/ticket-outline.svg" alt="award">
                        </span>
                        <input type="text" name="ad_awards" id="ad_awards" title="Enter any awards (separated by commas)" placeholder="School Awards" ng-model="ad_awards">
                    </label>
                    <label for="ad_position">
                        <span class="label_image">
                            <img src="<?php echo $url?>/assets/images/icons/ticket-outline.svg" alt="icon">
                        </span>
                        <input type="text" name="ad_position" id="ad_position" title="Enter any positions you held in JHS (separate by commas)" placeholder="Position Held" ng-model="ad_position">
                    </label>
                </fieldset>

                <fieldset id="class_fieldset">
                    <legend>Class</legend>
                    <p><strong>Important</strong>: The program you're enrolling in is divided into several classes. These classes are grouped by major elective subjects.</p>
                    <div class="joint sp-med gap-md">
                        <select name="program_id" id="program_select" style="align-self: flex-start;">
                            <option value="">Select an elective class</option>
                        </select>
                        <span id="course_displays"></span>
                    </div>
                </fieldset>

                <fieldset>
                    <legend>Witness</legend>
                    <label for="ad_witness">
                        <span class="label_image">
                            <img src="<?php echo $url?>/assets/images/icons/person-outline.svg" alt="witness">
                        </span>
                        <input type="text" name="ad_witness" id="ad_witness" ng-model="ad_witness" title="Your witness could be a teacher, pastor, snr/public/civil servant, etc" placeholder="Witness' Name*" required>
                    </label>
                    <label for="ad_witness_phone">
                        <span class="label_image">
                            <img src="<?php echo $url?>/assets/images/icons/phone-portrait-outline.svg" alt="witness phone">
                        </span>
                        <input type="text" name="ad_witness_phone" id="ad_witness_phone" ng-model="ad_witness_phone" required minlength="10" maxlength = "16" 
                        placeholder="Witness' Contact Number*" title="Enter a valid phone number, and it should be only numbers" pattern="[0-9+()\-\s]{10,15}" />
                    </label>
                </fieldset>
            </div>                           
        </div>
        <div id="view3" class="view_box">
            <div class="head">
                <h2>Final Results</h2>
            </div>
            <p>Check to see if results are in the right form before proceeding</p>
            <div class="body">
                <fieldset>
                    <legend>CSSPS Details</legend>
                    <div class="joint">
                        <div class="label">
                            <div class="name">
                                <span>SHS Name</span>
                            </div>
                            <div class="value">
                                <span id="res_shs_placed"></span>
                            </div>
                        </div>
                        <div class="label">
                            <div class="name">
                                <span>Enrol Code</span>
                            </div>
                            <div class="value">
                                <span>{{ad_enrol_code}}</span>
                            </div>
                        </div>
                        <div class="label">
                            <div class="name">
                                <span>JHS Index Number</span>
                            </div>
                            <div class="value">
                                <span id="res_ad_index"></span>
                            </div>
                        </div>
                        <div class="label">
                            <div class="name">
                                <span>Six Aggregate</span>
                            </div>
                            <div class="value">
                                <span id="res_ad_aggregate"></span>
                            </div>
                        </div>
                        <div class="label">
                            <div class="name">
                                <span>Course Chosen</span>
                            </div>
                            <div class="value">
                                <span id="res_ad_course"></span>
                            </div>
                        </div>
                    </div>
                </fieldset>

                <fieldset>
                    <legend>Personal Details</legend>
                    <div class="joint">
                        <div class="label">
                            <div class="name">
                                <span>Lastname</span>
                            </div>
                            <div class="value">
                                <span id="res_ad_lname"></span>
                            </div>
                        </div>
                        <div class="label">
                            <div class="name">
                                <span>Other name(s)</span>
                            </div>
                            <div class="value">
                                <span id="res_ad_oname"></span>
                            </div>
                        </div>
                        <div class="label">
                            <div class="name">
                                <span>Profile Picture</span>
                            </div>
                            <div class="value">
                                <span id="res_ad_profile_picture">Not Set</span>
                            </div>
                        </div>
                        <div class="label">
                            <div class="name">
                                <span>Gender</span>
                            </div>
                            <div class="value">
                                <span id="res_ad_gender"></span>
                            </div>
                        </div>
                        <div class="label">
                            <div class="name">
                                <span>JHS Attended</span>
                            </div>
                            <div class="value">
                                <span id="res_ad_jhs">{{ad_jhs}}</span>
                            </div>
                        </div>
                        <div class="label">
                            <div class="name">
                                <span>JHS Location
                            </div>
                            <div class="value">
                                <span>{{ad_jhs_town}}</span>
                            </div>
                        </div>
                        <div class="label">
                            <div class="name">
                                <span>JHS District</span>
                            </div>
                            <div class="value">
                                <span>{{ad_jhs_district}}</span>
                            </div>
                        </div>
                        <div class="label">
                            <div class="name">
                                <span>Birthdate</span>
                            </div>
                            <div class="value">
                                <span id="res_ad_birthdate">{{ad_birthdate}}</span>
                            </div>
                        </div>
                        <div class="label">
                            <div class="name">
                                <span>Birth Place
                            </div>
                            <div class="value">
                                <span>{{ad_birth_place}}</span>
                            </div>
                        </div>
                    </div>
                </fieldset>

                <fieldset>
                    <legend>Particulars of Parents / Guardian</legend>
                    <div class="joint">
                        <div class="label" ng-show="ad_father_name">
                            <div class="name">
                                <span>Father's Name</span>
                            </div>
                            <div class="value">
                                <span>{{ad_father_name}}</span>
                            </div>
                        </div>
                        <div class="label" ng-show="ad_father_name">
                            <div class="name">
                                <span>Occupation of Father</span>
                            </div>
                            <div class="value">
                                <span>{{ad_father_occupation}}</span>
                            </div>
                        </div>
                        <div class="label" ng-show="ad_mother_name">
                            <div class="name">
                                <span>Maiden's Name</span>
                            </div>
                            <div class="value">
                                <span>{{ad_mother_name}}</span>
                            </div>
                        </div>
                        <div class="label" ng-show="ad_mother_name">
                            <div class="name">
                                <span>Maiden's Occupation</span>
                            </div>
                            <div class="value">
                                <span>{{ad_mother_occupation}}</span>
                            </div>
                        </div>
                        <div class="label" ng-show="ad_guardian_name">
                            <div class="name">
                                <span>Guardian's Name</span>
                            </div>
                            <div class="value">
                                {{ad_guardian_name}}
                            </div>
                        </div>
                        <div class="label" ng-show="ad_postal_address">
                            <div class="name">
                                <span>Postal Address</span>
                            </div>
                            <div class="value">
                                <span>{{ad_postal_address}}</span>
                            </div>
                        </div>
                        <div class="label">
                            <div class="name">
                                <span>Residential Address</span>
                            </div>
                            <div class="value">
                                <span>{{ad_resident}}</span>
                            </div>
                        </div>
                        <div class="label">
                            <div class="name">
                                <span>Contact Number 1</span>
                            </div>
                            <div class="value">
                                <span>{{ad_phone}}</span>
                            </div>
                        </div>
                        <div class="label" ng-show="ad_other_phone">
                            <div class="name">
                                <span>Contact Number 2</span>
                            </div>
                            <div class="value">
                                <span>{{ad_other_phone}}</span>
                            </div>
                        </div>
                    </div>
                </fieldset>

                <fieldset>
                    <legend>Interests</legend>
                    <div class="joint">
                        <div class="label">
                            <div class="name">
                                <span>Interests</span>
                            </div>
                            <div class="value" id="interest_value">
                                <span id="res_ad_interest"></span>
                            </div>
                        </div>
                    </div>
                </fieldset>

                <fieldset>
                    <legend>Other Information</legend>
                    <div class="joint">
                        <div class="label">
                            <div class="name">
                                <span>Awards</span>
                            </div>
                            <div class="value">
                                <span>{{ad_awards}}</span>
                            </div>
                        </div>
                        <div class="label">
                            <div class="name">
                                <span>Role(s) Held</span>
                            </div>
                            <div class="value">
                                <span>{{ad_position}}</span>
                            </div>
                        </div>
                        <div class="label">
                            <div class="name">
                                <span>Elective Class</span>
                            </div>
                            <div class="value">
                                <span id="program_display_val">N/A</span>
                            </div>
                        </div>
                    </div>
                </fieldset>

                <fieldset>
                    <legend>Witness Information</legend>
                    <div class="joint">
                        <div class="label">
                            <div class="name">
                                <span>Witness' Name</span>
                            </div>
                            <div class="value">
                                <span>{{ad_witness}}</span>
                            </div>
                        </div>
                        <div class="label">
                            <div class="name">
                                <span>Witness' Contact</span>
                            </div>
                            <div class="value">
                                <span>{{ad_witness_phone}}</span>
                            </div>
                        </div>
                    </div>
                </fieldset>
            </div>
            <label for="agree" class="checkbox">
                <input type="checkbox" name="agree" id="agree" value="agree">
                <span>I, <strong id="fullCandidateName"></strong> do accept that my admission to this school opens a new chapter in my life. I therefore pledge to abide by all the rules and regulations of the school</span>
            </label>
        </div>
    </div>
    
    <div class="message_box success no_disp">
        <span class="message"></span>
        <div class="close"><span>&cross;</span></div>
    </div>
    <div class="flex flex-eq flex-wrap gap-sm">
        <label for="continue" class="btn sw-full w-full sp-unset">
            <button name="continue" type="button" class="sp-lg w-fluid teal" disabled >Continue</button>
        </label>
        <label for="submit_admission" class="btn w-full smt-unset sp-unset no_disp">
            <button type ="button" name="submit_admission" class="img_btn w-fluid sp-md primary" disabled value="admissionFormSubmit">
                <img src="<?php echo $url?>/assets/images/icons/save-outline.svg" alt="save">
                <span>Save</span>
            </button>
        </label>
        <label for="print_summary" class="btn w-full smt-unset sp-unset no_disp">
            <button type ="button" name="print_summary" class="img_btn w-fluid sp-md indigo" disabled value="admissionFormSubmit">
                <img src="<?php echo $url?>/assets/images/icons/print-outline.svg" alt="print">
                <span>Print</span>
            </button>
        </label>
        <label for="modal_cancel" class="btn w-full sp-unset">
            <button type="reset" name="modal_cancel" class="sp-lg w-fluid secondary" value="cancel" ng-click="formreset()">Cancel</button>
        </label>
    </div>
    <div id="form_footer">
    </div>
    <a href="<?php echo $url?>/pdf_handle.php" class="no_disp" id="handle_pdf" target="_blank" rel="nofollow"></a>
</form>