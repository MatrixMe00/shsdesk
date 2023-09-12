<?php
include_once("session.php");
if(isset($_REQUEST["school_id"]) && !empty($_REQUEST["school_id"])){
    $user_school_id = $_REQUEST["school_id"];
    $user_details = getUserDetails($_REQUEST["user_id"]);
}else{
    //set nav_point session
    $_SESSION["nav_point"] = "Transactions";
    }
?>

<section class="section_container" id="transaction_summary">
    <div class="content" style="background-color: #007bff;">
        <div class="head" id="trans_received">
            <h2>
                <?php
                    $res = $connect->query("SELECT transactionID FROM transaction");
                    
                    echo $res->num_rows;
                ?>
            </h2>
        </div>
        <div class="body">
            <span>Transactions Received</span>
        </div>
    </div>

    <div class="content" style="background-color: #20c997">
        <div class="head" id="trans_expired">
            <h2>
                <?php
                    $res = $connect->query("SELECT transactionID FROM transaction WHERE Transaction_Expired = TRUE");
                    
                    echo $res->num_rows;
                ?>
            </h2>
        </div>
        <div class="body">
            <span>Transactions Expired</span>
        </div>
    </div>

    <div class="content" style="background-color: #ffc107">
        <div class="head" id="trans_left">
            <h2>
                <?php
                    $res = $connect->query("SELECT transactionID FROM transaction WHERE Transaction_Expired = FALSE");
                    
                    echo $res->num_rows;
                ?>
            </h2>
        </div>
        <div class="body">
            <span>Transactions left to Expire</span>
        </div>
    </div>

    <div class="content" style="background-color: #dc3545">
        <div class="head">
            <h2>
                <?php
                    $res = $connect->query("SELECT transactionID FROM transaction WHERE LOWER(contactName)='shsdesk'");
                    
                    echo $res->num_rows;
                ?>
            </h2>
        </div>
        <div class="body">
            <span>SHSDesk Transactions</span>
        </div>
    </div>
</section>

<section style="text-align: center;">
    <p>This section is used to add and view transactions currently in the system</p>
    <p>Click any button to continue</p>
</section>

<section>
    <div id="action">
        <div class="head">
            <h2>Transaction Actions</h2>
        </div>
        <div class="body flex flex-wrap">
            <div class="btn">
                <button class="cyan" id="transaction_new_btn">Make New</button>
            </div>
            <div class="btn">
                <button class="teal" id="btn_search">Search a transaction</button>
            </div>
            <div class="btn">
                <button class="red" id="btn_close" style="display: none">Close Container</button>
            </div>
        </div>
    </div>
</section>

<section id="new_transaction" style="display:none;">
    <form action="<?php echo $url?>/admin/superadmin/submit.php" method="post" name="newTransactionForm">
        <div class="head">
            <h3>Add a new Transaction</h3>
        </div>
        <div class="body">
            <div class="message_box success no_disp">
                <span class="message"></span>
                <div class="close"><span>&cross;</span></div>
            </div>
            <div class="joint">
                <label for="trans_id">
                    <input type="text" class="text_input" id="trans_id" name="trans_id" placeholder="Transaction ID"
                    title="Enter the received transaction ID" />
                </label>
                <label for="cont_name">
                    <input type="text" class="text_input tel" id="cont_name" name="cont_name"
                    title="Enter the contact's name [name of the person who bought it]" placeholder="Contact Name" />
                </label>
                <label for="cont_number">
                    <input type="text" class="text_input tel" id="cont_number" name="cont_number"
                    title="Enter the contact's number [number of the person who bought it]" placeholder="Contact number" />
                </label>
                <label for="cont_email">
                    <input type="text" class="text_input" id="cont_email" name="cont_email" placeholder="Contact's email"
                    title="Contact's email address. You can leave it blank if none was provided during transaction" />
                </label>
            </div>
            <label for="school">
                <select name="school" id="school">
                    <option value="">Select the school</option>
                    <?php
                        $sql = "SELECT id,schoolName FROM schools";
                        if($result = $connect->query($sql)){
                            while($row = $result->fetch_assoc()){
                                echo "<option value=\"".$row["id"]."\">".$row["schoolName"]."</option>";
                            }
                        }
                    ?>
                </select>
            </label>
            <input type="hidden" name="amount" value="30">
            <input type="hidden" name="deduction" value="0.59">
        </div>
        <div class="foot">
            <div class="btn">
                <button type="submit" class="primary" name="submit" value="new_transaction">Add Transaction Detail</button>
            </div>
        </div>
    </form>
</section>

