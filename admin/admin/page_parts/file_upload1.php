<form action="<?php echo $url?>/read_excel.php" name="importForm" enctype="multipart/form-data" method="POST">
        <div class="head" style="padding: 0 1em 0.5rem">
            <h5>NB:</h5>
            <ol>
                <li>Your file should be a spreadsheet file</li>
                <li>Spreadsheet files with .xls or .xlsx as extensions are acceptable</li>
                <li>Your data should have headings for easy entry into the database</li>
                <li>Make sure you have uploaded all your houses and their required details</li>
                <li>If you do not have the default spreadsheet file for upload, please click <a href="<?php echo $url?>/admin/admin/assets/files/default files/students_template.xlsx">
                this link</a> to download</li>
            </ol>
        </div>
        <div class="body">
            <div class="message_box no_disp">
                <span class="message">Here is a test message</span>
                <div class="close"><span>&cross;</span></div>
            </div>
            <label for="import" class="file_label">
                <span class="label_title">Upload your file here</span>
                <div class="fore_file_display">
                    <input type="file" name="import" id="import" accept=".xls, .xlsx">
                    <span id="plus">+</span>
                    <span id="display_file_name">Choose or drag your file here</span>
                </div>
            </label>
        </div>
        <div class="foot flex">
            <label for="submit" class="btn">
                <button type="submit" name="submit" value="upload_students">Upload</button>
            </label>
            <label for="close" class="btn">
                <button type="reset" name="close">Close</button>
            </label>
        </div>
    </form>