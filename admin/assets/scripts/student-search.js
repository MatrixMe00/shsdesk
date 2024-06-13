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

        // index number at top
        const is_enroled = data.enrolCode.toLowerCase() != "not set" ?
            "Enroled" : "Not Enroled";
        
        $("span#index_number").html(data.indexNumber + " [" + is_enroled + "]");
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

    $("button#search_result_close").click(function(){
        $("span#index_number").html("");
        $("#form").addClass("no_disp");
    })
})