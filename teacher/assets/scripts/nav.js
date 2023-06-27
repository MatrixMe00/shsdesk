//select the active tab upon page refresh
$(document).ready(()=>{
    const activePage = $("input#active-page").val()
    $(".tab[data-active=" + activePage + "]").click()
})

//ham button manipulation
$("#ham").click(function(){
    $("nav").toggleClass("clicked")
})

//tabs manipulation
$(".tab:not(.logout)").click(function(){
    $(".tab.active").removeClass("active primary")
    $(this).addClass("active primary")

    //change page title
    $("title").html($(this).attr("data-document-title"))

    //display page content
    $.ajax({
        url: $(this).attr("data-href"),
        dataType: "html",
        type: "GET",
        timeout: 30000,
        beforeSend: function(){
            $("main").html("Loading page content...")
        },
        success: function(data, textStatus){
            if(data){
                $("main").html(data)
            }else{
                $("main").html("An empty or broken page has been received. Please contact the admin for help")
            }
        },
        error: (xhr, textStatus)=>{
            let message = ""

            if(textStatus === "timeout"){
                message = "Connection was timed out due to a slow network. Please try again later"
            }else{
                message = "Undefined error encountered. Please try again later"
            }

            alert_box(message,"danger",8)
        }
    })
})

$(".tab.logout").click(()=>{
    $.ajax({
        url: "./logout",
        dataType: "html",
        timeout: 30000,
        beforeSend: function(){
            $("main").html("Logging out...")
        },
        success: function(data){
            location.reload()
        },
        error: (xhr, textStatus) => {
            let message = ""

            if(textStatus == "timeout"){
                message = "Connection was timed out due to a slow network. Please try again later"
            }
            alert_box(message,"danger",8)
        }
    })
})

$(".tab").click(function(){
    $("nav.clicked").removeClass("clicked")
})