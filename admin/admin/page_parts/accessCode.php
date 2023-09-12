<?php   
    include_once("auth.php");
    
    //set nav_point session
    $_SESSION["nav_point"] = "access";
?>

<section id="main_view" class="sp-xlg-tp sp-med-lr txt-al-c">
    <p>Please select an option to proceed</p>
    <div class="btn sm-auto p-lg m-med">
        <?php if($developmentServer): ?><button class="plain-r primary" data-main-section="enable-disable">Change Settings</button><?php endif; ?>
        <button class="plain-r primary" data-main-section="set-up">Set Up Access Code Price</button>
        <button class="plain-r primary" data-main-section="bulk-purchase">Purchase Bulk Access Code</button>
    </div>
</section>

<section class="p-xlg-tp p-med-lr btn_section bulk-purchase">
    <p class="txt-al-c not_specify">
        In this view, you would be able to make a bulk purchase of access codes for your students.
        Please select the options below to make the purchase. 
        Please note that you are only charged by the default price of 
        <strong id="default_price_text"></strong> per individual
    </p>
    <p class="txt-al-c specify primary no_disp">
        When specifying individual students, separate the names with a comma and a space. <br> Eg. Student1, Student2, etc
    </p>
</section>

<section class="sp-xlg-tp btn_section bulk-purchase">
    <h3 class="txt-al-c sm-lg-b">Select which category of students you are buying for</h3>
    <div class="btn flex flex-wrap gap-sm sm-auto">
        <?php
            //grab total students
            $year1 = fetchData1("COUNT(indexNumber) AS total","students_table","school_id=$user_school_id AND studentYear=1")["total"];
            $year2 = fetchData1("COUNT(indexNumber) AS total","students_table","school_id=$user_school_id AND studentYear=2")["total"];
            $year3 = fetchData1("COUNT(indexNumber) AS total","students_table","school_id=$user_school_id AND studentYear=3")["total"];

            //grab total number of students with active access codes
            $year1_access = fetchData1("COUNT(DISTINCT a.indexNumber) AS total","accesstable a JOIN students_table s ON a.indexNumber = s.indexNumber","a.school_id=$user_school_id AND s.studentYear=1 AND a.status=1")["total"];
            $year2_access = fetchData1("COUNT(DISTINCT a.indexNumber) AS total","accesstable a JOIN students_table s ON a.indexNumber = s.indexNumber","a.school_id=$user_school_id AND s.studentYear=2 AND a.status=1")["total"];
            $year3_access = fetchData1("COUNT(DISTINCT a.indexNumber) AS total","accesstable a JOIN students_table s ON a.indexNumber = s.indexNumber","a.school_id=$user_school_id AND s.studentYear=3 AND a.status=1")["total"];

            //get the current users who have no code
            $year1 -= $year1_access;
            $year2 -= $year2_access;
            $year3 -= $year3_access;
        ?>
        <button class="plain-r primary btn-item" data-id="1" data-count="<?= $year1 ?>">Year 1 Only [<?= $year1_access ?>]</button>
        <button class="plain-r primary btn-item" data-id="2" data-count="<?= $year2 ?>">Year 2 Only [<?= $year2_access ?>]</button>
        <button class="plain-r primary btn-item" data-id="3" data-count="<?= $year3 ?>">Year 3 Only [<?= $year3_access ?>]</button>
        <button class="plain-r primary btn-item" data-id="all" data-count="<?= $year1 + $year2 + $year3 ?>">All Students [<?= $year1_access + $year2_access + $year3_access ?>]</button>
        <button class="plain-r primary btn-item specify-btn" data-count="0">Specify</button>
    </div>
    <label for="specify" class="specify no_disp flex-column">
        <span class="label_title">Please specify the full name of the student(s)</span>
        <input type="text" name="specify" id="specify" placeholder="Specify student or students">
        <div id="student_match" class="no_disp flex-wrap gap-md" style="max-height: 30vh; overflow: auto"></div>
    </label>
    <p class="item-event txt-al-c info">[number] are students with active access codes</p>
</section>

