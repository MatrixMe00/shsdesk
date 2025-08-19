$(document).ready(function(){
    $(".section_btn").click(function(){
        $(".section_btn:not(.plain-r)").addClass("plain-r");
        $(this).removeClass("plain-r");

        //display respective block
        const section = $("#" + $(this).attr("data-section"));
        $(".section_block:not(.no_disp)").addClass("no_disp");
        section.removeClass("no_disp");
    })
    $("select[name=program]").change(function(){
        const value = $(this).val();

        if(value == ""){
            $("select[name=course], select[name=class_year]").prop("selectedIndex", 0);
            $("select[name=course] option:not(.first-child), select[name=class_year] option:not(.first-child)").addClass("no_disp");
        }else{
            $("select[name=course] option:not(.first-child)").each((i, e) => {
                const program_id = $(e).attr("data-program-id")
                if(program_id == value){
                    $(e).removeClass("no_disp")
                }
            })

            $("select[name=class_year] option").removeClass("no_disp");
        }
    })

    //concerning the files that will be chosen
    $("input[type=file]").change(function(){
        //get the value of the image name
        const image_path = $(this).val();

        //strip the path name to file name only
        const image_name = image_path.split("C:\\fakepath\\");

        //store the name of the file into the display div
        if(image_path != ""){
            $(this).siblings(".plus").hide();
            $(this).siblings(".display_file_name").html(image_name);       
        }else{
            $(this).siblings(".plus").css("display","initial");
            $(this).siblings(".display_file_name").html("Choose or drag your file here");
        }
    })

    $("form").submit(async function(e){
        const form = $(this);
        const submit_btn = $(this).find("button[name=submit]");
        if($(this).prop("name") == "getDocument"){
            const response = await formSubmit(form, submit_btn, false);
            
            if(response.includes("Error")){
                const message = response.replace("Error: ","");
                alert_box(message, "danger")
                e.preventDefault();
            }else{
                return true;
            }
        }else{
            e.preventDefault();
            const response = await jsonFileUpload(form, submit_btn, false);

            if (response.status) {
                let token = await ajaxCall({
                    url: "./submit.php",
                    formData: { submit: "getToken" },
                    returnType: "json",
                    beforeSend: function () {
                        submit_btn.html("Acquiring Token...").prop("disabled", true);
                    }
                });

                if (token.error) {
                    alert_box("Error acquiring token. Please try again", "danger", 7);
                    submit_btn.html("Upload Results").prop("disabled", false);
                } else {
                    token = token.data;

                    // create the result token head
                    const head_created = await create_result_head(
                        token,
                        response.data.course_id,
                        response.data.program_id,
                        response.data.semester,
                        response.data.exam_year
                    );

                    if (!head_created) {
                        alert_box("Could not create result header", "danger", 7);
                        submit_btn.html("Upload Results").prop("disabled", false);
                        return;
                    }

                    const students = response.data.records;
                    const total_data = students.length;

                    // delete extra fields
                    delete response.data.records;
                    delete response.status;

                    // build payload for bulk submit
                    const payload = {
                        submit: "submit_results",
                        result_token: token,
                        students: JSON.stringify(students),
                        course_id: response.data.course_id,
                        program_id: response.data.program_id,
                        exam_year: response.data.exam_year,
                        semester: response.data.semester,
                        academic_year: response.data.academic_year,
                        assign_positions: 1 // since we are sending all at once, do positions at the end
                    };

                    let progress = 0;
                    const progressInterval = setInterval(() => {
                        if (progress < total_data) {
                            progress++;
                            submit_btn.html(`Uploading... [${progress} of ${total_data}]`);
                        }
                    }, 150);

                    try {
                        const bulkResponse = await $.ajax({
                            url: "./submit.php",
                            type: "POST",
                            data: payload,
                            timeout: 20000
                        });

                        clearInterval(progressInterval);

                        let parsed;
                        try {
                            parsed = typeof bulkResponse === "string" ? JSON.parse(bulkResponse) : bulkResponse;
                        } catch (e) {
                            parsed = { success: false, message: bulkResponse };
                        }

                        if (parsed.success) {
                            if (parsed.failed.length > 0) {
                                alert_box(`Some records failed: ${parsed.failed.join(", ")}`, "warning", 8);
                            } else {
                                alert_box(`All ${total_data} records successfully added`, "success", 5);
                            }
                            submit_btn.html(`Upload Complete (${total_data} records)`);
                        } else {
                            deleteTokenResults(token);
                            alert_box(parsed.message || "Upload failed", "danger", 8);
                            submit_btn.html(`Upload was not completed`).prop("disabled", false);
                        }
                    } catch (error) {
                        clearInterval(progressInterval);
                        deleteTokenResults(token);
                        alert_box(error.statusText || error.toString(), "danger", 8);
                        submit_btn.html("Upload Results").prop("disabled", false);
                    }

                    setTimeout(() => {
                        form[0].reset();
                        form.find("input[type=file]").change();
                        submit_btn.html("Upload Results").prop("disabled", false);
                    }, 3000);
                }
            } else {
                alert_box(response.data, "danger", 5);
            }
        }
        // $(this).unbind("submit").submit()
    })
})