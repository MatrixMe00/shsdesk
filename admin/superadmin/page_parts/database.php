<?php include_once($_SERVER["DOCUMENT_ROOT"]."/includes/session.php");
    $_SESSION["nav_point"] = "database";
?>
<section>
    <p>This page is intended to allow you access the database using direct queries.</p>
</section>

<section>
    <div class="head">
        <h3>List of Tables and Attributes</h3>
    </div>
    <div class="body">
        <div class="form" style="border-radius: unset; background-color: whitesmoke; margin-bottom: 5px; border: thin solid #aaa">
            <label for="tables">
                <select name="tables" id="tables">
                    <option value="">Select A Table</option>
                    <option value="admins_table">Admins Table [admins_table]</option>
                    <option value="admissiondetails">Admins Details [admissiondetails]</option>
                    <option value="cssps">CSSPS [cssps]</option>
                    <option value="enrol_table">Enrol Table [enrol_table]</option>
                    <option value="exeat">Exeat [exeat]</option>
                    <option value="faq">FAQ [faq]</option>
                    <option value="houses">Houses [houses]</option>
                    <option value="house_allocation">House Allocation [house_allocation]</option>
                    <option value="login_details">Login Details [login_details]</option>
                    <option value="notification">Notification [notification]</option>
                    <option value="pageitemdisplays">Page Item Display [pageitemdisplays]</option>
                    <option value="reply">Reply [reply]</option>
                    <option value="roles">Roles [roles]</option>
                    <option value="schools">Schools [schools]</option>
                    <option value="school_category">School Category [school_category]</option>
                    <option value="transaction">Transaction [transaction]</option>
                </select>
            </label>
        </div>
        <div class="table_heads">
            <div class="body empty">
                <p>No Table Has been Selected</p>
            </div>
            <div id="data_results">
                <table></table>
            </div>
        </div>
    </div>
</section>

<section>
    <form action="<?php echo "$url/admin/superadmin/submit.php"?>" name="databaseQuery">
        <label for="query">
            <textarea name="query" id="query" placeholder="Please enter a query in the space provided below"></textarea>
        </label>
        <label for="submit" class="btn">
            <button type="submit" name="submit" id="submit" class="teal" value="databaseQuery">Query Database</button>
        </label>
    </form>
</section>

<section style="height: fit-content; max-height: initial" id="result_section">
    <div class="head">
        <h3>Query Results</h3>
    </div>
    <div class="body empty">
        <p>No search query has been submitted yet</p>
    </div>
    <div id="results"></div>
</section>

<script>
    //when the table is changed to view its table heads
    $("select#tables").change(function(){
        //display empty if nothing is selected
        if($(this).val() == ''){
            $("#data_results").hide();
            
            $(".table_heads .body.empty p").html("No Table Has been Selected");
            $(".table_heads .body").show();

            //empty the table
            $("#data_results table").html("");
        }else{
            //use ajax to get headers
            $.ajax({
                url: $("form[name=databaseQuery]").attr("action"),
                data: "table_name=" + $(this).val() + "&submit=getHeaders",
                dataType: "html",
                beforeSend: function(){
                    //show a loading indicator
                    $(".table_heads .body.empty p").html("Loading Contents, please wait...");
                    $(".table_heads .body").show();
                },
                success: function(data){
                    //hide loading indicator
                    $(".table_heads .body").hide();
                    
                    if(data.includes("<tr>")){
                        $("#data_results table").html(data);
                        $("#data_results").show();
                    }else{
                        $(".table_heads .body.empty p").html(data);
                        $(".table_heads .body").show(); 
                    }
                },
                error: function(data){
                    $(".table_heads .body.empty p").html(JSON.stringify(data));
                    $(".table_heads .body").show();
                }
            })
        }
    })
    $("form[name=databaseQuery]").submit(function(e){
        e.preventDefault();
        textarea = $("textarea#query").val();

        //check for key statements
        if(textarea.length > 0){
            if(!textarea.toLowerCase().includes("select") && !textarea.toLowerCase().includes("retreive") && 
            !textarea.toLowerCase().includes("delete") && !textarea.toLowerCase().includes("update") && !textarea.toLowerCase().includes("create")){
                $("#result_section .body.empty").html("Please provide an operation name. This could be SELECT, RETRIEVE, DELETE, UPDATE or CREATE")
            }else if(!textarea.toLowerCase().includes("from") && textarea.toLowerCase().includes("select")){
                $("#result_section .body.empty").html("Please provide the FROM clause.");
            }else{
                $.ajax({
                    url: $(this).attr("action"),
                    data: "submit=databaseQuery&query=" + textarea,
                    dataType: "html",
                    beforeSend: function(){
                        $("#results").hide();
                        $("#result_section .body.empty").html("Querying Database, please wait...");
                        $("#result_section .body").show();
                    },
                    success: function(data){
                        $("#result_section .body").hide();

                        if(data.includes("success")){
                            data = data.replace("success","");
                            $("#results").html(data);
                            $("#results").show();
                        }else if(data.includes("<table")){
                            $("#results").html(data);
                            $("#results").show();
                        }else{
                            $("#result_section .body").show().html("Query contains an error. Please check your query and try again");
                            $("#results").html("");
                            $("#results").hide();
                        }
                    }
                })
            }
        }else{
            $("#result_section .body.empty").html("Query Field is empty, no query was submitted");
        }
        
        // $.ajax({
        //     url: $(this).attr("action")
        // })
    })
</script>