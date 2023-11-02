<form action="<?= "$url/admin/admin/submit.php" ?>" class="w-full wmax-md color-dark" method = "POST" name="records_date">
    <?php 
        //get the records data
        $dates = fetchData1("start_date, end_date","record_dates", "school_id=$user_school_id");
        
        //set defaults
        $is_new_data =  true;
        $old_is_expired = false;
        $today = date("Y-m-d H:i");
        $old_class[] = "border";

        if(is_array($dates)){
            $is_new_data = false;
            $dates["start_date"] = date("Y-m-d H:i", strtotime($dates["start_date"]));
            $dates["end_date"] = date("Y-m-d H:i", strtotime($dates["end_date"]));

            if($today > $dates["end_date"]){
                $old_is_expired = true;
                $old_class[] = "b-red";
            }else{
                $old_class[] = "b-primary";
            }
        }else{
            $old_is_expired = true;
        }
    ?>
    <h4>Provide the time span for the records entry</h4>
    <div class="body">
        <div class="message_box no_disp">
            <span class="message"></span>
            <div class="close"><span>&cross;</span></div>
        </div>

        <?php if(!$is_new_data): ?>
        <div class="joint">
            <label for="start_date_o" class="flex-column gap-sm">
                <span class="label_title">Previous Start Date</span>
                <input type="datetime-local" name="start_date_o" id="start_date_o"
                    class="<?= implode(" ", $old_class) ?>" value="<?= $dates["start_date"] ?>" readonly> 
            </label>
            <label for="end_date_o" class="flex-column gap-sm">
                <span class="label_title">Previous End Date</span>
                <input type="datetime-local" name="end_date_o" id="end_date_o" 
                    class="<?= implode(" ", $old_class) ?>" value="<?= $dates["end_date"] ?>" readonly> 
            </label>
        </div>
        <?php endif; ?>
        
        <?php if(!$old_is_expired) : ?>
        <p class="color-orange txt-al-c">Your current record entry date has not expired</p>
        <div class="btn txt-al-c w-full">
            <button type="button" id="force_update" class="w-full wmax-xs">Force An Update</button>
        </div>
        <?php endif; ?>
        
        <div class="joint update_section <?= !$old_is_expired ? "no_disp sm-lg-t" : "" ?>">
            <label for="start_date" class="flex-column gap-sm">
                <span class="label_title">Provide the starting date</span>
                <input type="datetime-local" name="start_date" id="start_date" class="border b-secondary" value="<?= $today ?>"> 
            </label>
            <label for="end_date" class="flex-column gap-sm">
                <span class="label_title">Provide the final date for submission</span>
                <input type="datetime-local" name="end_date" id="end_date" class="border b-secondary" value=""> 
            </label>
        </div>
        <label for="date_submit" class="btn update_section sp-unset sm-auto w-full w-full-child wmax-xs p-lg <?= !$old_is_expired ? "no_disp" : "" ?>">
            <button type="submit" name="submit" id="date_submit" value="record_date" class="primary xs-rnd">Save</button>
        </label>
    </div>
</form>