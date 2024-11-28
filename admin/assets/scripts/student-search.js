$(document).ready(function(){
    function fillForm(data){
        $("#indexNumber").val(data.indexNumber);
        $("#lname").val(data.Lastname);
        $("#oname").val(data.Othernames);
        $("#gender").val(data.Gender);
        $("#school").val(data.schoolName);
        $("#school_id").val(data.schoolID);
        $("#program").val(data.programme);
        $("#primary_phone").val(data.primaryPhone);
        $("#secondary_phone").val(data.secondaryPhone);
        $("#student_house").val(data.house_name);
        $("#enrol_code").val(data.enrolCode);
        $("#boarding_status").val(data.boardingStatus);
        $("#witness_name").val(data.witnessName);
        $("#witness_phone").val(data.witnessPhone);
        $("#admission_year").val(data.academic_year);

        // index number at top
        const is_enroled = data.enrolCode.toLowerCase() != "not set" ?
            "Enroled" : "Not Enroled";
        
        $("span#index_number").html(data.indexNumber + " [" + is_enroled + "]");

        // enable or disable activate button
        $("#can_activate").prop("disabled", !data.can_activate);
    }

    $("form.student_search").submit(async function(e){
        e.preventDefault();
        const form_name = $(this).attr("name");
        let message = null;
        let response = null;
        let type = "danger";
        let time = 5;

        if(form_name == "search-student"){
            const isCode = $("input#enrolCode").prop("checked") ? 1 : 0;
            const isCurrent = $("input#enrolCode").prop("checked") ? 1 : 0;
            
            await $.ajax({
                url: $(this).attr("action"),
                data: {
                    submit: $(this).find("button[name=submit]").val(),
                    enrolCode: isCode, current: isCurrent,
                    search: $("#txt_search").val()
                },
                dataType: "json",
                beforeSend: function(){
                    $("section#form").addClass("no_disp");
                    $("section#loader").removeClass("no_disp").find("#message").html("Searching Student...");

                    //reset fillable form
                    $("form[name=update-student]")[0].reset();
                },
                success: function(data){
                    if(data.error){
                        $("section#loader").find("#message").html(data.data);
                    }else{
                        $("section#loader").addClass("no_disp").find("#message").html("");

                        // fill form
                        fillForm(data.data);

                        $("section#form").removeClass("no_disp");
                    }                        
                },
                error: function(xhr, textStatus, errorThrown){
                    $("section#loader").removeClass("no_disp").find("#message").html(errorThrown);
                    console.log(xhr);
                }
            })
        }else{
            message = formSubmit($(this), $(this).find("button[name=submit]"));
        }
    })

    $("#can_activate").click(function(){
        const proceed = confirm("Are you sure you want to allow this student for this year's admission?");

        if(proceed){
            const button = $(this);
            const index_number = $("#indexNumber").val();
            const admission_year = $("#admission_year").val();
            ajaxCall({
                url: "submit.php",
                formData: {submit: "activate_student_admission", index_number: index_number, academic_year: admission_year},
                method: "POST",
                returnType: "json",
                beforeSend: function(){
                    button.prop("disabled", true).html("Processing...");
                }
            }).then((response) => {
                button.html("Activate").prop("disabled", response.message == "success");

                if(response){
                    if(response.message == "success"){
                        $("#admission_year").val(response.admission_year);
                        alert_box(index_number + " has been activated for " + response.admission_year + " admission year", "success", 6);
                    }else{
                        alert_box(response.message, "danger");
                    }
                }                
            })
        }
    })

    $("button#search_result_close").click(function(){
        $("span#index_number").html("");
        $("#form").addClass("no_disp");
    })
})