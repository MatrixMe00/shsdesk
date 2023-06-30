<div class="yes_no_container white sp-xlg-lr sp-xxlg-tp sm-rnd wmax-sm wmin-unset w-full sm-auto">
        <div class="body txt-al-c sp-xxlg-tp">
            <p id="warning_content">Do you want to delete?</p>
        </div>

        <form action="<?php echo $url?>/admin/admin/submit.php" class="no_disp" name="table_yes_no_form" id="table_yes_no_form">
            <input type="hidden" name="indexNumber">
            <input type="hidden" name="school_id" value="<?php echo $user_school_id?>">
            <input type="hidden" name="submit" value="table_yes_no_submit">
            <input type="hidden" name="db" value="">
            <input type="hidden" name="addFirstYears" value="">
        </form>

        <div class="foot flex flex-center-content flex-center-align">
            <div class="btn p-lg flex-all-center w-full flex-eq sm-xlg-t gap-md">
                <button type="button" name="yes_button" class="plain-r green" onclick="$('#table_yes_no_form').submit();">Yes</button>
                <button type="button" name="no_button" class="plain-r red" onclick="$('#table_del').addClass('no_disp')">No</button>
            </div>
            
        </div>
    </div>