<section id="search" style="display: none">
    <form action="<?php echo $url?>/admin/superadmin/submit.php" method="post">
        <div class="head">
            <h3>Search an existing transaction</h3>
        </div>
        <div class="body">
            <div class="search flex flex-wrap">
                <label for="txt_search" style="flex: 1; border: 1px solid grey">
                    <input type="text" name="txt_search" id="txt_search" placeholder="Enter transaction ID...">
                </label>
                <label for="submit" class="btn">
                    <button type="submit" name="submit" value="search_transaction">Search</button>
                </label>
            </div>
            <label for="contact" class="checkbox">
                <input type="checkbox" name="contact" id="contact">
                <span class="span_title">Search By Contact Number</span>
            </label>
        </div>
    </form>
</section>

<section id="results" style="display: none">
    <div class="head">
        <h2>Results</h2>
    </div>
    <div class="body empty flex-column">
        <p>Desired transaction id seems not to exist in the system</p>
        <span>Click <span style="color: blue; cursor:pointer" id="new_trans">Here</span> to create a new transation</span>
    </div>
    <div class="body" id="response">
    </div>
</section>

<script src="<?php echo $url?>/assets/scripts/form/general.min.js?v=<?php echo time()?>" async></script>
<script>
    $("form[name=newTransactionForm]").submit(function(){
        type = "error";
        time = 5;

        if($("#trans_id").val() === ""){
            message = "Please enter a transaction ID";
        }else if($("#trans_id").val().length < 14){
            message = "Transaction id provides is invalid. Check the length";
        }else if($("#trans_id").val()[0] != "T"){
            message = "Valid transaction IDs begin with T.";
        }else if($("#cont_name").val() === ""){
            message = "Please provide the contact's name";
        }else if($("#cont_number").val() === ""){
            message = "Please provide the contact's number";
        }else if($("#cont_number").val().length < 10){
            message = "Please provide a valid phone number";
        }else if($("#school").val() === ""){
            message = "Please select the school which was bought";
        }else{
            dataString = $(this).serialize();
            dataString += "&submit=" + $("form[name=newTransactionForm] button[name=submit]").val();

            $.ajax({
                url: $(this).attr("action"),
                data: dataString,
                dataType: "text",
                beforeSend: function(){
                    message = "Writing Data...";
                    type = "load";
                    time = 0;
                },
                success: function(data){
                    message = data;
                    time = 5;

                    if(data == "success"){
                        type = "success";

                        $("form[name=newTransactionForm] input, form[name=newTransactionForm] select").val("");
                    }else{
                        type = "error";
                    }

                    messageBoxTimeout("newTransactionForm", message, type, time);
                },
                error: function(data){
                    message = data;
                    time = 0;
                }
            })
        }

        messageBoxTimeout("newTransactionForm", message, type, time);
        // alert($(this).serialize());
    })

    $("section#search .search button[name=submit]").click(function(){
        search = $("section#search .search input[name=txt_search]").val();
        contactSearch = $("section#search .body label[for=contact] input").prop("checked");

        $("section#results").show();
        if(search === ""){
            $("#results .empty").hide();
            $("#results #response").show().html("<p style='text-align:center; padding: 0.5em'>Empty string provided</p>");
        }else if(search.length < 3){
            $("#results .empty").hide();
            $("#results #response").show().html("<p style='text-align:center; padding: 0.5em'>Search will start with 3 or more characters</p>");
        }else{
            $.ajax({
                url: "superadmin/submit.php",
                data: "submit=search_transaction&txt_search=" + search + "&searchByContact=" + contactSearch,
                dataType: "html",
                beforeSend: function(){
                    $("#results .empty").hide();
                    $("#results #response").show().html("<p style='text-align:center; padding: 0.5em'>Searching...</p>");
                },
                success: function(data){
                    if(data == "not-found"){
                        $("#results .empty").show();
                        $("#results #response").hide();
                    }else{
                        $("#results .empty").hide();
                        $("#results #response").show().html(data);
                    }
                }
            })
        }
    })

    //function to autorefresh transaction box
    function ajaxCall(name, data){
        $.ajax({
                url: "superadmin/submit.php",
                data: "submit=currentTransactionCount",
                dataType: "json",
                success: function(data){
                    data = JSON.parse(JSON.stringify(data));
                    $("#trans_received h2").html(data["trans_received"]);
                    $("#trans_expired h2").html(data["trans_expired"]);
                    $("#trans_left h2").html(data["trans_left"]);
                },
                error: function(e){
                    alert_box("Network error encountered. Get internet access to continue")
                    clearInterval(this);
                }
            })
    }

    function autoRefresh(){
        setInterval(ajaxCall,5000);
    }

    //start the autorefresh
    autoRefresh();

    //display or hide search or new transaction sections
    $("button#transaction_new_btn, span#new_trans").click(function(){
        $("section#new_transaction").show();
        $("section#search, section#results").hide();
        $("button#btn_close").show();
    })
    $("button#btn_search").click(function(){
        $("section#new_transaction").hide();
        $("section#search").show();
        $("button#btn_close").show();
    })

    $("button#btn_close").click(function(){
        $("#new_transaction, section#search, section#results").hide();
        $(this).hide();
    })
</script>