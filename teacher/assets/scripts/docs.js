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
            
            if(response.status){
                let token = await ajaxCall({
                    url: "./submit.php", formData: {submit: "getToken"}, returnType: "json", beforeSend: function(){
                        submit_btn.html("Acquiring Token...").prop("disabled", true);
                    }
                });

                if(token.error){
                    alert_box("Error acquiring token. Please try again", "danger", 7);
                    submit_btn.html("Upload Results").prop("disabled", false);
                }else{
                    token = token.data;
                    
                    // delete records and status so that they can be easily 
                    // passed into the individual records
                    delete response.data.records;
                    delete response.status;

                    // create the result token head
                    const head_created = await create_result_head(token, response.data.course_id, response.data.program_id, response.data.semester, response.data.exam_year);
                    let complete = true;

                    if (head_created) {
                        const total_data = data.length;
                        const data = response.data.records;
                        const submit_value = "submit_result";
                
                        for (let i = 0; i < total_data; i++) {
                            const record = data[i];
                            const isFinal = i + 1 === total_data;
                            const assign_positions = isFinal ? 1 : 0;
                            const data_ = { 
                                ...record, ...response.data, 
                                isFinal: isFinal, submit: submit_value, 
                                result_token: token, assign_positions: assign_positions 
                            };
                                            
                            // upload resource
                            const response_ = await ajaxCall({
                                url: "./submit.php",
                                formData: data_,
                                method: "POST",
                                beforeSend: function(){
                                    submit_btn.html(`Uploading ${record.indexNumber} [${i + 1} of ${total_data}]`);
                                }
                            })

                            if(response_ != "true"){
                                alert_box(response_ == "false" ? record.indexNumber + " could not be saved for unknown reason" : response_);
                                complete = false;

                                // remove the token and its results
                                deleteTokenResults(token);
                                break;
                            }

                            // mimic a delay
                            await delay(300);
                        }
                        
                        if(complete){
                            submit_btn.html(`All ${total_data} records successfully added`);
                        }else{
                            submit_btn.html(`Upload was not completed`);
                        }

                        setTimeout(() => {
                            submit_btn.html("Upload Results").prop("disabled", false);
                        }, 3000);
                        
                    }
                }
            }else{
                alert_box(response.data, "danger", 5);
            }
        }
        // $(this).unbind("submit").submit()
    })
})