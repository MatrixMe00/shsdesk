function payWithPaystack(){
    //0551234987
    cust_amount = $("#pay_amount").val().split(" ");
    cust_amount = parseInt(cust_amount[1]) * 100;
    var handler = PaystackPop.setup({
        key: 'pk_test_3a5dff723cbd3fe22c4770d9f924d05c77403fca',
        email: $("form[name=paymentForm] .body #pay_email").val(),
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
            alert('Payment was successful. Your transaction reference is ' + response.reference);

            //parse data into database
            passPaymentToDatabase(response.reference);
            // $('#admission').removeClass(`no_disp`);
        },
        onClose: function(){
            alert('Transaction has been canceled by user');
        }
    });
    handler.openIframe();
}

//function to pass data into database
function passPaymentToDatabase(reference){
    fullname = $("#pay_fullname").val();
    email = $("#pay_email").val();
    phone = $("#pay_phone").val();

    amount = $("#pay_amount").val().split(" ");
    amount = amount[1];

    deduction = parseFloat(((1.95/100) * parseInt(amount)).toFixed(2));

    
    //get the selected school name
    school_name = $("#school_admission_case #school_select option:selected").html();

    school_id = getSchoolID(school_name);

    dataString = "transaction_id=" + reference + "&contact_number=" + phone + "&school=" + school_id + "&amount=" + amount + "&deduction=" + deduction + "&contact_email=" + email + "&contact_name=" + fullname + "&submit=add_payment_data";

    alert(dataString);

    $.ajax({
        url: $("form[name=paymentForm]").attr("action"),
        type: "POST",
        dataType: "html",
        data: dataString,
        cache: false,
        async: false,
        success: function(response){
            if(response == "success"){
                //pass transaction id to admission form
                $("#ad_transaction_id").val(reference);

                //display admission form
                $('#admission').removeClass(`no_disp`);
            }else if(response == "database_send_error"){
                form_name = "form[name=paymentForm]";
                message = "An error was encountered! Please try again in a short while with your reference id";
                message_type = "error";
                time = 10;
                
                messageBoxTimeout(form_name, message, message_type, time);
            }else{
                form_name = "form[name=paymentForm]";
                message = "An error was encountered while we tried to process your data. Please try again later.";
                message_type = "error";
                time = 10;
                
                messageBoxTimeout(form_name, message, message_type, time);
            }
        },
        error: function(){
            alert("Error communicating with server. Check your connection, enter your transaction code and try again");

            //enable only the transaction_id section
            $("#pay_fullname, #pay_email, #pay_phone").prop("disabled", true);

            $("#pay_reference").prop("disabled", false);

            exit(1);
        }
    })
}

function getSchoolID(school_name){
    id = 0;

    while(school_name.includes(" ")){
        school_name = school_name.replace(" ","+");
    }

    dataString = "submit=search_school_id&school_name=" + school_name;

    $.ajax({
        url: $("form[name=paymentForm").attr("action"),
        data: dataString,
        type: "POST",
        dataType: "text",
        cache: true,
        async: false,
        success: function(response){
            if(parseInt(response) > 0){
                id = parseInt(response);
            }else{
                alert("An unexpected error occured. Enter your reference id and try again");
                exit(1);
            }
        },
        error: function(){
            alert("Unable to connect to server");
        }
    });

    return id;
}

//what should happen when the payment form is submitted
$("form[name=paymentForm]").submit(function(){
    form_name = $(this).attr("name");

    //check if only the reference was given else, make payment to system
    if($("section#trans #pay_reference").val() == ""){
        //disable reference element
        $("#pay_reference").prop("disabled", true);
        $("#pay_fullname, #pay_email, #pay_phone").prop("disabled", false);

        //go to paystack payment method
        // payWithPaystack();
        passPaymentToDatabase("T1234567890");
    }else{
        //disable other input elements
        $("#pay_fullname, #pay_email, #pay_phone").prop("disabled", true).val("");

        ref = $("section#trans #pay_reference").val();
        dataString = "reference_id=" + ref + "&submit=checkReference";

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
                if(response == "success"){
                    //pass transaction id to admission form
                    $("#ad_transaction_id").val(ref);

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
                    }else{
                        message = "Reference ID entered is incorrect. Please enter a valid id to continue";
                        messageType = "error";
                        time = 5;
                    }

                    messageBoxTimeout(form_name, message, messageType, time);
                }
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