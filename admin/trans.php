<?php include_once("../includes/session.php");

    $_SESSION["nav_point"] = "Payments";
?>
<head>
    <style>
        table{
            width: 98%;
            border: 1px solid lightgrey;
            box-shadow: 0 0 0 1px grey;
            transition: box-shadow 0.5s;
            margin: 1%;
            margin-top: 2.5%;
            background-color: white;
            max-width: 98vw;
            overflow: auto;
        }
        table:first-child{
            margin-top: 1%;
        }
        thead td{cursor: pointer}
        table:hover{
            box-shadow: 0 0 4px 0 grey;
        }
        tfoot{
            text-align:center;
        }
        td{
            width:fit-content;
            padding: 0.3em;
            background-color: transparent;
        }
        tbody tr{
            border: 1px solid lightgrey;
            cursor: default;
            background-color: transparent !important;
        }
        table tbody tr:hover{
            background-color: rgb(90%,90%,90%) !important;
        }
        tbody tr.pending{
            background-color: lightyellow !important;
        }
        tbody tr.pending:hover{
            background-color: rgb(255,255,200) !important;
        }
        tbody tr.pending.highlight{
            background-color: rgb(255, 255, 150) !important;
            color: black;
        }
        tbody tr.highlight{
            background-color: lightseagreen !important;
            color: ghostwhite;
        }
        .form{
            margin: 1%;
        }

        #table_view table{
            max-height: 2vh;
        }

        span.item-event.info{
            cursor: pointer;
        }

        span.item-event.info:hover{
            text-decoration: underline;
        }

        #tables{
            overflow: auto;
        }

        @media screen and (min-width: 768px) {
            #table_view, #data_details{
                max-width: 70vw;
            }

            #tables{
                padding-left: 15vw;
            }
        }

        @media screen and(max-height: 480px){
            #tables{
                align-items: stretch;
                justify-content: stretch;
            }
            #table_view{
                display: none;
            }
        }
    </style>
</head>

<section>
    <p>This section is used to display all payments done through the system to admins and schools</p>
</section>

<section>
    <div class="head">
        <h3>Controls</h3>
    </div>
    <div class="body flex">
        <div class="btn">
            <button id="auto_gen" class="teal"><?php
                if($user_details["role"] != 1){
                    echo "Get Latest Results";
                }else{
                    echo "Update";
                }
            ?></button>
        </div>
        <div id="stat" class="no_disp" style="align-self: center">
            <span></span>
        </div>
    </div>
</section>

<?php if(isset($_SESSION["user_login_id"]) && $user_details["role"] <= 2){?>
<section>
    <div class="head">
        <h3>Super Admins</h3>
    </div><?php
        $sql = "SELECT * FROM payment WHERE user_role <= 2";
        $result = $connect->query($sql);

        if($result->num_rows > 0){
            $count = 1;
    ?>
    <div class="body">
        <table id="table_1">
            <thead>
                <tr>
                    <td>Transaction Reference</td>
                    <td>Sent To</td>
                    <td>Contact Number</td>
                    <td>User Role</td>
                    <td>Students Cleared</td>
                    <td>Channel</td>
                    <td>Amount</td>
                    <td>Date</td>
                    <td>Status</td>
                </tr>
            </thead>
            <tbody><?php 
                while($row = $result->fetch_assoc()){
            ?>
                <tr data-row-count="<?php echo $count?>"<?php
                    if($row["status"] == "Pending"){
                        echo " class=\"pending\"";
                    }
                ?> data-row-id="<?php echo $row["id"]?>">
                    <td class="td_transaction"><?php
                        if(empty($row["transactionReference"])){
                            echo "Not set";
                        }else{
                            echo $row["transactionReference"];
                        }
                    ?></td>
                    <td class="td_name"><?php 
                        if ($row["contactName"] == "-"){
                            echo "Not set";
                        }else{
                            echo $row["contactName"];
                        }
                    ?></td>
                    <td class="td_number"><?php 
                        if(empty($row["contactNumber"])){
                            echo "Not set";
                        }else{
                            echo $row["contactNumber"];
                        }
                    ?></td>
                    <td class="td_role"><?php echo formatName(getRole($row["user_role"])); ?></td>
                    <td class="td_student"><?php echo number_format($row["studentNumber"])?></td>
                    <td class="td_channel"><?php
                        if(empty($row["method"])){
                                echo "Not set";
                            }else{
                                echo $row["method"];
                            }
                    ?></td>
                    <td class="td_amount"><?php echo number_format(($row["amount"] - $row["deduction"]),2)?></td>
                    <td class="td_deduction no_disp"><?php echo $row["deduction"]?></td>
                    <td class="td_date"><?php
                        if(empty($row["date"])){
                            echo "Not set";
                        }else{
                            echo date("M d, Y",strtotime($row["date"]));
                        }
                    ?></td>
                    <td><?php echo $row["status"]?></td>
                </tr><?php
                    $count++;
                }
                ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="8">End of Data Table</td>
                </tr>
            </tfoot>
        </table>
    </div>
    <?php }else{?>
    <div class="body empty">
        <p>No results to display.</p>
    </div>
    <?php } ?>
</section>
<?php }?>

