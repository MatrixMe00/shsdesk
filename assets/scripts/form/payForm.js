//reference id tracker
let reference_parsed = false;
let payment_received = false;
let transaction_reference = "";
let retry_counter = 0;
let api_key = ""; let school_split_code = "";
let cust_amount = 0;

//elements
const paymentForm = $("form[name=paymentForm]");
const selectedSchool = $("#school_select");

//variable to be used for tracking
trackKeeper = null;

// get the system price
$(document).ready(async function(){
    cust_amount = await ajaxCall({
        url: "submit.php",
        formData: {submit: "get_admission_price"},
        method: "POST",
        returnType: "json"
    });
    
    cust_amount = parseFloat(cust_amount["price"]) || 0;
})

function payWithPaystack(){
    if(parseFloat(cust_amount) <= 0){
        alert_box("Could not find payment price");
        return;
    }

    return new Promise((resolve, reject) => {
        // cust_amount = parseInt(cust_amount[1]) * 100;
        cust_amount = 30.6 * 100;
        const cust_email = paymentForm.find("#pay_email").val();

        try {
            var handler = PaystackPop.setup({
                key: api_key,
                email: cust_email,
                amount: cust_amount,
                currency: "GHS",
                split_code: school_split_code,
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
                        },
                        {
                            display_name: "Payment Type",
                            variable_name: "payment_type",
                            value: "admission"
                        }
                    ]
                },
                callback: function(response){
                    //clear any rechecks happening
                    clearInterval(trackKeeper);

                    //mark that payment has been received
                    payment_received = true;

                    //send an sms
                    sendSMS(response.reference);

                    //store reference into memory
                    transaction_reference = response.reference;
        
                    //pass a message that payment has been made
                    message = "Transaction was made successfully";
                    messageBoxTimeout("paymentForm", message, "success");
        
                    resolve(response.reference);
                },
                onClose: function(){
                    alert_box('Transaction has been canceled by user', "secondary", 10);
                    reject(false);
                }
            });
            handler.openIframe();   
        } catch (error) {
            error = error.toString();

            if(error.indexOf("PaystackPop is not defined") > -1){
                alert_box("You are currently offline. Please check your internet connection and try again later", "danger", 7);
            }else{
                alert_box(error, "danger")
            }

            reject(false);
        }
    })
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

//this is a retry approach for the transactions to verify it has been inserted
async function trackTransactions(reference = ""){
    if(!reference_parsed){
        const fullname = $("#pay_fullname").val();
        const email = $("#pay_email").val();
        const phone = $("#pay_phone").val();

        let amount = $("#pay_amount").val().split(" ");
        amount = amount[1];

        const deduction = parseFloat((((1.95/100) + Number.EPSILON) * parseInt(amount)).toFixed(2));
        
        //get the selected school id
        const school_id = parseInt(selectedSchool.val());

        if(school_id > 0){
            const dataString = {
                transaction_id: reference, contact_number: phone,
                school: school_id, amount: amount, deduction: deduction,
                contact_email: email, contact_name: fullname, submit: "trackTransaction"
            };

            const ajax = await $.ajax({
                url: paymentForm.attr("action"),
                type: "POST",
                dataType: "text",
                data: dataString,
                cache: false,
                timeout: 30000,
                success: function(response){
                    if(response == "success"){
                        //pass transaction id to admission form
                        $("#ad_transaction_id").val(reference);

                        //signal that reference has been parsed
                        reference_parsed = true;
                        transaction_reference = "";

                        message = "Reference ID confirmed";
                        alert_box(message, "success", 3.5)
                    }else if(response == "already-exist"){
                        //signal that reference is already parsed
                        reference_parsed = true;
                        transaction_reference = "";
                    }else{
                        console.log(response);
                    }
                },
                error: function(e, textStatus, errorThrown){
                    message = "Error occured while tracking your transaction";
                    time = 5

                    if(textStatus == "timeout"){
                        message = "Connection was timed out due to a slow network. Please try again later"
                        time = 8;
                    }else if(e.responseText.indexOf("php") < 0){
                        message = errorThrown;
                    }
                    
                    console.log(e);

                    //enable only the transaction_id section
                    $("#pay_fullname, #pay_email, #pay_phone").prop("disabled", true);

                    $("#pay_reference").prop("disabled", false);

                    messageBoxTimeout("paymentForm",message, "error", time);
                }
            })
        }
    }

    return reference_parsed;
}

