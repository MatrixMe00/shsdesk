//reference id tracker
reference_parsed = false;
payment_received = false;
transaction_reference = "";

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

    //for testing purposes
    if($("#pay_fullname").val().toLowerCase().includes("shsdesk")){
        mykey = "pk_test_3a5dff723cbd3fe22c4770d9f924d05c77403fca";
    }else{
        mykey = "pk_live_056157b8c9152eb97c1f04b2ed60e7484cd0d955";
    }

    var handler = PaystackPop.setup({
        key: mykey,
        email: cust_email,
        amount: cust_amount,
        currency: "GHS",
        // ref: ,
        // firstname: ,
        // lastname: ,
        // label: ,
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
                }
            ]
        },
        callback: function(response){
            //mark that payment has been received
            payment_received = true;

            //parse data into database
            passPaymentToDatabase(response.reference);
            // $('#admission').removeClass(`no_disp`);

            //store reference into memory
            transaction_reference = response.reference;
        },
        onClose: function(){
            alert_box('Transaction has been canceled by user', "secondary", 10);
        }
    });
    handler.openIframe();
}

function trackTransactions(reference = ""){
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

            $.ajax({
                url: $("form[name=paymentForm]").attr("action"),
                type: "POST",
                dataType: "text",
                data: dataString,
                cache: false,
                async: false,
                success: function(response){
                    if(response.includes("success")){
                        //pass transaction id to admission form
                        $("#ad_transaction_id").val(reference);

                        //signal that reference has been parsed
                        reference_parsed = true;

                        message = "Form could not open automatically. Please enter your transaction reference to open it.";
                        messageBoxTimeout("paymentForm", message, "load", 0);
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
        reference_parsed = trackTransactions(transaction_reference);
    }

    if(reference_parsed){
        //send an info to console
        console.log(transaction_reference + " successfully captured");
        
        //reset references
        reference_parsed = false;
        payment_received = false;
        transaction_reference = "";        

        //stop interval check
        clearInterval(trackKeeper);
    }
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
                alert_box("SMS sent successfully", "success", 8);
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
function passPaymentToDatabase(reference){
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

        $.ajax({
            url: $("form[name=paymentForm]").attr("action"),
            type: "POST",
            dataType: "text",
            data: dataString,
            cache: false,
            async: false,
            success: function(response){
                if(response.includes("success")){
                    //pass transaction id to admission form
                    $("#ad_transaction_id").val(reference);
                    
                    //send an sms
                    sendSMS(reference);

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

                    //check if transaction id is present
                    trackKeeper = setInterval(reCheck, 3000);

                    //display admission form
                    $('#admission').removeClass(`no_disp`);

                    message = "Transaction was made successfully";
                    messageBoxTimeout("paymentForm", message, "success");
                }else if(response == "database_send_error"){
                    form_name = "paymentForm";
                    message = "An error was encountered! Please try again in a short while with your reference id";
                    message_type = "error";
                    time = 10;
                    
                    messageBoxTimeout(form_name, message, message_type, time);
                }else{
                    form_name = "paymentForm";
                    message = "An error was encountered while we tried to process your data. Please try again later.";
                    message_type = "error";
                    time = 10;
                    
                    messageBoxTimeout(form_name, message, message_type, time);
                }
            },
            error: function(){
                message = "Error communicating with server. Check your connection, enter your transaction code and try again";

                //enable only the transaction_id section
                $("#pay_fullname, #pay_email, #pay_phone").prop("disabled", true);

                $("#pay_reference").prop("disabled", false);

                messageBoxTimeout("paymentForm",message, "error");

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
        // passPaymentToDatabase("T1234567890");
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
                //message = "Please wait...";
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