<section class="btn_section bulk-purchase">
    <form action="<?= "$url/admin/admin/submit.php" ?>" name="payForm" method="post">
        <div class="head">
            <h2>Payment Information</h2>
        </div>
        <div class="body">
            <div class="joint">
                <label for="fullname" class="flex-column gap-sm">
                    <span class="label_title">Your Fullname</span>
                    <input type="text" name="fullname" id="fullname" placeholder="Provide your fullname" readonly value="<?= $user_details["fullname"] ?>">
                </label>
                <label for="email" class="flex-column gap-sm">
                    <span class="label_title">Your Email</span>
                    <input type="email" name="email" id="email" placeholder="Please provide your email address" value="<?= $user_details["email"] ?>" readonly>
                </label>
                <label for="phone_number" class="flex-column gap-sm">
                    <span class="label_title">Your phone number</span>
                    <input type="tel" class="light" name="phone_number" id="phone_number" placeholder="Please provide your phone number" value="<?= remakeNumber($user_details["contact"], false, false) ?>"
                        maxlength="10" minlength="10">
                </label>
                <input type="hidden" name="school_name" value="<?= getSchoolDetail($user_school_id)["schoolName"] ?>">
            </div>
            <div class="joint">
                <label for="recipient_number" class="flex-column gap-sm">
                    <span class="label_title">Payment for</span>
                    <input type="text" name="recipient_number" id="recipient_number" placeholder="Please provide the total number of students" value="0 Students" readonly>
                </label>
                <label for="payable_amount" class="flex-column gap-sm">
                    <span class="label_title">Payable Amount</span>
                    <input type="text" name="payable_amount" id="payable_amount" placeholder="Please provide the total payable amount" value="GHC 0.00" readonly>
                </label>
            </div>
        </div>
        
        <div class="btn w-full wmax-sm sm-auto p-lg">
            <button class="primary w-full" name="submit" value="access_check">Make Payment</button>
        </div>
    </form>
</section>

<section class="p-sm-tp p-med-lr txt-al-c btn_section set-up">
    <p>Use this view to set up the charge of your access code.</p>
    <p> Please note that the system automatically charges <b>GHC 6.00</b>, and any additions you make will be addressed to you
        if students should make payments only through the system. You cannot add more than <b>GHC 4.00</b> to the actual price
    </p>
</section>

<section class="btn_section set-up">
    <form action="<?= "$url/admin/admin/submit.php" ?>" name="accessPriceForm" method="post">
        <div class="head">
            <h2>Access Code Update</h2>
        </div><?php 
            //get user data on price
            $default_price = 6;
            $price = fetchData1("access_price","accesspay","school_id=$user_school_id");
            $price = $price == "empty" ? 0 : floatval($price["access_price"] - $default_price);
        ?>
        <div class="body">
            <div class="joint">
                <label for="default_price" class="flex-column gap-sm">
                    <span class="label_title">Default Price</span>
                    <input type="text" name="default_price" id="default_price" placeholder="The default charged price" readonly value="">
                </label>
                <label for="school_price" class="flex-column gap-sm">
                    <span class="label_title">Your Price</span>
                    <input type="text" name="school_price" id="school_price" placeholder="Only cash values should be entered. Eg: 1.02" value="<?= $price ?>">
                </label>
                <label for="total_price" class="flex-column gap-sm">
                    <span class="label_title">Total price</span>
                    <input type="text" name="total_price" id="total_price" placeholder="The total price" value="" readonly>
                </label>
                <input type="hidden" name="school_name" value="<?= getSchoolDetail($user_school_id)["schoolName"] ?>">
            </div>
        </div>
        
        <div class="btn w-full wmax-sm sm-auto p-lg">
            <button class="primary w-full" name="submit" value="change_access">Update Access Payment</button>
        </div>
    </form>
</section>

<?php if($developmentServer): ?>
<section class="p-sm-tp p-med-lr txt-al-c btn_section enable-disable">
    <p>This view determines if your school will allow students to make purchase of access codes or not</p>
</section>

<section class="btn_section enable-disable">
    <form action="<?= "$url/admin/admin/submit.php" ?>" name="changeAccess" method="post">
        <div class="head">
            <h2>Set Up Access Code</h2>
        </div>
        <div class="body">
            <label for="current" class="flex-column gap-sm">
                <span class="label_title">Current Setting</span>
                <?php $current = fetchData1("active","accesspay","school_id=$user_school_id") ?? false ?>
                <input type="text" name="" class="border white" value="<?= $current === false ? "Disabled":"Enabled" ?>" readonly>
                <input type="hidden" name="current" value="<?= $current["active"] ?? 0 ?>">
            </label>
            <label for="change" class="flex-column gap-sm">
                <span class="label_title">Enable/Disable</span>
                <select name="change" id="change">
                    <option value="">Select Setting</option>
                    <option value="0">Disable</option>
                    <option value="1">Enable</option>
                </select>
            </label>
        </div>
        
        <div class="btn w-full wmax-sm sm-auto p-lg">
            <button class="primary w-full" name="submit" value="change_access_setting">Change Settings</button>
        </div>
    </form>
</section>
<?php endif; ?>

<script src="https://js.paystack.co/v1/inline.js" defer></script>
<script src="<?= "$url/admin/admin/assets/scripts/accesscode.js?v=".time() ?>"></script>