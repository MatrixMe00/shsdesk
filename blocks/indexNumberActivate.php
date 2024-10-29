<form action="<?php echo $url?>/submit.php" method="post" class="fixed" name="indexNumberCheckerForm">
    <div class="head">
        <h2>Activate your index number</h2>
    </div>
    <div class="body">
        <div class="message_box success no_disp">
            <span class="message"></span>
            <div class="close"><span>&cross;</span></div>
        </div>
        <p>Please make sure your index number is valid for the <b><?= getAcademicYear(now()) ?></b> admission year</p>
        <div class="joint">
            <label for="check_school_id">
                <span class="label_image">
                    <img src="<?php echo $url?>/assets/images/icons/school-outline.svg" alt="gender">
                </span>
                <select name="check_school_id" id="check_school_id" required>
                    <option value="">Select Your Placement School</option>
                    <?php 
                        $schools = decimalIndexArray(fetchData("id, schoolName","schools", "Active = TRUE", 0));
                        if($schools): 
                            foreach($schools as $school):
                    ?>
                    <option value="<?= $school["id"] ?>" data-programs = ""><?= $school["schoolName"] ?></option>
                    <?php endforeach; endif; ?>
                </select>
            </label>
            <label for="check_programme">
                <span class="label_image">
                    <img src="<?php echo $url?>/assets/images/icons/book-outline.svg" alt="course">
                </span>
                <select name="check_programme" id="check_programme" required>
                    <option value="">Select Your Programme</option>
                </select>
            </label>
            <label for="students_index">
                <span class="label_image">
                    <img src="<?php echo $url?>/assets/images/icons/book-outline.svg" alt="course">
                </span>
                <select name="students_index" id="students_index" required>
                    <option value="">Select Your Name</option>
                </select>
            </label>
            <label for="check_index_number">
                <span class="label_image">
                    <img src="<?php echo $url?>/assets/images/icons/index.png" alt="index number">
                </span>
                <input type="text" name="check_index_number" id="check_index_number" placeholder="Enter Your JHS Index Number*" 
                    title="Enter your index number in this field. It should be only numbers" readonly>
            </label>
        </div>
        <div class="joint">
            <p id="check_status_span" class="txt-al-c no_disp">This is a status text</p>
        </div>
    </div>
    <div class="flex flex-eq flex-wrap gap-sm">
        <label for="submit" class="btn_label smt-unset sp-unset">
            <button type="submit" name="submit" value="activate_index_number" class="img_btn w-fluid green sp-md" id="checkIndexButton" disabled>
                <img src="<?php echo $url?>/assets/images/icons/lock.png" alt="lock">
                <span>Activate</span>
            </button>
        </label>
        <label for="modal_cancel" class="btn w-full sp-unset">
            <button type="reset" name="modal_cancel" value="cancel" class="sp-lg w-fluid secondary">Cancel</button>
        </label>
    </div>
    <div class="foot">
        <p>
            <?php echo "@".date("Y")." ".$_SERVER["SERVER_NAME"] ?>
        </p>
    </div>
</form>