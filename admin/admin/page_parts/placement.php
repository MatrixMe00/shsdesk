<?php include_once("../../../includes/session.php")?>
<section class="section_container">
    <div class="content" style="background-color: #007bff;">
        <div class="head">
            <h2>
                <?php
                    $res = $connect->query("SELECT indexNumber 
                    FROM cssps 
                    WHERE schoolID = $user_school_id");
                    
                    echo $res->num_rows;
                ?>
            </h2>
        </div>
        <div class="body">
            <span>Placed by CSSPS</span>
        </div>
    </div>

    <div class="content" style="background-color: #20c997">
        <div class="head">
            <h2>
                <?php
                    $res = $connect->query("SELECT indexNumber 
                    FROM cssps 
                    WHERE enroled = TRUE AND schoolID = $user_school_id");
                    
                    echo $res->num_rows;
                ?>
            </h2>
        </div>
        <div class="body">
            <span>Completed Online Admission Form</span>
        </div>
    </div>

    <div class="content" style="background-color: #ffc107">
        <div class="head">
            <h2>
                <?php
                    $res = $connect->query("SELECT indexNumber 
                    FROM cssps 
                    WHERE enroled = FALSE AND schoolID = $user_school_id");
                    
                    echo $res->num_rows;
                ?>
            </h2>
        </div>
        <div class="body">
            <span>Yet to Perform Online Admission</span>
        </div>
    </div>
</section>

<section id="placement_search">
    <div id="action">
        <div class="head">
            <h2>Placement Actions</h2>
        </div>
        <div class="body flex flex-wrap">
            <div class="btn">
                <button onclick="$('#modal').removeClass('no_disp')">Add New Student</button>
            </div>
            <div class="btn">
                <button>Registered Students</button>
            </div>
            <div class="btn">
                <button type="button" onclick="$('#modal_2').removeClass('no_disp')">Import From Excel</button>
            </div>
        </div>
    </div>
    <div class="display">
        <div class="title_bar flex flex-space-content flex-center-align teal">
            <div id="title">Registered Students</div>
            <div id="close">
                <span></span>
                <span></span>
            </div>
        </div>
        <div id="content">
            <div id="search" class="form" role="form">
                <div class="flex flex-center-align">
                    <label for="search">
                        <input type="search" name="search" id="search" title="Search a name or index number here" placeholder="Search by index number or name...">
                    </label>
                    <div class="btn">
                        <button>Search</button>
                    </div>
                </div>
            </div>
            <div id="body" class="empty">
                Nothing to show. Click the "Registered Students" button to refresh this box, else make a search using the search field
            </div>
        </div>
    </div>
    <div class="display">
        <div class="title_bar flex flex-space-content flex-center-align red">
            <div id="title">Unregistered Students</div>
            <div id="close">
                <span></span>
                <span></span>
            </div>
        </div>
        <div id="content">
            <div id="search" class="form" role="form">
                <div class="flex flex-center-align">
                    <label for="search">
                        <input type="search" name="search" id="search" title="Search a name or index number here" placeholder="Search by index number or name...">
                    </label>
                    <div class="btn">
                        <button>Search</button>
                    </div>
                </div>
            </div>
            <div id="body" class="empty">
                Nothing to show. Click the "Registered Students" button to refresh this box, else make a search using the search field
            </div>
        </div>
    </div>
</section>

<div id="modal_2" class="fixed no_disp form_modal_form">
    <form action="<?php echo $url?>/admin/admin/submit.php" name="importForm" enctype="multipart/form-data" method="POST">
        <h5>NB:</h5>
        <ol>
            <li>Your file should be a spreadsheet file</li>
            <li>Spreadsheet files with .xls or .xlsx as extensions are acceptable</li>
            <li>Your data should have headings for easy entry into the database</li>
            <li>If you do not have the default spreadsheet file for upload, please click <a href="<?php echo $url?>/admin/admin/assets/files/default files/enrolment_template.xlsx">here</a> to download</li>
        </ol>
        <br>
        <div id="message_box" class="no_disp">
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
        <div class="flex">
            <label for="submit" class="btn">
                <button type="submit" name="submit" value="upload">Upload</button>
            </label>
            <label for="close" class="btn">
                <button type="reset" name="close">Close</button>
            </label>
        </div>
    </form>
</div>

<script src="assets/scripts/placement.js?v=<?php echo time()?>"></script>