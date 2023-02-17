//reference id tracker
reference_parsed = false;
payment_received = false;
transaction_reference = "";
retry_counter = 0;

//variable to be used for tracking
trackKeeper = null;

function payWithPaystack(){
    //0551234987
    cust_amount = $("#pay_amount").val().split(" ");
    cust_amount = parseInt(cust_amount[1]) * 100;
    cust_email = $("form[name=paymentForm] .body #pay_email").val();

    if(cust_email == ""){
        cust_email = "successinnovativehub@gmail.com";
    }

    var handler = PaystackPop.setup({
        key: "pk_test_3a5dff723cbd3fe22c4770d9f924d05c77403fca",
        email: cust_email,
        amount: cust_amount,
        currency: "GHS",
        metadata: {
            custom_fields: [
                {
                    display_name: "Mobile Number",
                    variable_name: "mobile_number",
                    value: $("#pay_phone").val()
                },
                {
                    display_name: "Customer's Name",
                    variable_name: "customer_name",
                    value: $("#pay_fullname").val()
                },
                {
                    display_name: "School Name",
                    variable_name: "school_name",
                    value: $("#school_admission_case #school_select option:selected").html()
                }
            ]
        },
        callback: function(response){
            //mark that payment has been received
            payment_received = true;

            //pass a message that payment has been made
            message = "Transaction was made successfully";
            messageBoxTimeout("paymentForm", message, "success");
            alert_box(message, "success", 3.5)

            //pull out admission form right after payment
            displayAdmissionForm(response.reference);
            
            //send an sms
            sendSMS(response.reference);

            //parse data into database in the background
            passPaymentToDatabase(response.reference);
            
            //store reference into memory
            transaction_reference = response.reference;

            //check if transaction id is present in db after 3.5 seconds
            trackKeeper = setInterval(reCheck, 3500);
        },
        onClose: function(){
            alert_box('Transaction has been canceled by user', "secondary", 10);
        }
    });
    handler.openIframe();
}

//function to display the admission form
function displayAdmissionForm(trans_ref){
    //pass transaction id to admission form
    $("#ad_transaction_id").val(trans_ref);

    //pass index number into student box in admission form
    $("#ad_index").val($("#student_index_number").val());

    //click the continue button automatically to retrieve data and show form
    $("#ad_index").blur();
    $("#admission button[name=continue]").prop("disabled", false).click();
}

//this is a retry approach for the transactions
async function trackTransactions(reference = ""){
    if(!reference_parsed){
        fullname = $("#pay_fullname").val();
        email = $("#pay_email").val();
        phone = $("#pay_phone").val();

        amount = $("#pay_amount").val().split(" ");
        amount = amount[1];

        deduction = parseFloat((((1.95/100) + Number.EPSILON) * parseInt(amount)).toFixed(2));
        
        //get the selected school name and id
        school_name = $("#school_admission_case #school_select option:selected").html();
        school_id = $("#student #school_admission_case label #school_select").val();

        if(school_id > 0){
            dataString = "transaction_id=" + reference + "&contact_number=" + phone + "&school=" + school_id + "&amount=" + amount + "&deduction=" + 
                        deduction + "&contact_email=" + email + "&contact_name=" + fullname + "&submit=trackTransaction";

            await $.ajax({
                url: $("form[name=paymentForm]").attr("action"),
                type: "POST",
                dataType: "text",
                data: dataString,
                cache: false,
                success: function(response){
                    if(response.includes("success")){
                        //pass transaction id to admission form
                        $("#ad_transaction_id").val(reference);

                        //signal that reference has been parsed
                        reference_parsed = true;

                        message = "Reference ID confirmed";
                        alert_box(message, "success", 3.5)
                    }else if(response.includes("already-exist")){
                        //signal that reference is already parsed
                        reference_parsed = true;
                    }
                },
                error: function(e){
                    e = JSON.parse(JSON.stringify(e));
                    message = e["statusText"];

                    //enable only the transaction_id section
                    $("#pay_fullname, #pay_email, #pay_phone").prop("disabled", true);

                    $("#pay_reference").prop("disabled", false);

                    messageBoxTimeout("paymentForm",message, "error");

                    exit(1);
                }
            })
        }
    }

    return reference_parsed;
}

function reCheck(){
    if(transaction_reference != ""){
        ++retry_counter;                //indicate a retry count
        reference_parsed = trackTransactions(transaction_reference);
    }

    if(reference_parsed){
        //send an info to console
        console.log(transaction_reference + " successfully captured");
        
        //reset references
        reference_parsed = false;
        payment_received = false;
        transaction_reference = "";
        retry_counter = 0

        //stop interval check
        clearInterval(trackKeeper);
    }else if(retry_counter == 3 && !reference_parsed){ alert_box("Slow network detected", "warning", 8)}
}

function sendSMS(reference){
    //send transaction id as an sms
    phone = $("#pay_phone").val().replace("+","");
    message = "Your payment was successful. Your transaction reference is " + reference + ". You can use this if you experience a " + 
    "problem while filling your form and would want to try again";

    dataString = "submit=sendTransaction&phone=" + phone + "&message=" + message;

    $.ajax({
        url: "sms/sms.php",
        data: dataString,
        type: "POST",
        dataType: "json",
        success: function(response){
            response1 = JSON.parse(JSON.stringify(response));
            if(response1["status"] == "success"){
                alert_box("SMS sent successfully", "success");
            }else{
                alert_box('An sms could not be sent, but payment was successful. Your transaction reference is ' + reference + ". Save this value at a safe place", "warning", 10);
            }
        },
        error: function(){
            alert_box('An error occured while sending sms, but payment was successful. Your transaction reference is ' + reference + ". Save this value at a safe place", "warning", 15);
        }
    })
}

