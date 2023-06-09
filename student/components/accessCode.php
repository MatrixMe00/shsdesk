<?php require_once "compSession.php"; $_SESSION["active-page"] = "code" ?>
<?php $accessCode = fetchData1("*","accesstable","indexNumber={$student['indexNumber']} ORDER BY expiryDate DESC");
if(!is_array($accessCode)) : ?>
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
<section class="d-section txt-al-c">
    <h1 class="sm-lg-b">Your Active Access Code</h1>
    <form action="#" class="wmax-md gap-med txt-al-l w-full sm-auto-lr lt-shade-h">
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
                    <input type="tel" name="owner" value="<?= fetchData1("phoneNumber","transaction","transactionID='{$accessCode['transactionID']}'")["phoneNumber"] ?>" placeholder="Owner" readonly>
                </label>
                <label for="purchase_date" class="flex flex-column">
                    <span class="label_title">Date Purchased</span>
                    <input type="datetime-local" name="purchase_date" value="<?= date("Y-m-d H:i:s", strtotime($accessCode["datePurchased"])) ?>" readonly>
                </label>
                <label for="expiry_date" class="flex flex-column">
                    <span class="label_title">Expiry Date</span>
                    <input type="datetime-local" name="expiry_date" value="<?= date("Y-m-d H:i:s", strtotime($accessCode["expiryDate"])) ?>" readonly>
                </label>
            </div>
            <div class="label no-border txt-fl3 flex-all-center w-full sm-xlg-t">
                <strong><?= strtoupper($accessCode["accessToken"]) ?></strong>
            </div>
        </div>
    </form>
</section>
<?php endif; ?>

<script src="https://js.paystack.co/v1/inline.js" defer></script>
<script>
    $(document).ready(function(){
        const cust_amount = 5;

        $("input#price").val("GHC " + cust_amount.toFixed(2))

        $("form").submit(async function(e){
            e.preventDefault()
          
            if($("input[name=indexNumber]").val() == ""){
                alert_box("Please enter your index number", "danger")
            }else if($("input[name=lname]").val() == ""){
                alert_box("Please enter your lastname", "danger")
            }else if($("input[name=oname]").val() == ""){
                alert_box("Please enter your other name(s)", "danger")
            }else if($("input[name=email]").val() == ""){
                alert_box("Please enter your email, email can't be empty", "danger")
            }else if($("input[name=phoneNumber]").val() == ""){
                alert_box("Please enter your phone number", "danger")
            }else if($("input[name=phoneNumber]").val().length != 10){
                alert_box("Please enter your provide a valid 10 digit phone number", "danger")
            }else{
                payWithPaystack()
            }
        })

        function payWithPaystack(){
            //0551234987
            let cust_email = $("input[name=email]").val();
            const fullname = $("input[name=lname]").val() + " " + $("input[name=oname]").val()
            var returnValue = ""

            if(cust_amount <= 0){
                alert_box("Price is unacceptable", "danger"); return
            }

            var handler = PaystackPop.setup({
                // key: "pk_live_056157b8c9152eb97c1f04b2ed60e7484cd0d955",
                key: "pk_test_3a5dff723cbd3fe22c4770d9f924d05c77403fca",
                email: cust_email,
                amount: cust_amount * 100,
                currency: "GHS",
                // split_code: "SPL_U6mW80wZNH",
                metadata: {
                    custom_fields: [
                        {
                            display_name: "Mobile Number",
                            variable_name: "mobile_number",
                            value: $("input[name=phoneNumber]").val()
                        },
                        {
                            display_name: "Customer's Name",
                            variable_name: "customer_name",
                            value: fullname
                        },
                        {
                            display_name: "School Name",
                            variable_name: "school_name",
                            value: $("input[name=school_name]").val()
                        }
                    ]
                },
                callback: function(response){
                    $.ajax({
                        url:"./submit.php",
                        data: $("form[name=payForm]").serialize() + "&transaction_id=" + response.reference + "&submit=" + $("form[name=payForm]").find("button[name=submit]").attr("value"),
                        timeout: 10000,
                        method: "POST",
                        async: false,
                        beforeSend: function(){
                            $("form[name=payForm]").find("button[name=submit]").html("Payment Ongoing...");
                        },
                        success: function(response){
                            $("form[name=payForm]").find("button[name=submit]").html("Make Payment");
                            if(response == "success"){
                                alert("Payment successful")
                                location.reload()
                            }else{
                                if(response.indexOf("success") !== -1){
                                    alert_box("Details were saved, but an sms could not be sent. Please refresh the page", "primary", 10);
                                }else{
                                    alert_box(response, "danger", 12)
                                }                                
                            }
                        },
                        error: function(xhr){
                            let message = ""

                            if(xhr.statusText == "timeout"){
                                message = "Connection was timed out due to a slow network. Please try again later"
                            }else{
                                message = xhr.responseText
                            }

                            alert_box(message, "danger")
                            $("form[name=payForm]").find("button[name=submit]").html("Make Payment");
                        }
                    })
                },
                onClose: function(){
                    returnValue = "Transaction has been canceled by user";
                    return returnValue
                }
            });
            handler.openIframe();
        }
    })
</script>