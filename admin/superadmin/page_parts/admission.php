    <?php include_once("session.php") ?>

    <form action="" method="post" name="admissiondetailsForm">
        <div class="body">
            <div class="message_box success">
                <span class="message">Here is a test message</span>
                <div class="close"><span>&cross;</span></div>
            </div>

            <input type="hidden" name="school_id" id="school_id">

            <div class="joint">
                <label for="head_name">
                    <span class="label_image">
                        <img src="<?php echo $url?>/assets/images/icons/person-outline.svg" alt="head name">
                    </span>
                    <input type="text" name="head_name" id="head_name" class="text_input" placeholder="Name of Head of School*" autocomplete="off" required>
                </label>
                <label for="head_title">
                    <span class="label_image">
                        <img src="<?php echo $url?>/assets/images/icons/bag-handle-outline.svg" alt="head title">
                    </span>
                    <input type="text" name="head_title" id="head_title" class="text_input" placeholder="Title of Head*" autocomplete="off" title="Enter the title of the head provided above" required>
                </label>
                <label for="sms_id">
                    <span class="label_image">
                        <img src="<?php echo $url?>/assets/images/icons/chatbox-outline.svg" alt="sms id">
                    </span>
                    <input type="text" name="sms_id" id="sms_id" class="text_input" placeholder="SMS Sender ID*" autocomplete="off" title="Provide the name to be seen when an sms is sent" required>
                </label>
            </div>
            
            <div class="joint">
                <label for="admission_year">
                    <span class="label_image">
                        <img src="<?php echo $url?>/assets/images/icons/calendar-number-outline.svg" alt="admission year">
                    </span>
                    <input type="text" name="admission_year" id="admission_year" class="text_input" placeholder="Admission" autocomplete="off"
                    <?php
                        //providing a value according to a calculated algorithm
                        $this_year = date("Y");
                        $this_month = date("m");
                        $admission_year = $this_year;

                        if($this_month < 9){
                            $admission_year = $this_year - 1;
                        }
                        
                        echo "value=\"$admission_year\""
                    ?>
                    title="This is generated automatically, but you can manually input the admission year" maxlength="4" minlength="4" pattern="[0-9]+" disabled>
                </label>
                <label for="academic_year">
                    <span class="label_image">
                        <img src="<?php echo $url?>/assets/images/icons/calendar-number-outline.svg" alt="academic year">
                    </span>
                    <input type="text" name="academic_year" id="academic_year" class="text_input" placeholder="Academic Year*" autocomplete="off" 
                    <?php
                        //get the academic year
                        $prev_year = null;
                        $next_year = null;
                        $this_date = date("Y-m-1");

                        if($this_date < date("Y-09-01")){
                            $prev_year = date("Y") - 1;
                            $next_year = date("Y");
                        }else{
                            $prev_year = date("Y");
                            $next_year = date("Y") + 1;
                        }

                        echo "value=\"$prev_year / $next_year\"";
                    ?>
                    title="Enter the current academic year" required maxlength="11" minlength="9" disabled>
                </label>
                <label for="reopening">
                    <span class="label_image">
                        <img src="<?php echo $url?>/assets/images/icons/person-outline.svg" alt="username_logo">
                    </span>
                    <input type="text" name="reopening" id="reopening" class="text_input" placeholder="Reopening Date [eg. <?php echo date("l, d M, Y")?>]*" autocomplete="off" required 
                    title="Enter the date of reopening">
                </label>
            </div>
            
            <label for="announcement">
                <span class="label_image">
                    <img src="<?php echo $url?>/assets/images/icons/megaphone-outline.svg" alt="announce">
                </span>
                <textarea name="announcement" id="announcement" placeholder="Enter an announcement here..." 
                autocomplete="off" title="Enter an announcement to be displayed when the student wants to log in or check admission"></textarea>
            </label>
            <label for="submit" class="btn">
                <button type="submit" name="submit" value="admissiondetails">Save</button>
            </label>
        </div>
        <div class="foot">
            <p>
                @<?php echo date("Y")." ".$_SERVER['HTTP_HOST']?>
            </p>
        </div>
    </form>

    <script src="<?php echo $url?>/assets/scripts/form/general.min.js?v=<?php echo time()?>"></script>
    <?php close_connections() ?>