$("form").submit(function(e){
    e.preventDefault();

    const result = jsonFormSubmit($(this), $("form[name=update_new_user] button[name=submit]"), false);
    
    result.then((response)=>{
        if(response["status"] == true){
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
            
            $("#pMessage").html(response["message"]).addClass("danger").removeClass("light");

            setTimeout(function(){
                $("#pMessage").html(html).removeClass("danger").addClass("light");
            },5000);
        }

    }).catch((response)=>{
        alert_box(response.responseText,"red",10)
    })
})