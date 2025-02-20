$(document).ready(function(){
    $(".system_setting").change(function(){
        const element = $(this);
        const key = element.attr("name");
        const value = element.val();

        ajaxCall({
            url: "admin/submit.php",
            formData: {submit: "modify_settings", key: key, value: value},
            returnType: "json",
            method: "POST",
            beforeSend: function(){
                element.prop("disabled", true);
            }
        }).then((response) => {
            element.prop("disabled", false);
            if(response.status){
                if(element.attr("data-need-refresh") == "1"){
                    alert_box("Settings applied. Refresh page for extra settings");
                }else{
                    alert_box("Settings have been applied");
                }
            }else{
                alert_box(response.message, "danger");
            }
        })
    })

    $("#add_program_promote").click(function(){
        const template = $("#program_promote_template").html();
        $("#promotion_classes").append(template);
    })

    $(document).off("click", ".promotion-remove:not(.info)").on("click", ".promotion-remove:not(.info)", function(){
        const element = $(this);
        const parent = element.parents("tr");

        if(parseInt(parent.attr("data-id")) > 0){
            const sibling = element.siblings(".promotion-save").first();

            ajaxCall({
                url: "admin/submit.php",
                formData: {submit: "remove_promotion_row", id: parent.attr("data-id")},
                method: "POST",
                returnType: "json",
                beforeSend: function(){
                    element.addClass("info").html("Removing..."); sibling.addClass("no_disp");
                }
            }).then(() => {
                element.removeClass("info").html("Remove");
                sibling.removeClass("no_disp");
                parent.remove();

                // hide the fix anomalies button
                if($("#promotion_classes tr").length < 1){
                    $("#fix_anomalies").addClass("no_disp");
                }
            })
        }else{
            parent.remove();
        }
    })

    $(document).off("click", ".promotion-save:not(.info)").on("click", ".promotion-save:not(.info)", function(){
        const element = $(this);
        const parent = element.parents("tr").first();
        const year1 = parent.find("select[name=year1]").val();
        const year2 = parent.find("select[name=year2]").val();
        const year3 = parent.find("select[name=year3]").val();
        const sibling = element.siblings(".promotion-remove").first();

        ajaxCall({
            url: "admin/submit.php",
            formData: {submit: "save_promotion_row", id: parent.attr("data-id"), year1: year1, year2: year2, year3: year3},
            method: "POST",
            returnType: "json",
            beforeSend: function(){
                sibling.addClass("no_disp"); element.addClass("info").html("Saving...");
            }
        }).then((response) => {
            element.removeClass('info').html("Save");
            sibling.removeClass("no_disp");
            
            alert_box(response.message, response.status ? "primary" : "danger");

            // store the new id into the parent table
            if(parent.attr("data-id") == "0" && response.status){
                parent.attr("data-id", response.id);

                $("#fix_anomalies").removeClass("no_disp");
            }
        })
    })

    $(document).off("click", "#fix_anomalies").on("click", "#fix_anomalies", function(){
        const element = $(this);
        const can_fix = confirm("This will assign students with their respective classes if they have already been promoted");
        
        if(can_fix){
            ajaxCall({
                url: "admin/submit.php",
                formData: {submit: "fix_anomalies"},
                method: "POST",
                returnType: "json",
                beforeSend: function(){
                    element.prop("disabled", true).text("Fixing...");
                }
            }).then(response => {
                element.prop("disabled", false).text("Fix Anomalies");
                alert_box(response.message);
            })
        }        
    })
})