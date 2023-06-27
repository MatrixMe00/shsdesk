$(document).ready(function(){
    const current_tab = $("#lhs .tab.active").attr("data-current-tab")

    $(".section_btn").click(function(){
        const section_id = $(this).attr("data-section-id");
        $("#lhs .tab.active").attr("data-current-tab",section_id)

        $(this).siblings("button.light").addClass("secondary").removeClass("light")
        $(this).removeClass("secondary").addClass("light")

        $(".btn_section:not(.no_disp)").addClass("no_disp")
        $("#"+section_id).removeClass("no_disp");
    })
    
    $("input[name=search]").keyup(function(){
        const parent = $(this).parents(".btn_section")
        const val = $(this).val()
        const cards = parent.find(".card")
        
        if(val !== ""){
            var displays = 0
            cards.filter(function(){
                const switch_ = $(this).text().toLowerCase().indexOf(val) == -1
                displays = !switch_ ? displays + 1 : displays
                $(this).toggleClass("no_disp", switch_);
            })

            if(displays == 0){
                parent.find(".no-result").removeClass("no_disp")
            }else{
                parent.find(".no-result").addClass("no_disp")
            }
        }else{
            cards.removeClass("no_disp")
        }
    })

    //hide all containers
    $(".btn_section").addClass("no_disp")
    $(".section_btn[data-section-id=" + current_tab + "]").click()
})

//function to change the page from main view to single view and vice versa
function pageChange({index = 0, program_name="", type="", table_id=""}){
    $("#page_title, #classes, #single_class, #cards-section").toggleClass("no_disp")
    $("#cards-section").toggleClass("flex")
    $("#content_wrapper").toggleClass("no_disp")
    $("span#single_class_name").html("")
    // $("#class_list_table tbody").html(insertEmptyTableLabel("Make a search on your class year to proceed", columns))
    
    $("#single_class table:not(.no_disp)").addClass("no_disp")
    $("#single_class table#" + table_id).removeClass("no_disp")

    if(index > 0){
        $("span#single_class_name").html(program_name + " | " + formatItemId(index, "PID"))
    }else{
        $("button[name=reset], label[for=search_table]").addClass("no_disp")
    }
}