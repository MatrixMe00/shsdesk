<section id="student" class="flex-all-center flex-column">
    <div class="selection w-fluid w-fluid-child flex flex-space-around flex-wrap">
        <div class="case" data-box="payment_form" id="school_admission_case">
            <h3>Online SHS Admission</h3><br>
            <label for="school_select" class="no_disp">
                <select name="school_select" id="school_select" class="primary">
                    <option value="NULL">Please select your school</option>
                    <option value="" class="selected"></option>
                </select>
            </label>
            <div class="flex flex-center-align gap-sm" id="pay_div">
                <label for="student_index_number" class="flex flex-column flex-wrap relative" style="flex: 4">
                    <span class="label_title">Provide your JHS index number below</span>
                    <input type="text" name="student_index_number" id="student_index_number" class="sp-lg ms-border" data-index="" placeholder="Enter JHS index number [Eg. 1000000000<?= $index_end ?>]">
                </label>
                <label for="student_check" class="btn sp-unset w-fluid-child self-align-end" style="flex:1">
                    <button name="student_check" type="button" id="student_check" class="sp-lg-tp primary">Check</button>
                </label>
                <label for="payment_button" class="btn sp-unset w-fluid-child self-align-end hide_label no_disp" style="flex: 1">
                    <button name="payment_button" id="payment_button" type="button" class="sp-lg-tp orange">Make My Payment</button>
                </label>
            </div>           
        </div>
    </div>

    <!-- cancel button -->
    <label for="student_cancel_operation" class="btn sm-lg-t w-fluid w-fluid-child" style="display:block; text-align: center; width: 100%;">
        <button name="student_cancel_operation" class="sp-lg-tp secondary" style="max-width: 480px ">Reset</button>
    </label>

    <!-- payment form -->
    <div id="payment_form" class="form_modal_box no_disp">
        <?php include_once($rootPath."/blocks/admissionPaymentForm.php");?>
    </div>

    <!-- admission form -->
    <div id="admission" class="form_modal_box flex no_disp">
        <?php include_once($rootPath.'/blocks/admissionForm.php')?>
    </div>
</section>