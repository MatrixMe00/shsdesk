function payWithPaystack(){
    //0551234987
    cust_amount = $("#pay_amount").val().split(" ");
    cust_amount = parseInt(cust_amount[1]) * 100;
    var handler =PaystackPop.setup({
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
            // passPaymentToDatabase(response.reference);
            $('#admission').removeClass(`no_disp`);
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

    $.ajax({
        url: $("form[name=paymentForm]").attr("action"),
        type: "POST",
        dataType: "html",
        data: dataString,
        cache: false,
        success: function(response){
            if(response == "success"){
                $('#admission').removeClass(`no_disp`);
            }else if(response == "database_send_error"){
                $("form[name=paymentForm] #message_box").removeClass("success load no_disp").addClass("error");
                $("form[name=paymentForm] #message_box .message").html("An error was encountered! Please try again in a short while with your reference id");

                setTimeout(function(){
                    $("form[name=paymentForm] #message_box").removeClass("success load error").addClass("no_disp");
                    $("form[name=paymentForm] #message_box .message").html("");
                },10000);
            }else{
                $("form[name=paymentForm] #message_box").removeClass("success load no_disp").addClass("error");
                $("form[name=paymentForm] #message_box .message").html("An error encountered while we tried to process your data.");

                setTimeout(function(){
                    $("form[name=paymentForm] #message_box").removeClass("success load error").addClass("no_disp");
                    $("form[name=paymentForm] #message_box .message").html("");
                },10000);
            }
        },
        error: function(){
            alert("Error communicating with server. Check your connection, enter your transaction code and try again");
            exit(1);
        }
    })
}

function getSchoolID(school_name = ""){
    id = 0;

    dataString = "submit=search_school_id&school_name=" + school_name;

    $.ajax({
        url: $("form[name=paymentForm").attr("action"),
        data: dataString,
        type: "POST",
        dataType: "html",
        cache: false,
        success: function(response){
            if(pareseInt(response)){
                id = parseInt(response);
            }else{
                alert("An unexpected error occured. Enter your reference id and try again");
                exit(1);
            }
        }
    })

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
        payWithPaystack();
    }else{
        //disable other input elements
        $("#pay_fullname, #pay_email, #pay_phone").prop("disabled", true).val("");

        ref = $("section#trans #pay_reference").val();
        dataString = "reference_id=" + ref + "&submit=checkReference";

        $.ajax({
            url: $("form[name=paymentForm]").attr("action"),
            data: dataString,
            type: "POST",
            dataType: "html",
            cache: false,
            beforeSend: function(){
                message = "Please wait...";
                messageType = "load";
                time = 0;
                messageBoxTimeout(form_name, message, messageType, time);
            },
            success: function(response){
                if(response == "success"){
                    $('#admission').removeClass(`no_disp`);
                }else{
                    if(response == "ref_expired"){
                        message = "Sorry! This reference id has expired. Please make a new payment to continue";
                        
                        time = 5;
                    }else{
                        message = "Data could not be processed. Please try again later";
                        time = 5;
                    }

                    messageType = "error";
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

// $("#pay_email").blur(function(){
//     window.clearTimeout(null);
//     messageBoxTimeout("paymentForm","My Message", "load", 5);
// })