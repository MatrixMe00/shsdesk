<form action="<?php echo $url?>/read_excel.php" name="importForm" enctype="multipart/form-data" method="POST">
        <div class="head" style="padding: 0 1em 0.5rem">
            <h5>NB:</h5>
            <ol>
                <li>Your file should be a spreadsheet file</li>
                <li>Spreadsheet files with .xls or .xlsx as extensions are acceptable</li>
                <li>Your data should have headings for easy entry into the database</li>
                <li>Make sure you have uploaded all your houses and their required details</li>
                <li>Make sure your file's last column is any of these: <strong><em>[F,G,H,I,J]</em></strong>. Anything outside these would be rejected</li>
                <li>Verify that the first column in your document is labeled <strong>"index number"</strong>. The system would mark a file without this first column as invalid</li>
                <li>If you do not have the default spreadsheet file for upload, please click on <a href="<?php echo $url?>/admin/admin/assets/files/default files/enrolment_template.xlsx">
                document1</a> or <a href="<?php echo $url?>/admin/admin/assets/files/default files/enrolment_template2.xlsx">document2</a> to download</li>
                <li>Please <strong>refrain</strong> from uploading the same document twice as this can lead to a <u>duplicate entry</u> of details if your document doesnt have the full index numbers of the students in place.
                    Quickly contact the system admins to assist you if you have a challenge
                </li>
            </ol>
        </div>
        <div class="body">
            <?php 
                $academic_year = getAcademicYear(now(), false); 
                $last_academic_year = fetchData("academic_year", "cssps", "schoolID=$user_school_id AND current_data = TRUE", order_by:"academic_year", asc: false); 
                $can_upload = $last_academic_year == "empty" || $last_academic_year["academic_year"] == $academic_year;

                if($can_upload):
            ?>
            <div class="message_box no_disp">
                <span class="message">Here is a test message</span>
                <div class="close"><span>&cross;</span></div>
            </div>
            <label for="academic_year" class="flex-wrap flex-column">
                <span class="label_title">Acadamic Year</span>
                <input type="text" name="academic_year" class="text_input" value="<?= $academic_year ?>" placeholder="Current Acadmic Year" >
            </label>
            <label for="import" class="file_label">
                <span class="label_title">Upload your file here</span>
                <div class="fore_file_display">
                    <input type="file" name="import" id="import" accept=".xls, .xlsx">
                    <span id="plus">+</span>
                    <span id="display_file_name">Choose or drag your file here</span>
                </div>
            </label>
            <?php else: ?>
                <p class="txt-al-c">Please visit <strong>History and Settings</strong> tab to clear the currently registered admission year to proceed</p>
            <?php endif; ?>
        </div>
        <div class="foot flex-all-center w-full-child">
            <div class="flex flex-wrap w-fluid-child flex-eq gap-sm wmax-xs">
                <?php if($can_upload): ?>
                <label for="submit" class="btn sm-unset sp-unset w-fluid-child">
                    <button type="submit" name="submit" class="primary sp-med" value="upload">Upload</button>
                </label>
                <?php endif; ?>
                <label for="close" class="btn sm-unset sp-unset w-fluid-child">
                    <button type="reset" name="close" class="secondary sp-med">Close</button>
                </label>
            </div>
        </div>
    </form>