<?php if(isset($_SESSION["user_login_id"]) && ($user_details["role"] <= 2 || str_contains(strtolower(getRole($user_details["role"])), "admin"))){?>
<section>
    <div class="head">
        <h3>Admin<?php if($user_details["role"] <= 2){echo "s"; }?></h3>
    </div>
    <?php
        if($user_school_id > 0){
            $sql = "SELECT * FROM payment WHERE user_role = ".$user_details["role"]." AND school_id= $user_school_id";
        }else{
            $admins = fetchData("id","roles","id > 2 AND title LIKE 'admin%'", 0);
            $sql = "SELECT * FROM payment";
            
            if(is_array($admins)){
                $sql .= " WHERE ";
                foreach($admins as $admin){
                    $sql .= " user_role=".$admin["id"];

                    if(end($admins) != $admin){
                        $sql .= " OR ";
                    }
                }
            }
        }
        $result = $connect->query($sql);

        if($result->num_rows > 0){
            $count = 1;
    ?>
    <div class="body">
        <table id="table_2">
            <thead>
                <tr>
                    <td>Transaction Reference</td>
                    <td>Sent To</td>
                    <td>Contact Number</td>
                    <?php if($user_details["role"] <= 2){ ?>
                    <td>School</td>
                    <?php }?>
                    <td>Students Cleared</td>
                    <td>Channel</td>
                    <td>Amount</td>
                    <td>Date</td>
                    <td>Status</td>
                </tr>
            </thead>
            <tbody><?php 
                while($row = $result->fetch_assoc()){
            ?>
                <tr data-row-count="<?php echo $count?>"<?php
                    if($row["status"] == "Pending"){
                        echo " class=\"pending\"";
                    }
                ?> data-row-id="<?php echo $row["id"]?>">
                    <td class="td_transaction"><?php
                        if(empty($row["transactionReference"])){
                            echo "Not set";
                        }else{
                            echo $row["transactionReference"];
                        }
                    ?></td>
                    <td class="td_name"><?php 
                        if ($row["contactName"] == "-"){
                            echo "Not set";
                        }else{
                            echo $row["contactName"];
                        }
                    ?></td>
                    <td class="td_number"><?php 
                        if(empty($row["contactNumber"])){
                            echo "Not set";
                        }else{
                            echo $row["contactNumber"];
                        }
                    ?></td>
                    <?php if($user_details["role"] <= 2) {?>
                    <td title="<?php echo getSchoolDetail($row["school_id"])["schoolName"] ?>" class="td_school"><?php echo getSchoolDetail($row["school_id"], true)["abbr"]?></td>
                    <?php }?>
                    <td class="td_student"><?php echo $row["studentNumber"]?></td>
                    <td class="td_channel"><?php
                        if(empty($row["method"])){
                            echo "Not set";
                        }else{
                            echo $row["method"];
                        }
                    ?></td>
                    <td class="td_amount"><?php echo number_format(($row["amount"] - $row["deduction"]),2)?></td>
                    <td class="td_deduction no_disp"><?php echo $row["deduction"]?></td>
                    <td class="td_date"><?php
                        if(empty($row["date"])){
                            echo "Not set";
                        }else{
                            echo date("M d, Y",strtotime($row["date"]));
                        }
                    ?></td>
                    <td><?php echo $row["status"]?></td>
                </tr><?php
                    $count++;
                }
                ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="8">End of Data Table</td>
                </tr>
            </tfoot>
        </table>
    </div>
    <?php }else{?>
    <div class="body empty">
        <p>No results to display.</p>
    </div>
    <?php } ?>
</section>
<?php } ?>

