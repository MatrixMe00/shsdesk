<?php require_once "compSession.php"; $_SESSION["active-page"] = "code" ?>
<?php if(access_without_payment($student["school_id"])): ?>
    <section class="d-section">
        <form action="#" class="wmax-md white gap-med txt-al-l w-full sm-auto-lr lt-shade-h sp-xlg-tp sp-xxlg-lr">
        <h1 class="sm-xxlg-b txt-al-c color-secondary">Expired Access Code</h1>
            <div class="body gap-sm flex flex-column flex-eq sm-med-tp">
                <div class="joint gap-sm">
                    <label for="indexNumber" class="flex flex-column">
                        <span class="label_title">Index Number</span>
                        <input type="text" name="indexNumber" value="<?= $student["indexNumber"] ?>" placeholder="Index Number" readonly>
                    </label>
                    <label for="owner" class="flex flex-column">
                        <span class="label_title">Owner</span>
                        <input type="text" name="owner" value="<?= $student["Lastname"]." ".$student["Othernames"] ?>" placeholder="Owner" readonly>
                    </label>
                </div>
                <div class="joint gap-sm">
                    <label for="purchase_date" class="flex flex-column">
                        <span class="label_title">Duration</span>
                        <input type="text" name="purchase_date" value="N/A" readonly>
                    </label>
                </div>
                <div class="label no-border txt-fl3 flex-all-center w-full sm-xlg-t">
                    <strong class="color-green">FREE_ACCESS</strong>
                </div>
            </div>
        </form>
    </section>
<?php exit; endif; ?>
<?php 
    $accessCode = decimalIndexArray(fetchData1("*", "accesstable", ["indexNumber={$student['indexNumber']}"], order_by: "expiryDate", asc: false, limit: 0));
    $schoolHasApproved = fetchData1("access_price","accesspay","school_id={$student['school_id']} AND active=1");
    $activeCode = false;

    // check if last code is active
    if(is_array($accessCode)){
        $last_code = $accessCode[0];
        $activeCode = (bool) $last_code["status"];
    }

    // no purchase if school hasnt approved yet
    if($schoolHasApproved == "empty"):
?>
<section class="d-section sp-xxlg-tp txt-al-c lt-shade">
    <p>You cannot purchase an access code yet. Please contact your school admin or try again at another time</p>
</section>
<?php else: ?>
<input type="hidden" name="unit_price" id="unit_price" value="<?= $schoolHasApproved["access_price"] ?>">
<?php if(!$activeCode) : ?>
<section class="d-section txt-al-c">
    <h1 class="sm-lg-b">Purchase your Access Code</h1>
    <form action="<?= "$url/submit.php" ?>" class="wmax-md gap-med txt-al-l w-full sm-auto-lr lt-shade-h" method="POST" name="payForm">
        <div class="body flex flex-column gap-sm flex-eq sm-med-tp">
            <div class="joint gap-sm">
                <label for="indexNumber" class="flex flex-column">
                    <span class="label_title">Index Number</span>
                    <input type="text" name="indexNumber" id="indexNumber" value="<?= $student["indexNumber"] ?>" placeholder="Index Number" readonly>
                </label>
                <label for="lname" class="flex flex-column">
                    <span class="label_title">Lastname</span>
                    <input type="text" name="lname" id="lname" value="<?= $student["Lastname"] ?>" placeholder="Lastname" readonly>
                </label>
                <label for="oname" class="flex flex-column">
                    <span class="label_title">Othername(s)</span>
                    <input type="text" name="oname" id="oname" value="<?= $student["Othernames"] ?>" placeholder="Othername(s)" readonly>
                </label>
            </div>
            <div class="joint gap-sm">
                <label for="email" class="flex flex-column">
                    <span class="label_title">Email</span>
                    <input type="email" name="email" id="email" value="<?= $student["Email"] ?>" placeholder="Email [required]">
                </label>
                <label for="phoneNumber" class="flex flex-column">
                    <span class="label_title">Phone Number</span>
                    <input type="tel" name="phoneNumber" class="tel" id="phoneNumber" value="" placeholder="Phone Number [required]">
                </label>
                <label for="price" class="flex flex-column">
                    <span class="label_title">Cost</span>
                    <input type="text" name="price" id="price" value="" readonly>
                </label>
                <input type="hidden" name="school_name" value="<?= getSchoolDetail($student["school_id"])["schoolName"] ?>">
            </div>
            <label class="btn w-full sm-lg-t">
                <button class="teal w-full sp-lg" name="submit" value="make_payment">Make Payment</button>
            </label>
            <p id="stat_message" class="no_disp txt-al-c txt-fs">Some message</p>
        </div>
    </form>
