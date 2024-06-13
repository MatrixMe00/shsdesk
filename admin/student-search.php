<section>
    <!-- make search here -->
    <form action="<?= "$url/admin/submit.php" ?>" method="GET" class="student_search" name="search-student">
        <div class="head">
            <h2>Search a student data</h2>
        </div>
        <div class="body">
            <div class="search flex flex-wrap">
                <label for="txt_search" style="flex: 1; border: 1px solid grey">
                    <input type="text" name="txt_search" id="txt_search" placeholder="Enter search item [default is index number]">
                </label>
                <label for="submit" class="btn">
                    <button type="submit" name="submit" value="search_student">Search</button>
                </label>
            </div>
            <div class="search_options flex flex-wrap">
                <label for="enrolCode" class="checkbox gap-sm">
                    <input type="checkbox" name="enrolCode" id="enrolCode">
                    <span class="span_title">Search By Enrolment Code</span>
                </label>
                <label for="current_ad" class="checkbox gap-sm">
                    <input type="checkbox" name="current_ad" id="current_ad">
                    <span class="span_title">Current Admission Only</span>
                </label>
            </div>
        </div>
    </form>
</section>

<section id="loader" class="txt-al-c no_disp">
    <div class="body empty p-xlg-tp p-lg-lr">
        <p id="message"></p>
    </div>
</section>

<section id="form" class="no_disp">
    <form action="<?= "$url/admin/submit.php" ?>" method="POST" class="student_search" name="update-student">
        <div class="head">
            <h3>Student Details - <span id="index_number"></span></h3>
        </div>
        <div class="body">
            <div class="message_box success no_disp">
                <span class="message"></span>
                <div class="close"><span>&cross;</span></div>
            </div>
            <fieldset>
                <legend><?= $role_id < 3 ? "Non-Changeable Fields" : "Student Details" ?></legend>
                <div class="joint">
                    <label for="indexNumber" class="flex-column gap-sm">
                        <span class="label_title">Index Number</span>
                        <input type="text" name="indexNumber" id="indexNumber" readonly>
                    </label>
                    <label for="lname" class="flex-column gap-sm">
                        <span class="label_title">Lastname</span>
                        <input type="text" name="lname" id="lname" readonly>
                    </label>
                    <label for="oname" class="flex-column gap-sm">
                        <span class="label_title">Othername(s)</span>
                        <input type="text" name="oname" id="oname" readonly>
                    </label>
                    <label for="gender" class="flex-column gap-sm">
                        <span class="label_title">Gender</span>
                        <input type="text" name="gender" id="gender" readonly>
                    </label>
                    <?php if($role_id < 3): ?>
                    <label for="school" class="flex-column gap-sm">
                        <span class="label_title">School</span>
                        <input type="text" name="school" id="school" readonly>
                        <input type="hidden" name="school_id" id="school_id">
                    </label>
                    <?php endif; ?>
                    <label for="program" class="flex-column gap-sm">
                        <span class="label_title">Program</span>
                        <input type="text" name="program" id="program" readonly>
                    </label>
                    <label for="primary_phone" class="flex-column gap-sm">
                        <span class="label_title">Primary Phone Number</span>
                        <input type="text" name="primary_phone" id="primary_phone" readonly>
                    </label>
                    <label for="secondary_phone" class="flex-column gap-sm">
                        <span class="label_title">Secondary Phone Number</span>
                        <input type="text" name="secondary_phone" id="secondary_phone" readonly>
                    </label>
                    <label for="student_house" class="flex-column gap-sm">
                        <span class="label_title">House Allocated</span>
                        <input type="text" name="student_house" id="student_house" readonly>
                    </label>
                    <label for="boarding_status" class="flex-column gap-sm">
                        <span class="label_title">Boarding Status</span>
                        <input type="text" name="boarding_status" id="boarding_status" readonly>
                    </label>

                    <?php if($role_id < 3): ?>
                    <label for="witness_name" class="flex-column gap-sm">
                        <span class="label_title">Witness Name</span>
                        <input type="text" name="witness_name" id="witness_name" readonly>
                    </label>
                    <label for="witness_phone" class="flex-column gap-sm">
                        <span class="label_title">Witness Phone</span>
                        <input type="text" name="witness_phone" id="witness_phone" readonly>
                    </label>
                    <?php endif; ?>
                </div>
            </fieldset>

            <fieldset class="sm-lg-t">
                <legend><?= $role_id < 3 ? "Changeable Fields" : "Enrolment Data" ?></legend>
                <div class="joint">
                    <label for="enrol_code" class="flex-column gap-sm">
                        <span class="label_title">Enrolment Code</span>
                        <input type="text" name="enrol_code" id="enrol_code" <?= $role_id <= 2 ? "" : "readonly" ?>>
                    </label>
                    <?php if($role_id >= 3): ?>
                    <label for="witness_name" class="flex-column gap-sm">
                        <span class="label_title">Witness Name</span>
                        <input type="text" name="witness_name" id="witness_name" readonly>
                    </label>
                    <label for="witness_phone" class="flex-column gap-sm">
                        <span class="label_title">Witness Phone</span>
                        <input type="text" name="witness_phone" id="witness_phone" readonly>
                    </label>
                    <?php endif; ?>
                </div>
            </fieldset>

            <div class="btn w-full flex-wrap flex flex-eq gap-sm sm-auto wmax-3xs sm-lg-t">
                <button name="submit" type="button" class="primary" disabled>Save</button>
                <button type="reset" name="close" id="search_result_close" class="red">Close</button>
            </div>
        </div>
    </form>
</section>