<section>
    <div class="head">
        <h3>School Head<?php if($user_details["role"] <= 2){echo "s"; }?></h3>
    </div>
    <?php
        if($user_school_id > 0){
            if(str_contains(strtolower(getRole($user_details["role"])), "admin")){
                $sql = "SELECT * FROM payment WHERE user_role = ".(intval($user_details["role"])+1)." AND school_id= $user_school_id";
            }else{
                $sql = "SELECT * FROM payment WHERE user_role = ".$user_details["role"]." AND school_id= $user_school_id";
            }
        }else{
            $admins = fetchData("id","roles","id > 2 AND title LIKE 'school head%'", 0);
            $sql = "SELECT * FROM payment";
            
            if(is_array($admins)){
                $sql .= " WHERE ";
                foreach($admins as $admin){
                    $sql .= " user_role=".$admin["id"];

                    if(end($admins) != $admin){
                        $sql .= " OR ";
                    }
                }
            }
        }
        $result = $connect->query($sql);

        if($result->num_rows > 0){
            $count = 1;
    ?>
    <div class="body">
        <table id="table_3">
            <thead>
                <tr>
                    <td>Transaction Reference</td>
                    <td>Sent To</td>
                    <td>Contact Number</td>
                    <?php if($user_details["role"] <= 2){ ?>
                    <td>School</td>
                    <?php }?>
                    <td>Students Cleared</td>
                    <td>Channel</td>
                    <td>Amount</td>
                    <td>Date</td>
                    <td>Status</td>
                </tr>
            </thead>
            <tbody><?php 
                while($row = $result->fetch_assoc()){
            ?>
                <tr data-row-count="<?php echo $count?>"<?php
                    if($row["status"] == "Pending"){
                        echo " class=\"pending\"";
                    }
                ?> data-row-id="<?php echo $row["id"]?>">
                    <td class="td_transaction"><?php
                        if(empty($row["transactionReference"])){
                            echo "Not set";
                        }else{
                            echo $row["transactionReference"];
                        }
                    ?></td>
                    <td class="td_name"><?php 
                        if ($row["contactName"] == "-"){
                            echo "Not set";
                        }else{
                            echo $row["contactName"];
                        }
                    ?></td>
                    <td class="td_number"><?php 
                        if(empty($row["contactNumber"])){
                            echo "Not set";
                        }else{
                            echo $row["contactNumber"];
                        }
                    ?></td>
                    <?php if($user_details["role"] <= 2) {?>
                    <td title="<?php echo getSchoolDetail($row["school_id"])["schoolName"] ?>" class="td_school"><?php echo getSchoolDetail($row["school_id"], true)["abbr"]?></td>
                    <?php }?>
                    <td class="td_student"><?php echo $row["studentNumber"]?></td>
                    <td class="td_channel"><?php
                        if(empty($row["method"])){
                            echo "Not set";
                        }else{
                            echo $row["method"];
                        }
                    ?></td>
                    <td class="td_amount"><?php echo number_format(($row["amount"] - $row["deduction"]),2)?></td>
                    <td class="td_deduction no_disp"><?php echo $row["deduction"]?></td>
                    <td class="td_date"><?php
                        if(empty($row["date"])){
                            echo "Not set";
                        }else{
                            echo date("M d, Y",strtotime($row["date"]));
                        }
                    ?></td>
                    <td><?php echo $row["status"]?></td>
                </tr><?php
                    $count++;
                }
                ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="8">End of Data Table</td>
                </tr>
            </tfoot>
        </table>
    </div>
    <?php }else{?>
    <div class="body empty">
        <p>No results to display.</p>
    </div>
    <?php } ?>