</section>
<?php else : ?>
<section class="d-section">
    <form action="#" class="wmax-md white gap-med txt-al-l w-full sm-auto-lr lt-shade-h sp-xlg-tp sp-xxlg-lr">
    <h1 class="sm-xxlg-b txt-al-c">Your Active Access Code</h1>
        <div class="body gap-sm flex flex-column flex-eq sm-med-tp">
            <div class="joint gap-sm">
                <label for="indexNumber" class="flex flex-column">
                    <span class="label_title">Index Number</span>
                    <input type="text" name="indexNumber" value="<?= $student["indexNumber"] ?>" placeholder="Index Number" readonly>
                </label>
                <label for="owner" class="flex flex-column">
                    <span class="label_title">Owner</span>
                    <input type="text" name="owner" value="<?= $student["Lastname"]." ".$student["Othernames"] ?>" placeholder="Owner" readonly>
                </label>
            </div>
            <div class="joint gap-sm">
                <label for="owner" class="flex flex-column">
                    <span class="label_title">Purchase Number [Phone]</span>
                    <input type="tel" name="owner" value="<?= fetchData1("phoneNumber","transaction","transactionID='{$last_code['transactionID']}'")["phoneNumber"] ?>" placeholder="Owner" readonly>
                </label>
                <label for="purchase_date" class="flex flex-column">
                    <span class="label_title">Date Purchased</span>
                    <input type="text" name="purchase_date" value="<?= date("M d, Y H:i:s", strtotime($last_code["datePurchased"])) ?>" readonly>
                </label>
                <label for="expiry_date" class="flex flex-column">
                    <span class="label_title">Expiry Date</span>
                    <input type="text" name="expiry_date" value="<?= date("M d, Y H:i:s", strtotime($last_code["expiryDate"])) ?>" readonly>
                </label>
            </div>
            <div class="label no-border txt-fl3 flex-all-center w-full sm-xlg-t">
                <strong class="color-green"><?= strtoupper($last_code["accessToken"]) ?></strong>
            </div>
        </div>
    </form>
</section>
<?php endif;
    if(is_array($accessCode)):
        for($count = $activeCode ? 1 : 0; $count < count($accessCode); $count++):
            $access_code = $accessCode[$count];
?>
<section class="d-section">
    <form action="#" class="wmax-md white gap-med txt-al-l w-full sm-auto-lr lt-shade-h sp-xlg-tp sp-xxlg-lr">
    <h1 class="sm-xxlg-b txt-al-c color-secondary">Expired Access Code</h1>
        <div class="body gap-sm flex flex-column flex-eq sm-med-tp">
            <div class="joint gap-sm">
                <label for="indexNumber" class="flex flex-column">
                    <span class="label_title">Index Number</span>
                    <input type="text" name="indexNumber" value="<?= $student["indexNumber"] ?>" placeholder="Index Number" readonly>
                </label>
                <label for="owner" class="flex flex-column">
                    <span class="label_title">Owner</span>
                    <input type="text" name="owner" value="<?= $student["Lastname"]." ".$student["Othernames"] ?>" placeholder="Owner" readonly>
                </label>
            </div>
            <div class="joint gap-sm">
                <label for="owner" class="flex flex-column">
                    <span class="label_title">Purchase Number [Phone]</span>
                    <input type="tel" name="owner" value="<?= fetchData1("phoneNumber","transaction","transactionID='{$access_code['transactionID']}'")["phoneNumber"] ?>" placeholder="Owner" readonly>
                </label>
                <label for="purchase_date" class="flex flex-column">
                    <span class="label_title">Date Purchased</span>
                    <input type="text" name="purchase_date" value="<?= date("M d, Y H:i:s", strtotime($access_code["datePurchased"])) ?>" readonly>
                </label>
                <label for="expiry_date" class="flex flex-column">
                    <span class="label_title">Expiry Date</span>
                    <input type="text" name="expiry_date" value="<?= date("M d, Y H:i:s", strtotime($access_code["expiryDate"])) ?>" readonly>
                </label>
            </div>
            <div class="label no-border txt-fl3 flex-all-center w-full sm-xlg-t">
                <strong class="color-red"><?= strtoupper($access_code["accessToken"]) ?></strong>
            </div>
        </div>
    </form>
</section>
<?php /*000000001*/
        endfor;
    endif;

endif; ?>

<script src="https://js.paystack.co/v1/inline.js" defer></script>
<script src="<?= "$url/assets/scripts/accesscode.js?v=".time() ?>"></script>