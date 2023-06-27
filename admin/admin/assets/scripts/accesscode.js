function addToList(element){
    var specify_text = $("input#specify").val()
    const text = $(element).html()
    const indexNumber = text.split(" - ")[1]

    if(specify_text.indexOf(",") !== -1){
        var lastText = specify_text.split(", ")
        lastText = lastText[lastText.length-1]

        specify_text = specify_text.replace(lastText,indexNumber)
    }else{
        specify_text = indexNumber
    }

    $("input#specify").val(specify_text)
    $("#student_match").removeClass("flex-all-center").addClass("no_disp").html("")
}

$(document).ready(function(){
    //variables for setting up access price
    const unit_price = 6;
    let school_price = parseFloat($("input#school_price").val())
    const school_max_price = 4;

    //variables for making bulk payment
    let cust_amount = 0;
    let recipient = "";
    let recipient_number = 0;

    //hide all btn_sections
    $(".btn_section").addClass("no_disp")

    //default price allocation for access price set up
    $("input#default_price").val("GHC " + unit_price.toFixed(2))
    $("input#school_price").val("GHC " + school_price.toFixed(2))
    $("input#total_price").val("GHC " + (unit_price + school_price).toFixed(2))
    $("#default_price_text").text("GHC " + unit_price.toFixed(2))

    $("#main_view button").click(function(){
        const section = $(this).attr("data-main-section")
        $("#main_view button").addClass("plain-r")
        $(this).removeClass("plain-r")

        //make toggle on sections
        $("section.btn_section").addClass("no_disp")
        $("section.btn_section." + section).removeClass("no_disp")
    })

    $("input#school_price").on({
        focus: function(){
            $("input#school_price").select()
        },
        blur: function(){
            const price_value = $("input#school_price").val()

            if(/^-?\d*\.?\d+$/.test(price_value)){
                if(parseFloat(price_value) <= school_max_price){
                    school_price = parseFloat(price_value)
                    $("input#school_price").val("GHC " + school_price.toFixed(2))
                    const total_price = unit_price + school_price
                    $("input#total_price").val("GHC " + total_price.toFixed(2))
                }else{
                    const message = "Your maximum profit can not go beyond GHC " + school_max_price.toFixed(2)
                    $("input#school_price").val("GHC 0.00")
                    alert_box(message, "danger")
                }                
            }else{
                alert_box("Only numbers are required in the input field", "danger")
            }
        }
    })
    
    $(".btn-item").click(function(){
        if($(this).hasClass("plain-r")){
            $(".btn-item:not(.plain-r)").addClass("plain-r")
            $(this).removeClass("plain-r")

            //make calculation
            recipient_number = parseInt($(this).attr("data-count"))
            cust_amount = unit_price * recipient_number
            $("input#payable_amount").val("GHC " + cust_amount.toFixed(2))
            $("input#recipient_number").val(recipient_number + (recipient_number != 1 ? " Students" : " Student"))
        }
    })

    $(".specify-btn").click(function(){
        $(".specify").removeClass("no_disp")
        $(".not_specify").addClass("no_disp")
    })

    $(".btn-item:not(.specify-btn)").click(function(){
        $(".specify").addClass("no_disp")
        $(".not_specify").removeClass("no_disp")
        $("input#specify").val("")
        recipient = $(this).attr("data-id")
    })

    $("input#specify").keyup(function(){
        let myword = $(this).val()

        if(myword !== ""){
            //split into comma separated
            myword = myword.split(", ")
            const keyword = myword[myword.length - 1]
            
            $.ajax({
                url: "./admin/submit.php",
                data: {submit: "search_name", keyword: keyword, type: "student"},
                dataType: "json",
                method: "GET",
                timeout: 30000,
                beforeSend: function(){
                    const span = "<p class='txt-al-c'>Searching...</p>"
                    $("#student_match").removeClass("no_disp").addClass("flex-all-center").html(span)
                },
                success: function(response){
                    response = JSON.parse(JSON.stringify(response))

                    if(typeof response["message"] === "object"){
                        $("#student_match").html("")
                        for(var i = 0; i < response["message"].length; i++){
                            const student = response["message"][i]
                            const hide_span = $("input#specify").val().indexOf(student["indexNumber"]) > -1 ? " no_disp" : ""
                            
                            const span = "<p class='w-fit h-light sp-med" + hide_span + "' onclick='addToList($(this))'>" + 
                                            student["Lastname"] + " " + student["Othernames"] + " - " + student["indexNumber"]
                                        "</p>";
                            $("#student_match").append(span)
                        }
                    }else{
                        var span = "";
                        if(response["message"] == "no-result"){
                            span = "<p class='txt-al-c'>No results were found</p>"
                        }else{
                            span = ""
                            $("#student_match").addClass("no_disp").removeClass("flex")
                        }

                        $("#student_match").html(span)
                    }
                },
                error: function(xhr){
                    let message = ""

                    if(xhr.statusText == "timeout"){
                        message = "Connection was timed out due to slow network detected. Please check your internet and try again"
                    }else if(xhr.statusText == "parseerror"){
                        message = "Data receieved from the server is considered invalid type. Please try again"
                    }else{
                        message = xhr.responseText
                    }

                    alert_box(message, "danger", 8)
                }
            })
        }else{
            $("#student_match").removeClass("flex-all-center").addClass("no_disp").html("")
        }
    })

    $("input#specify").blur(function(){
        let keys = $(this).val().split(", ")
        cust_amount = unit_price * keys.length
        $("input#payable_amount").val("GHC " + cust_amount.toFixed(2))
        
        recipient = $(this).val()
        recipient_number = keys.length
        $("input#recipient_number").val(recipient_number + (recipient_number != 1 ? " Students" : " Student"))
    })

    function payWithPaystack(formData){
        if(formData.amount <= 0){
            alert_box("Price cannot be lower than 0. Please select an option", "danger"); return
        }

        //check and be sure that necessary details have been provided
        checkForm(formData, "payForm").then((response) => {
            if(response === true){
                formData.submit = "access_payment"
                var handler = PaystackPop.setup({
                    // key: "pk_live_056157b8c9152eb97c1f04b2ed60e7484cd0d955",
                    key: "pk_test_3a5dff723cbd3fe22c4770d9f924d05c77403fca",
                    email: formData.email,
                    amount: formData.amount * 100,
                    currency: "GHS",
                    // split_code: "SPL_U6mW80wZNH",
                    metadata: {
                        custom_fields: [
                            {
                                display_name: "Mobile Number",
                                variable_name: "mobile_number",
                                value: formData.phone
                            },
                            {
                                display_name: "Customer's Name",
                                variable_name: "customer_name",
                                value: formData.fullname
                            },
                            {
                                display_name: "School Name",
                                variable_name: "school_name",
                                value: formData.school_name
                            }
                        ]
                    },
                    callback: function(response){
                        formData.transaction_id = response.reference
                        $.ajax({
                            url:$("form[name=payForm]").attr("action"),
                            data: formData,
                            timeout: 30000,
                            method: "POST",
                            async: false,
                            beforeSend: function(){
                                $("form[name=payForm]").find("button[name=submit]").html("Payment Ongoing...");
                            },
                            success: function(response){
                                $("form[name=payForm]").find("button[name=submit]").html("Make Payment");
                                response = JSON.parse(JSON.stringify(response))

                                if(response["message"] == "success"){
                                    const total = parseInt(response["success"]) + parseInt(response["fail"])

                                    alert_box("Payment and data storage was successful")
                                    alert_box(response["success"] + " of " + total + " successful", "success", 8)
                                    alert_box(response["fail"] + " of " + total + " failed", "danger", 8)

                                    setTimeout(()=>{
                                        $("#lhs .item.active").click()
                                    }, 1000)
                                }else{
                                    alert_box(response["message"], "danger", 8)                       
                                }

                                if(response["trans_insert"] === false){
                                    alert_box("Transaction details could not be saved to system", "danger", 7)
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
                        alert_box("Transaction has been canceled by user", "danger");
                    }
                });
                handler.openIframe();
            }else{
                alert_box(response, "danger", 7)
            }
        }).catch((error) => {
            alert_box(error,"danger",10)
        })
    }

    function checkForm(formData, formName){
        return new Promise((resolve, reject) => {
            const element = "form[name=" + formName + "]"

            $.ajax({
                url: $(element).attr("action"),
                data: formData,
                success: function(response){
                    if(typeof response === "string"){
                        if(response == "success"){
                            resolve(true)
                        }else{
                            resolve(response)
                        }
                    }
                },
                error: function(xhr){
                    reject(xhr.responseText)
                }
            })
        })
    }

    $("form[name=payForm]").submit(function(e){
        e.preventDefault()

        if($("input#specify").val() !== ""){
            $("input#specify").blur()
        }

        const form = {
            fullname: $(this).find("input[name=fullname]").val(),
            email: $(this).find("input[name=email]").val(),
            phone: $(this).find("input[name=phone_number]").val(),
            amount: cust_amount,
            recipients: recipient,
            school_name: $(this).find("input[name=school_name]").val(),
            submit: $(this).find("button[name=submit]").val(),
            transaction_id: ""
        }

        if(form.fullname == ""){
            alert_box("Please provide your fullname", "danger")
        }else if(form.email == ""){
            alert_box("Please provide your email", "danger")
        }else if(form.phone == ""){
            alert_box("Please provide your phone number", "danger")
        }else if(form.phone.length != 10){
            alert_box("Please provide a valid 10 digit value for your phone number", "danger", 7)
        }else{
            payWithPaystack(form)
        }
    })

    $("form[name=accessPriceForm]").submit(function(e){
        e.preventDefault()
        const response = formSubmit($(this), $(this).find("button[name=submit]", false));
        
        if(response === true){
            alert_box("Update was successful", "success");
            $("#lhs .item.active").click()
        }else{
            alert_box(response, "danger", 7)
        }
    })
})