</section>

    <div class="form_modal_box fixed no_disp flex flex-column flex-center-content" id="tables">
        <div id="table_view">
            <table>
                <thead></thead>
                <tbody></tbody>
            </table>
        </div>
        <div id="data_details" class="form">
            <div class="head">
                <h3>Transaction For <span id="send_name"></span></h3>
            </div>
            <div class="body">
                <div class="joint">
                    <?php if($user_details["role"] == 1){?>
                    <label for="send_name" class="flex-wrap flex-column">
                        <span class="label_title">Send Name</span>
                        <input type="text" name="send_name" id="send_name">
                    </label>
                    <label for="send_phone" class="flex-wrap flex-column">
                        <span class="label_title">Transaction Phone Number</span>
                        <input type="text" name="send_phone" id="send_phone">
                    </label>
                    <label for="student_count" class="flex-wrap flex-column">
                        <span class="label_title">Students Cleared</span>
                        <input type="text" name="student_count" id="student_count">
                    </label><?php } ?>
                </div>
                <div class="joint">
                    <label for="trans_ref" class="flex-wrap flex-column">
                        <span class="label_title">Transaction Reference</span>
                        <input type="text" name="trans_ref" id="trans_ref" style="width:100%" <?php 
                            if($user_details["role"] != 1){
                                echo "disabled";
                            }
                        ?>>
                    </label>
                    <label for="channel" class="flex-wrap flex-column">
                        <span class="label_title">Channel</span>
                        <input type="text" name="channel" id="channel" style="width:100%" disabled>
                        <?php if($user_details["role"] == 1){ ?>
                        <span class="item-event info" id="change_channel">Update Channel</span>
                        <?php } ?>
                    </label>
                    <label for="update_channel" class="no_disp flex-wrap flex-column">
                        <span class="label_title">Update Channel</span>
                        <select name="update_channel" id="update_channel">
                            <option value="">Select Channel</option>
                            <option value="Mobile Money">Mobile Money</option>
                            <option value="Bank">Bank</option>
                        </select>
                    </label>
                </div>
                <div class="joint">
                    <label for="amount" class="flex-wrap flex-column">
                        <span class="label_title">Amount Sent</span>
                        <input type="text" name="amount" id="amount" style="width:100%" <?php 
                            if($user_details["role"] != 1){
                                echo "disabled";
                            }
                        ?>>
                        <span class="item-event date">Amount above is the full amount sent, this is without the deduction</span>
                    </label>
                    <label for="deduction" class="flex-wrap flex-column">
                        <span class="label_title">Deduction for Transaction</span>
                        <input type="text" name="deduction" id="deduction" style="width:100%" <?php 
                            if($user_details["role"] != 1){
                                echo "disabled";
                            }
                        ?>>
                        <span class="item-event date">Amount above is the amount deducted by service provider during transfer</span>
                    </label>
                </div>
                <div class="joint">
                    <label for="date" class="flex-wrap flex-column">
                        <span class="label_title">Date Sent</span>
                        <input type="text" name="date" id="date" style="width:100%" <?php 
                            if($user_details["role"] != 1){
                                echo "disabled";
                            }
                        ?>>
                        <?php if($user_details["role"] == 1){?>
                        <span class="item-event info" id="change_date">Update Date</span>
                        <?php } ?>
                    </label>
                    <?php if($user_details["role"] == 1){?>
                    <label for="update_date" class="flex-wrap flex-column no_disp">
                        <span class="label_title">Update Date</span>
                        <input type="date" name="update_date" id="update_date" style="width: 100%">
                    </label>
                    <?php } ?>
                    <label for="status" class="flex-wrap flex-column">
                        <span class="label_title">Status</span>
                        <input type="text" name="status" id="status" style="width:100%" disabled>
                        <?php 
                            if($user_details["role"] == 1){
                                echo "<span id='change_status' class='item-event info'>Change Status</span>";
                            }
                        ?>
                    </label>
                    <?php if($user_details["role"] == 1){?>
                    <label for="update_status" class="flex-wrap flex-column no_disp">
                        <span class="label_title">Update Status</span>
                        <select name="update_status" id="update_status" style="width:100%">
                            <option value="">Select Status</option>
                            <option value="Sent">Sent</option>
                            <option value="Pending">Pending</option>
                        </select>
                    </label>
                    <?php } ?>
                </div>
                <?php if($user_details["role"] == 1){ ?>
                <label for="submit" class="btn">
                    <button type="submit" name="submit" id="submit">Update</button>
                </label>
                <label for="form_load">
                    <span></span>
                </label>
                <?php } ?>
            </div>
        </div>
        <div class="btn">
            <button class="red" type="reset">Close</button>
        </div>
    </div>
    <script>
        $("table tbody").click(function(){
            parent = $(this).parent();
            parent_id = $(parent).attr("id");
            thead = $(parent).children("thead");
            tbody = $(parent).children("tbody");
            td_count = $(tbody).children("tr").length;

            highlight_count = parseInt($("tr.highlight").attr("data-row-count"));
            new_tbody = "";

            if(highlight_count == td_count && td_count > 1){
                if(td_count > 2){
                    init = 2;
                }else{
                    init = 1;
                }

                for(var i = init; i >= 0; i--){
                    data = $("#" + parent_id + " tr[data-row-count=" + (td_count - i) + "]").html();
                    tr_class = $("#" + parent_id + " tr[data-row-count=" + (td_count - i) + "]").prop("class");
                    new_tbody += "<tr class='" + tr_class + "'>" + data + "</tr>";
                }
            }else if(td_count > 1){
                if(highlight_count != 1){
                    if(td_count > 2){
                        max = 1;
                    }else{
                        max = 0;
                    }
                    min = -1;
                }else{
                    min = 0;
                    if(td_count > 2){
                        max = 2;
                    }else{
                        max = 1;
                    }
                }
                
                for(var i = min; i <= max; i++){
                    data = $("#" + parent_id + " tr[data-row-count=" + (highlight_count + i) + "]").html();
                    tr_class = $("#" + parent_id + " tr[data-row-count=" + (highlight_count + i) + "]").prop("class");
                    
                    new_tbody += "<tr class='" + tr_class + "'>" + data + "</tr>";
                }
            }else{
                new_tbody = $(tbody).html();
            }

            $("#tables #table_view table thead").html($(thead).html());
            $("#tables #table_view table tbody").html(new_tbody);
            $("#tables").removeClass("no_disp");
        })

        $("table tbody tr").click(function(){
            $(this).addClass("highlight");
            recepient_name = $(this).children("td.td_name").html();

            if(recepient_name == "-" || recepient_name.toLowerCase() == "not set"){
                recepient_name = "Receipt Number " + $(this).attr("data-row-id");
            }
            $("#data_details #send_name").html(recepient_name);

            //parse details into form
            $("#data_details input[name=trans_ref]").val($(this).children("td:first-child").html());
            $("#data_details input[name=channel]").val($(this).children("td.td_channel").html());
            $("#data_details input[name=amount]").val($(this).children("td.td_amount").html());
            $("#data_details input[name=date]").val($(this).children("td.td_date").html());
            $("#data_details input[name=deduction]").val($(this).children("td.td_deduction").html());
            $("#data_details input[name=send_name]").val($(this).children("td.td_name").html());
            $("#data_details input[name=send_phone]").val($(this).children("td.td_number").html());
            $("#data_details input[name=student_count]").val("Money below is a collective for " + $(this).children("td.td_student").html() + " enroled students");

            
            if($(this).children("td:last-child").html().toLocaleLowerCase() == "sent"){
                $("#data_details input[name=status]").val("Transaction was successful");
            }else{
                $("#data_details input[name=status]").val("Transaction is pending send or approval");
            }
        })

        $("span#change_status").click(function(){
            $("#data_details label[for=update_status]").toggleClass("no_disp");

            if($(this).html().toLowerCase().includes("change")){
                $(this).html("Retain Status");
            }else{
                $(this).html("Change Status");
            }
        })

        $("span#change_channel").click(function(){
            $("#data_details label[for=update_channel]").toggleClass("no_disp");

            if($(this).html().toLowerCase().includes("update")){
                $(this).html("Retain Channel");
            }else{
                $(this).html("Update Channel");
            }
        })

        $("span#change_date").click(function(){
            $("#data_details label[for=update_date]").toggleClass("no_disp");

            if($(this).html().toLowerCase().includes("update")){
                $(this).html("Retain Date");
            }else{
                $(this).html("Update Date");
            }
        })

        $("#tables .btn .red").click(function(){
            $("#tables").addClass("no_disp");
            $("#tables_view thead, #tables_view tbody").html("");
            $("table tbody tr").removeClass("highlight");

            $("#data_details label[for=update_status]").addClass("no_disp");
            $("span#change_status").html("Change Status");
            $("span#change_status").html("Update Channel");
        })

        $("thead td").click(function(){
            table = $(this).parents("thead").siblings("tbody").eq(0);
            rows = table.find("tr").toArray().sort(comparer($(this).index()));
            this.asc = !this.asc;
            if(!this.asc){rows = rows.reverse()}
            for(var i=0; i < rows.length; i++){
                table.append(rows[i]);
            }
        })

        function comparer(index){
            return function(a, b){
                valA = getCellValue(a, index), valB = getCellValue(b, index);
                return $.isNumeric(valA) && $.isNumeric(valB) ? valA - valB : valA.toString().localeCompare(valB);
            }
        }

        function getCellValue(row, index){
            return $(row).children("td").eq(index).text();
        }

        $("button#auto_gen").click(function(){
            html = $(this).parent().html();
            $.ajax({
                url: "submit.php",
                data: "submit=updatePayment",
                beforeSend: function(){
                    $("#stat").removeClass("no_disp");
                    $("#stat span").html("Loading...");
                },
                success: function(data){
                    if(data == "success"){
                       $("#stat span").html("Updated Successfully! Refreshing");
                       
                       setTimeout(function(){
                           $("#stat span").html("");
                           $("#stat").addClass("no_disp");

                           $("#lhs .item.active").click();
                       }, 3000);
                    }else{
                        console.log(data);
                    }
                },
                error: function(jqXHR, textStatus, errorThrown){
                    alert_box(textStatus, "warning", 7);
                }
            })
        })
    </script>

    <script>
        $("#data_details button[name=submit]").click(function(){
            trans_ref = $("#data_details input[name=trans_ref]").val();
            update_channel = $("#data_details select[name=update_channel]").val();
            amount = $("#data_details input[name=amount]").val();
            deduction = $("#data_details input[name=deduction]").val();
            update_date = $("#data_details input[name=update_date]").val();
            update_status = $("#data_details select[name=update_status]").val();
            row_id = $("table tbody tr.highlight").attr("data-row-id");
            send_name = $("#data_details input[name=send_name]").val();
            send_phone = $("#data_details input[name=send_phone]").val();
            date = $("#data_details input[name=date]").val();
        
            dataString = "trans_ref=" + trans_ref + "&update_date=" + update_date + "&update_channel=" + update_channel +
             "&amount=" + amount + "&deduction=" + deduction + "&update_status=" + update_status + "&row_id=" + row_id + "&submit=updateSelectedPayment" + 
             "&send_name=" + send_name + "&send_phone=" + send_phone + "&date=" + date;

            $.ajax({
                url: "submit.php",
                data: dataString,
                beforeSend: function(){
                    $("label[for=form_load] span").html("Loading...");
                },
                success: function(data){
                    if(data == "success"){
                        $("label[for=form_load] span").html("Update successful. Redirecting!");

                        setTimeout(function(){
                            $("#lhs .item.active").click();
                        }, 3000);
                    }else{
                        $("label[for=form_load] span").html("Update could not be made. Please try again!");
                    }
                },
                error: function(request, status, errorText){
                    if(request.readyState == 4){
                        $("label[for=form_load] span").html(request.statusText);
                    }else{
                        $("label[for=form_load] span").html(request.statusText);
                    }
                    
                }
            })
        })
    </script>