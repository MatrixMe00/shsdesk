function getKey() {    
    return new Promise((resolve, reject) => {
      $.ajax({
        url: "./submit.php",
        data: {
          submit: "get_keys_ajax"
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

$(document).ready(function(){
    const cust_amount = $("input#unit_price").val();
    var api_key = ""; var school_split_code = "";

    $("input#price").val("GHC " + parseFloat(cust_amount).toFixed(2))
    
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
            const payment_key = await getKey();
            if(payment_key.indexOf(" | ") > -1){
                api_key = payment_key.split(" | ")[1]
                school_split_code = payment_key.split(" | ")[0]

                payWithPaystack()
            }else{
                api_key = ""; school_split_code = "";
                alert_box(payment_key)
            }
        }
    })

    function payWithPaystack(){
        //0551234987
        let cust_email = $("input[name=email]").val();
        const fullname = $("input[name=lname]").val() + " " + $("input[name=oname]").val()

        if(cust_amount <= 0){
            alert_box("Price cannot be lower than 0. Please select an option", "danger"); return
        }

        try {
            var handler = PaystackPop.setup({
                key: api_key,
                email: cust_email,
                amount: cust_amount * 100,
                currency: "GHS",
                split_code: school_split_code,
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
                        },
                        {
                            display_name: "Index Number",
                            variable_name: "index_number",
                            value: $("input[name=indexNumber]").val()
                        }
                    ]
                },
                callback: function(response){
                    $.ajax({
                        url:"./submit.php",
                        data: $("form[name=payForm]").serialize() + "&transaction_id=" + response.reference + "&submit=" + $("form[name=payForm]").find("button[name=submit]").attr("value"),
                        timeout: 30000,
                        method: "POST",
                        async: false,
                        beforeSend: function(){
                            $("form[name=payForm]").find("button[name=submit]").html("Payment Ongoing...");
                        },
                        success: function(response){
                            $("form[name=payForm]").find("button[name=submit]").html("Make Payment");
                            alert_box("Waiting for page reload...");
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
        } catch (error) {
            eror = error.toString();

            if(eror.indexOf("PaystackPop is not defined") > -1){
                alert_box("You are currently offline. Please check your internet connection and try again later", "danger", 7)
            }else{
                alert_box(eror, "danger")
            }
        }
    }
})