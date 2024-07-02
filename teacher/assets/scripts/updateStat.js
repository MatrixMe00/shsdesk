$("form").submit(async function(e){
    e.preventDefault();

    const result = await jsonFormSubmit($(this), $(this).find("button[name=submit]"), false);

    if(result.status == true){
        $("#pMessage").html("Update Successful. Preparing dashboard...").addClass("success").removeClass("light");

        //refresh in 3seconds
        setTimeout(function(){
            $("#pMessage").html("Welcome " + $("#new_username").val());
        },3000);

        setTimeout(function(){
            location.href = location.href;
        },4000);
    }else{
        html = $("#pMessage").html();
        message = "";
        
        $("#pMessage").html(result.message).addClass("danger").removeClass("light");

        setTimeout(function(){
            $("#pMessage").html(html).removeClass("danger").addClass("light");
        },5000);
    }
})