$(document).ready(function(){
    const cust_amount = $("input#unit_price").val();

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