//function to pass data into database
async function passPaymentToDatabase(reference){
    fullname = $("#pay_fullname").val();
    email = $("#pay_email").val();
    phone = $("#pay_phone").val();

    amount = $("#pay_amount").val().split(" ");
    amount = amount[1];

    deduction = parseFloat((((1.95/100) + Number.EPSILON) * parseInt(amount)).toFixed(2));
    
    //get the selected school name and id
    school_name = $("#school_admission_case #school_select option:selected").html();
    school_id = $("#student #school_admission_case label #school_select").val();

    if(school_id > 0){
        dataString = "transaction_id=" + reference + "&contact_number=" + phone + "&school=" + school_id + "&amount=" + amount + "&deduction=" + 
                    deduction + "&contact_email=" + email + "&contact_name=" + fullname + "&submit=add_payment_data";

        await $.ajax({
            url: $("form[name=paymentForm]").attr("action"),
            type: "POST",
            dataType: "text",
            data: dataString,
            cache: false,
            success: function(response){
                if(response.includes("success")){
                    //pass admin number into admission form
                    cont = response.split("-")[1];
                    html = "<p style='text-align: center; font-size: small; color: #666'>" + 
                            "Finding trouble? Contact the admin via <a href='tel:" + cont + 
                            "' style='color: blue'>" + cont + "</a></p>";
                    $("#admission #form_footer").html(html);

                    message = "Transaction ID confirmed";
                    alert_box(message, "success", 3.5)
                }else if(response == "database_send_error"){
                    form_name = "paymentForm";
                    message = "Admin contact could not be retrieved";
                    message_type = "warning";
                    
                    alert_box(message, message_type)
                }else{
                    form_name = "paymentForm";
                    message = "An error prevented the admin contact to be retrieved";
                    message_type = "warning";
                    time = 10;
                    
                    alert_box(message, message_type)
                }
            },
            error: function(){
                message = "Error communicating with server. Validation failed on first try";

                //enable only the transaction_id section
                $("#pay_fullname, #pay_email, #pay_phone").prop("disabled", true);

                $("#pay_reference").prop("disabled", false);

                alert_box(message, "warning");

                exit(1);
            }
        })
    }
    
}

//what should happen when the payment form is submitted
$("form[name=paymentForm]").submit(function(){
    form_name = "paymentForm";

    //check if only the reference was given else, make payment to system
    if($("section#trans #pay_reference").val() == ""){
        //disable reference element
        $("#pay_reference").prop("disabled", true);
        $("#pay_fullname, #pay_email, #pay_phone").prop("disabled", false);

        //go to paystack payment method
        payWithPaystack();
    }else{
        //disable other input elements
        $("#pay_fullname, #pay_email, #pay_phone").prop("disabled", true).val("");

        school_id = $("#student #school_admission_case label #school_select").val();
        ref = $("section#trans #pay_reference").val();
        dataString = "reference_id=" + ref + "&submit=checkReference&school_id=" + school_id;

        $.ajax({
            url: $("form[name=paymentForm]").attr("action"),
            data: dataString,
            type: "POST",
            dataType: "text",
            cache: false,
            beforeSend: function(){
                message = loadDisplay({size:"small", animation:"anim-fade anim-swing"});
                messageType = "load";
                time = 0;
                messageBoxTimeout(form_name, message, messageType, time);
            },
            success: function(response){
                if(response.includes("success")){
                    //pass transaction id to admission form
                    $("form[name=admissionForm] #ad_transaction_id").val(ref);

                    //pass admin number into admission form
                    cont = response.split("-")[1];
                    html = "<p style='text-align: center; font-size: small; color: #666'>" + 
                            "Finding trouble? Contact the admin via <a href='tel:" + cont + 
                            "' style='color: blue'>" + cont + "</a></p>";
                    $("#admission #form_footer").html(html);

                    //pass index number into student box in admission form
                    $("#ad_index").val($("#student_index_number").val());

                    //click the continue button automatically
                    $("#ad_index").blur();
                    $("button[name=continue]").prop("disabled", false).click();

                    //display admission form
                    $('#admission').removeClass(`no_disp`);

                    messageType = "success";
                    message = "Verification was successful";
                    time = 5;
                }else{
                    if(response == "ref_expired"){
                        message = "Sorry! This reference id has expired. Please make a new payment to continue";
                        messageType = "error";
                        time = 5;
                    }else if(response == "error"){
                        message = "Reference ID entered is incorrect. Please enter a valid id to continue";
                        messageType = "error";
                        time = 5;
                    }else{
                        message = response;
                        messageType = "error";
                        time = 0;
                    }
                }
                messageBoxTimeout(form_name, message, messageType, time);
            },
            error: function(){
                message = "Error communicating with server. Please check your internet connection and try again later";
                messageType = "error";
                time = 5;
                messageBoxTimeout(form_name, message, messageType, time);
            }
        })
    }
})

$("#paymentFormButton").click(function(){
    $("form[name=paymentForm]").submit();
})

// $("#pay_email").blur(function(){
//     window.clearTimeout(null);
//     messageBoxTimeout("paymentForm","My Message", "load", 5);
// })