async function reCheck() {
    if (transaction_reference != "") {
        ++retry_counter; // indicate a retry count
        reference_parsed = await trackTransactions(transaction_reference);
    }

    if (reference_parsed) {
        // reset references
        reference_parsed = false;
        payment_received = false;
        transaction_reference = "";
        retry_counter = 0;

        // stop interval check
        clearInterval(trackKeeper);
    } else if (retry_counter == 3 && !reference_parsed) {
        alert_box("Slow network detected", "warning", 8);
    } else {
        // Retry after a short delay (e.g., 3 seconds)
        setTimeout(async function () {
            await reCheck();
        }, 3000);
    }
}

async function sendSMS(reference){
    //send transaction id as an sms
    const phone = $("#pay_phone").val().replace("+","");
    const message = "Your payment was successful. Your transaction reference is " + reference + ". You can use this if you experience a " + 
    "problem while filling your form and would want to try again";

    dataString = {submit: "sendTransaction", phone: phone, message: message};

    const ajax = await $.ajax({
        url: "sms/sms.php",
        data: dataString,
        type: "POST",
        dataType: "json",
        timeout: 30000,
        success: function(response){
            const response1 = JSON.parse(JSON.stringify(response));
            if(response1["status"] == "success"){
                alert_box("SMS sent successfully", "success");
            }else{
                const sms_message = "An sms could not be sent, but payment was successful. Your transaction reference is " + reference + ". Save this value at a safe place";
                alert_box(sms_message, "danger", 10);
            }
        },
        error: function(e, textStatus){
            alert_box('An error occured while sending sms, but payment was successful. Your transaction reference is ' + reference + ". Save this value at a safe place", "warning", 15);
            if(textStatus == "timeout"){
                alert_box("Connection was timed out due to a slow network. Please try again later", "danger")
            }else{
                console.log(e.responseText)
            }
        }
    })
}

//function to pass data into database
async function passPaymentToDatabase(reference){
    const fullname = $("#pay_fullname").val();
    const email = $("#pay_email").val();
    const phone = $("#pay_phone").val();

    let amount = $("#pay_amount").val().split(" ");
    amount = amount[1];

    const deduction = parseFloat((((1.95/100) + Number.EPSILON) * parseInt(amount)).toFixed(2));
    
    //get the selected school name and id
    const school_id = selectedSchool.val();

    if(school_id > 0){
        const dataString = {
            transaction_id: reference, contact_number: phone, school: school_id,
            amount: amount, deduction: deduction, contact_email: email, contact_name: fullname,
            submit: "add_payment_data"
        };

        const ajax = await $.ajax({
            url: paymentForm.attr("action"),
            type: "POST",
            dataType: "text",
            data: dataString,
            cache: false,
            timeout: 30000,
            success: function(response){
                if(response.includes("success")){
                    //pass admin number into admission form
                    cont = response.split("-")[1];
                    html = "<p style='text-align: center; font-size: small; color: #666'>" + 
                            "Having trouble? Contact the admin via <a href='tel:" + cont + 
                            "' style='color: blue'>" + cont + "</a></p>";
                    $("#admission #form_footer").html(html);

                    message = "Transaction ID parsed";
                    reference_parsed = true;
                    transaction_reference = "";

                    alert_box(message, "success", 3);
                }else if(response == "database_send_error"){
                    form_name = "paymentForm";
                    message = "Admin contact could not be retrieved";
                    
                    alert_box(message, "warning");
                    console.log(response);
                }else{
                    form_name = "paymentForm";
                    message = "An error prevented the admin contact to be retrieved";
                    
                    alert_box(message, "warning", 10);
                    console.log(response);
                }
            },
            error: function(xhr, textStatus, errorThrown){
                message = "Error communicating with server. Validation failed on first try";

                if(textStatus == "timeout"){
                    message = "Connection was timed out due to a slow network. Please try again later"
                }else if(!xhr.responseText.indexOf("php") < 0){
                    message = errorThrown;
                }
                
                //enable only the transaction_id section
                $("#pay_fullname, #pay_email, #pay_phone").prop("disabled", true);

                $("#pay_reference").prop("disabled", false);

                alert_box(message, "warning");
            }
        })
    }    
}

