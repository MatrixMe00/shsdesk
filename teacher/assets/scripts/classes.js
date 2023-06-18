var no_search = "<tr class='empty'><td colspan='5'>Make a search on your class to proceed</td></tr>"
var columns = $("#class_list_table thead td").length

function viewData(element){
    const tr = $(element).parents("tr")
    const index = $(tr).children("td:nth-child(1)").html()
    const name = $(tr).children("td:nth-child(2)").html()
    const gender = $(tr).children("td:nth-child(3)").html()
    const mark = $(tr).children("td:nth-child(4)").html()
    const grade = $(tr).children("td:nth-child(5)").html()

    //fill single student
    $("#single_student .name").html(name)
    $("#single_student .index").html(index)
    $("#single_student .gender").html(gender)
    $("#single_student .mark").html(mark)
    $("#single_student .grade").html(grade)

    $('#single_student').removeClass('no_disp')
}

//select year of the current program
$("#class_year").change(function(){
    $("button[name=list_search]").removeClass("no_disp")
    $("button[name=data_search], button[name=reset]").addClass("no_disp")
})

//button to show the search field
$("button[name=data_search]").click(function(){
    $("label[for=search]").removeClass("no_disp").addClass("flex")
    $("label[for=class_year]").addClass("no_disp")
    $(this).addClass("no_disp")
})

//search for a selected program and program year
$(".form-element button[name=list_search]").click(function(){
    if($("#class_year").val() !== ""){
        $(this).addClass("no_disp")
        $("button[name=data_search], button[name=reset]").removeClass("no_disp")

        const index = $(this).attr("data-pid")
        const c_index = $("select#course_id").val()
        const semester = $("select#exam_semester").val()
        const date_period = $("select#result_year").val()
        const p_year = $("#class_year").val()

        $.ajax({
            url: "./submit.php",
            data: {
                submit: "search_class_list", program_id: index, program_year: p_year,
                course_id: c_index, sem:semester, period: date_period
            },
            timeout: 8000,
            beforeSend: function(){
                tr = insertEmptyTableLabel("Fetching your results, please wait...", columns)
                $("#class_list_table tbody").html(tr)
            },
            success: function(data){
                if(data["status"] == false){
                    const tr = insertEmptyTableLabel(data["message"], columns)
                    $("table#class_list_table tbody").html(tr)
                }else{
                    fillTable({
                        table_id: "class_list_table", result_data: data["message"], first_countable: false,
                        has_mark: true, mark_index: 4, mark_first: true, mark_result_type: $("#mark_result_type").val()
                    })

                    //add a view td
                    $("#class_list_table tbody tr").append("<td><span class=\"item-event view\" onclick=\"viewData($(this))\">View</span></td>")
                }
            },
            error: function(xhr){
                let message = ""
                if(xhr.statusText == "timeout"){
                    message = "Connection was timed out due to a slow network. Please check your internet connection and try again"
                }else{
                    message = xhr.responseText
                }

                alert_box(message, "danger")
            }
        }) 
    }else{
        alert_box("Please select a class year", "danger")
    }        
})

//function to reset the form control
$(".form-element button[name=reset]").click(function(){
    $("#class_year").prop("selectedIndex", 0)
    $("button[name=data_search], button[name=reset]").addClass("no_disp")
    $("button[name=list_search]").removeClass("no_disp")

    $("label[for=search]").addClass("no_disp").removeClass("flex")
    $("label[for=class_year]").removeClass("no_disp")

    $("table#class_list_table tbody").html(insertEmptyTableLabel("Make a search on your class year to proceed", columns))
})

//function to change the page from main view to single view and vice versa
function pageChange(index = 0, program_name=""){
    $("#page_title, #classes, #single_class, #cards-section").toggleClass("no_disp")
    $("#classes, #cards-section").toggleClass("flex")
    $("span#single_class_name").html("")
    $("#class_list_table tbody").html(insertEmptyTableLabel("Make a search on your class year to proceed", columns))
    
    if(index > 0){
        $("span#single_class_name").html(program_name + " | " + formatItemId(index, "PID"))
        $(".form-element button[name=list_search]").attr("data-pid", index)
        $("#single_class select").prop("selectedIndex",0)
        $("select#course_id option").each((index_l, element)=>{
            var pid = parseInt($(element).attr("data-pid"))
            
            if(index === pid){
                $(element).removeClass("no_disp")
            }else{
                $(element).addClass("no_disp")
            }
        })
    }else{
        $("button[name=list_search]").removeClass("no_disp")
        $("button[name=data_search], button[name=reset]").addClass("no_disp")
    }
}

//search key control
$("#search").keyup(function(){
    const searchText = $(this).val().toLowerCase()
    $("table#class_list_table tbody tr").each(function(){
        const rowData = $(this).text().toLowerCase()
        if(rowData.indexOf(searchText) === -1){
            $(this).hide()
        }else{
            $(this).show()
        }
    })
})

$(".form-element select").change(function(){
    $("button[name=list_search]").removeClass("no_disp")
    $("button[name=data_search]").addClass("no_disp")
})