<?php   
    if(isset($_REQUEST["school_id"]) && !empty($_REQUEST["school_id"])){
        $user_school_id = $_REQUEST["school_id"];
        $user_details = getUserDetails($_REQUEST["user_id"]);
        
        include_once("../../includes/session.php");
    }else{
        include_once("../../../includes/session.php");
    
        //set nav_point session
        $_SESSION["nav_point"] = "access";
    }
?>

<section class="p-xlg-tp p-med-lr">
    <p class="txt-al-c not_specify">
        In this view, you would be able to make a bulk purchase of access codes for your students.
        Please select the options below to make the purchase
    </p>
    <p class="txt-al-c specify primary no_disp">
        When specifying individual students, separate the names with a comma and a space. <br> Eg. Student1, Student2, etc
    </p>
</section>

<section class="sp-xlg-tp">
    <h3 class="txt-al-c sm-lg-b">Select which category of students you are buying for</h3>
    <div class="btn flex flex-wrap gap-sm sm-auto">
        <button class="plain-r primary btn-item" data-id="1" data-count="<?= fetchData1("COUNT(indexNumber) AS total","students_table","school_id=$user_school_id AND studentYear=1")["total"] ?>">Year 1 Only</button>
        <button class="plain-r primary btn-item" data-id="2" data-count="<?= fetchData1("COUNT(indexNumber) AS total","students_table","school_id=$user_school_id AND studentYear=2")["total"] ?>">Year 2 Only</button>
        <button class="plain-r primary btn-item" data-id="3" data-count="<?= fetchData1("COUNT(indexNumber) AS total","students_table","school_id=$user_school_id AND studentYear=3")["total"] ?>">Year 3 Only</button>
        <button class="plain-r primary btn-item" data-id="all" data-count="<?= fetchData1("COUNT(indexNumber) AS total","students_table","school_id=$user_school_id")["total"] ?>">All Students</button>
        <button class="plain-r primary btn-item specify-btn" data-count="0">Specify</button>
    </div>
    <label for="specify" class="specify no_disp flex-column">
        <span class="label_title">Please specify the full name of the student(s)</span>
        <input type="text" name="specify" id="specify" placeholder="Specify student or students">
        <div id="student_match" class="no_disp flex-wrap gap-md" style="max-height: 30vh; overflow: auto"></div>
    </label>
</section>

<section>
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

<script src="https://js.paystack.co/v1/inline.js" defer></script>
<script src="<?= "$url/admin/admin/assets/scripts/accesscode.js?v=".time() ?>"></script>