//what should happen when the payment form is submitted
paymentForm.submit(async function(){
    const form_name = "paymentForm";

    //check if only the reference was given else, make payment to system
    if($("section#trans #pay_reference").val() == ""){
        //disable reference element
        $("#pay_reference").prop("disabled", true);
        $("#pay_fullname, #pay_email, #pay_phone").prop("disabled", false);

        if($("#pay_fullname").val() === "" || $("pay_fullname").val() === null){
            messageBoxTimeout("paymentForm", "Please provide your fullname", "error");
        }else if($("#pay_email").val() === "" || $("pay_email").val() === null){
            messageBoxTimeout("paymentForm", "Please provide your email", "error");
        }else if($("#pay_phone").val() === "" || $("pay_phone").val() === null){
            messageBoxTimeout("paymentForm", "Please provide your phone number", "error");
        }else{
            try {
                //go to paystack payment method
                const response = await payWithPaystack();

                if(response){
                    //pull out admission form right after payment
                    displayAdmissionForm(response);
        
                    //parse data into database in the background
                    passPaymentToDatabase(response);
        
                    //check if transaction id is present in db after 3.5 seconds
                    trackKeeper = setInterval(reCheck, 3500);
                }else{
                    console.log(response);
                    alert_box("Message: " + response);
                }
            } catch (error) {
                console.log("Payment Error: ", error);
            }
        }        
    }else{
        //disable other input elements
        $("#pay_fullname, #pay_email, #pay_phone").prop("disabled", true).val("");

        const school_id = selectedSchool.val();
        const ref = $("section#trans #pay_reference").val();
        const dataString = {reference_id: ref, submit: "checkReference", school_id: school_id};

        $.ajax({
            url: paymentForm.attr("action"),
            data: dataString,
            type: "POST",
            dataType: "text",
            cache: false,
            timeout: 30000,
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
            error: function(xhr, textStatus, errorThrown){
                message = "Error communicating with server. Please check your internet connection and try again later";
                
                if(textStatus === "timeout"){
                    message = "Connection was timed out due to a slow network. Please try again later"
                }
                messageType = "error";
                time = 5;
                console.log(errorThrown);
                messageBoxTimeout(form_name, message, messageType, time);
            }
        })
    }
})

function getKey() {
    const school_id = selectedSchool.val();
    
    return new Promise((resolve, reject) => {
        $.ajax({
        url: "./submit.php",
        data: {
            submit: "get_keys_ajax",
            schoolID: school_id
        },
        success: function(response) {
            resolve(response);
        },
        error: function(error) {
            reject(error);
        }
        });
    });
}

$("#paymentFormButton").click(async function(){
    try {
        const payment_key = await getKey();

        if(payment_key.indexOf(" | ") > -1){
            api_key = payment_key.split(" | ")[1]
            school_split_code = payment_key.split(" | ")[0]

            paymentForm.submit();
        }else{
            api_key = ""; school_split_code = "";
            alert_box(payment_key)
        }
    } catch (error) {
        console.error("Error fetching payment key:", error);
    }
})