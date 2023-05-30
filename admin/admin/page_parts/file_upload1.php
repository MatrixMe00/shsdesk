<form action="<?php echo $url?>/read_excel.php" name="importForm" enctype="multipart/form-data" method="POST"
    class="wmax-md sm-sm">
        <div class="head" style="padding: 0 1em 0.5rem">
            <h5>NB:</h5>
            <ol>
                <li>Your file should be a spreadsheet file</li>
                <li>Spreadsheet files with .xls or .xlsx as extensions are acceptable</li>
                <li>Your data should have headings for easy entry into the database</li>
                <li>Make sure you have uploaded all your houses and their required details</li>
                <li>If you do not have the default spreadsheet file for upload, please click <a href="<?php echo $url?>/admin/admin/assets/files/default files/students_template.xlsx">
                this link</a> to download</li>
                <li>If you do not have the index number of the student, please leave that field blank</li>
                <li>Index Numbers should not be less than 10 characters if it is provided</li>
                <li>The name of the class should tally with the name of the class you provide the system with.
                    Failure to do this will cause a student not to be provided a class.
                </li>
                <li>The class is different from the program name. You can either provide the short form of the clas or the full name of the class.
                    Eg. You can either provide in the class name section "<em>Science 1</em>" or "<em>Sci 1</em>", but the programme can simply be 
                    "<u>General Science</u>"
                </li>
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
        <div class="foot flex-all-center w-full-child">
            <div class="flex flex-wrap w-fluid-child flex-eq gap-sm wmax-xs">
                <label for="submit" class="btn sm-unset sp-unset w-fluid-child">
                    <button type="submit" name="submit" class="primary sp-med" value="upload_students">Upload</button>
                </label>
                <label for="close" class="btn sm-unset sp-unset w-fluid-child">
                    <button type="reset" name="close" class="secondary sp-med">Close</button>
                </label>
            </div>
        </div>
    </form>