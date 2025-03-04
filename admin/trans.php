<?php include_once("../includes/session.php");

    $_SESSION["nav_point"] = "payment";

    $isAdmin = $admin_access === 2;
    $isHead = $admin_access === 1 && str_contains($user_role, "head");
    $is_chass = str_contains($user_role, "chass");

    // hold the rows to prevent multiple requests from db about role titles
    $role_array = [];
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

<section class="txt-al-c p-med">
    <p>This section is used to display all payments done through the system to admins and schools</p>
</section>

<?php if($isAdmin): 
    $trans_split = fetchData("*","transaction_splits","schoolID=$user_school_id");
    if($trans_split == "empty"){
        $trans_message = "Please add your transaction accounts by using the \"Trans Account\" tab below";
    }else{
        $trans_message = "Transaction account has been provided";
    }
?>
<section class="txt-al-c <?= $trans_split == "empty" ? "color-red":"color-green" ?>">
    <p><?= $trans_message ?></p>
</section>
<?php endif; ?>

<?php if(($isAdmin || $isHead) || $role_id <= 2 || $is_chass): ?>
<section>
    <div class="head txt-al-c">
        <h3>Controls</h3>
    </div>
    <div class="body flex">
        <div class="btn p-med wmax-sm flex-eq flex flex-wrap sm-auto gap-sm w-full">
            <button id="auto_gen" class="teal"><?php
                if($user_details["role"] > 2){
                    echo "Get Latest Results";
                }else{
                    echo "Update";
                }
            ?></button>
            <?php if($isAdmin): ?>
            <button id="trans_split" class="pink">Trans Account</button>
            <button id="close_trans" class="red no_disp">Close Block</button>
            <?php endif; ?>
        </div>
        <div id="stat" class="no_disp" style="align-self: center">
            <span></span>
        </div>
    </div>
</section>

<?php if(isset($_SESSION["user_login_id"]) && $admin_access > 3){?>
<section>
    <div class="head">
        <h3>Super Admins</h3>
    </div><?php
        $system_payments = decimalIndexArray(fetchData("*","payment","user_role <= 2", 0));

        if(is_array($system_payments)){
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
                foreach($system_payments as $row){
            ?>
                <tr data-row-count="<?php echo $count?>"<?php
                    if($row["status"] == "Pending"){
                        echo " class=\"pending\"";
                    }
                ?> data-row-id="<?php echo $row["id"]?>">
                    <td class="td_transaction">
                        <?= empty($row["transactionReference"]) ? "Not set" : $row["transactionReference"] ?>
                    </td>
                    <td class="td_name">
                        <?= $row["contactName"] == "-" ? "Not set" : $row["contactName"] ?>
                    </td>
                    <td class="td_number">
                        <?= empty($row["contactNumber"]) ? "Not set" : $row["contactNumber"] ?>
                    </td>
                    <td class="td_role"><?php
                        if(!isset($role_array[$row["user_role"]])){
                            $role_array[$row["user_role"]] = getRole($row["user_role"]);
                        }
                        
                        echo $role_array[$row["user_role"]];
                    ?></td>
                    <td class="td_student"><?= number_format($row["studentNumber"]) ?></td>
                    <td class="td_channel">
                        <?= empty($row["method"]) ? "Not set" : $row["method"] ?>
                    </td>
                    <td class="td_amount">
                        <?= number_format(($row["amount"] - $row["deduction"]),2) ?>
                    </td>
                    <td class="td_deduction no_disp">
                        <?= $row["deduction"]?>
                    </td>
                    <td class="td_date"><?php
                        if(empty($row["date"])){
                            echo "Not set";
                        }else{
                            echo date("M d, Y",strtotime($row["date"]));
                        }
                    ?></td>
                    <td><?= $row["status"]?></td>
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

<?php if($is_chass || $admin_access > 3): ?>
<section class="section_block">
    <div class="head">
        <h3><?= $admin_access > 3 ? "Chass Heads" : "Payment History" ?></h3>
    </div>
    <?php 
        if($admin_access > 3){
            $chass_ids = decimalIndexArray(fetchData("id", "roles", "title LIKE 'chass%'", 0));

            if($chass_ids){
                $chass_ids = implode(",", array_column($chass_ids, "id"));
                $chass_payments = decimalIndexArray(fetchData("*","payment","user_role IN ($chass_ids)", 0));
            }else{
                $chass_payments = false;
            }
            
        }else{
            $chass_payments = decimalIndexArray(fetchData("*","payment","user_role = $role_id", 0));
        }

        if($chass_payments):
    ?>
    <div class="body">
        <table id="table_4">
            <thead>
                <tr>
                    <td>Transaction Reference</td>
                    <td>Sent To</td>
                    <td>Contact Number</td>
                    <?php if($admin_access > 3): ?>
                    <td>User Role</td>
                    <?php endif; ?>
                    <td>Students Cleared</td>
                    <td>Channel</td>
                    <td>Amount</td>
                    <td>Date</td>
                    <td>Status</td>
                </tr>
            </thead>
            <tbody><?php foreach($chass_payments as $count => $row): ?>
                <tr data-row-count="<?php echo ($count + 1)?>" class="<?= $row["status"] == "Pending" ? "pending" : "" ?>" data-row-id="<?php echo $row["id"]?>">
                    <td class="td_transaction">
                        <?= empty($row["transactionReference"]) ? "Not set" : $row["transactionReference"] ?>
                    </td>
                    <td class="td_name">
                        <?= $row["contactName"] == "-" ? "Not set" : $row["contactName"] ?>
                    </td>
                    <td class="td_number">
                        <?= empty($row["contactNumber"]) ? "Not set" : $row["contactNumber"] ?>
                    </td>
                    <?php if($admin_access > 3) : ?>
                    <td class="td_role"><?php
                        if(!isset($role_array[$row["user_role"]])){
                            $role_array[$row["user_role"]] = getRole($row["user_role"]);
                        }
                        
                        echo $role_array[$row["user_role"]] == "chass_t" ? "CHASS [Tech]" : "CHASS [SHS/SHTS]";
                    ?></td>
                    <?php endif; ?>
                    <td class="td_student"><?= number_format($row["studentNumber"]) ?></td>
                    <td class="td_channel">
                        <?= empty($row["method"]) ? "Not set" : $row["method"] ?>
                    </td>
                    <td class="td_amount">
                        <?= number_format(($row["amount"] - $row["deduction"]),2) ?>
                    </td>
                    <td class="td_deduction no_disp">
                        <?= $row["deduction"]?>
                    </td>
                    <td class="td_date"><?php
                        if(empty($row["date"])){
                            echo "Not set";
                        }else{
                            echo date("M d, Y",strtotime($row["date"]));
                        }
                    ?></td>
                    <td><?= $row["status"]?></td>
                </tr><?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="8">End of Data Table</td>
                </tr>
            </tfoot>
        </table>
    </div>
    <?php else: ?>
    <div class="body empty">
        <p>No results to display.</p>
    </div>
    <?php endif; ?>
</section>
<?php endif;?>

<?php if($isAdmin || $admin_access > 3){?>
<section class="section_block">
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

<?php if(!$is_chass): ?>
<section class="section_block">
    <div class="head">
        <h3>School Head<?php if($user_details["role"] <= 2){echo "s"; }?></h3>
    </div>
    <?php
        if($user_school_id > 0){
            if($isAdmin){
                $sql = "SELECT * FROM payment WHERE user_role = ".(intval($user_details["role"])+1)." AND school_id= $user_school_id";
            }else{
                $sql = "SELECT * FROM payment WHERE user_role = ".$user_details["role"]." AND school_id= $user_school_id";
            }
        }else{
            $heads = fetchData("id","roles","id > 2 AND title LIKE 'school head%'", 0);
            $sql = "SELECT * FROM payment";

            if(array_key_exists("id",$heads)){
                $heads = [0=>$heads];
            }
            
            if(is_array($heads)){
                $sql .= " WHERE ";
                foreach($heads as $head){
                    $sql .= " user_role=".$head["id"];

                    if(end($heads) != $head){
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
<?php endif; ?>

<?php if($isAdmin): 
    //check if user is taking a share from the system
    if($role_price > 0){
        $adminIsPaid = true;
    }else{
        $adminIsPaid = false;
    }
?>
<section class="section_block trans_split no_disp">
    <?php if($trans_split == "empty"): ?>
    <form name="add_new_split" method="POST" action="<?= "$url/admin/admin/submit.php" ?>">
        <div class="head">
            <h2>Transaction Accounts</h2>
        </div>
        <div class="color-yellow txt-fs sm-med-tp">
            <p class="txt-al-c">The details in this form must be legit and accurate since SHSDesk would not be liable to any errors done</p>
        </div>
        <div class="body relative">
            <div class="message_box sticky top no_disp">
                <span class="message"></span>
                <div class="close"><span>&cross;</span></div>
            </div>
            <input type="hidden" name="school_id" value="<?= $user_school_id ?>">
            <div class="joint">
                <?php 
                    $vendor = [
                        "mobile" => [
                            "AirtelTigo", "Glo", "MTN","Vodafone"
                        ],
                        "bank" => [
                            array('value' => 'Agricultural Development Bank', 'name' => 'ADB'),
                            array('value' => 'Bank of Ghana', 'name' => 'BoG'),
                            array('value' => 'Barclays Bank', 'name' => 'Barclays'),
                            array('value' => 'CalBank', 'name' => 'CalBank'),
                            array('value' => 'Ecobank Ghana', 'name' => 'Ecobank'),
                            array('value' => 'Fidelity Bank', 'name' => 'Fidelity'),
                            array('value' => 'GCB Bank', 'name' => 'GCB'),
                            array('value' => 'National Investment Bank', 'name' => 'NIB'),
                            array('value' => 'Prudential Bank', 'name' => 'Prudential'),
                            array('value' => 'Republic Bank', 'name' => 'Republic'),
                            array('value' => 'Stanbic Bank', 'name' => 'Stanbic'),
                            array('value' => 'Standard Chartered Bank', 'name' => 'SCB'),
                            array('value' => 'Societe Generale', 'name' => 'SocGen'),
                            array('value' => 'United Bank for Africa', 'name' => 'UBA'),
                            array('value' => 'Universal Merchant Bank', 'name' => 'UMB'),
                            array('value' => 'Zenith Bank', 'name' => 'Zenith'),
                            array('value' => 'Access Bank', 'name' => 'Access'),
                            array('value' => 'First National Bank', 'name' => 'FNB'),
                            array('value' => 'GHL Bank', 'name' => 'GHL'),
                            array('value' => 'StanChart Bank', 'name' => 'StanChart')
                        ]
                    ];
                    $bankVals = array_column($vendor["bank"], "value");
                    array_multisort($bankVals, SORT_ASC, $vendor["bank"]);
                ?>
                <?php if($adminIsPaid): ?>
                <label for="admin_account_type" class="flex-column gap-sm">
                    <span class="label_title">Account Type [Admin]</span>
                    <select name="admin_account_type" id="admin_account_type" class="account_type" data-mode="admin">
                        <option value="">Select Account Type</option>
                        <option value="mobile">Mobile Money</option>
                        <option value="bank">Bank</option>
                    </select>
                </label>
                <label for="admin_account_vendor1" id="admin_mobile" class="flex-column gap-sm no_disp">
                    <span class="label_title">Account Vendor [Admin]</span>
                    <select id="admin_account_vendor1" class="bank">
                        <option value="">Select mobile Vendor</option>
                        <?php 
                            foreach($vendor["mobile"] as $mobile){
                                echo "<option value='$mobile'>$mobile</option>";
                            }
                        ?>
                    </select>
                </label>
                <input type="hidden" name="admin_bank" class="bank_name">
                <label for="admin_account_vendor2" id="admin_bank" class="flex-column gap-sm no_disp">
                    <span class="label_title">Account Vendor [Admin]</span>
                    <select id="admin_account_vendor2" class="bank">
                        <option value="">Select Bank Vendor</option>
                        <?php 
                            foreach($vendor["bank"] as $bank){
                                echo "<option value='{$bank['value']}'>{$bank['name']}</option>";
                            }
                        ?>
                    </select>
                </label>
                <label for="admin_number" class="flex-column gap-sm keep">
                    <span class="label_title">Admin's Account Number</span>
                    <input type="text" name="admin_number" id="admin_number" placeholder="Account Number">
                </label>
            </div>
            <?php endif; ?>

            <div class="joint">
                <label for="head_account_type" class="flex-column gap-sm">
                    <span class="label_title">Account Type [Head]</span>
                    <select name="head_account_type" id="head_account_type" class="account_type" data-mode="head">
                        <option value="">Select Account Type</option>
                        <option value="mobile">Mobile Money</option>
                        <option value="bank">Bank</option>
                    </select>
                </label>
                <input type="hidden" name="head_bank" class="bank_name">
                <label for="head_account_vendor1" id="head_mobile" class="flex-column gap-sm no_disp">
                    <span class="label_title">Account Vendor [Head]</span>
                    <select id="head_account_vendor1" class="bank">
                        <option value="">Select mobile Vendor</option>
                        <?php 
                            foreach($vendor["mobile"] as $mobile){
                                echo "<option value='$mobile'>$mobile</option>";
                            }
                        ?>
                    </select>
                </label>
                <label for="head_account_vendor2" id="head_bank" class="flex-column gap-sm no_disp">
                    <span class="label_title">Account Vendor [Head]</span>
                    <select id="head_account_vendor2" class="bank">
                        <option value="">Select Bank Vendor</option>
                        <?php 
                            foreach($vendor["bank"] as $bank){
                                echo "<option value='{$bank['value']}'>{$bank['name']}</option>";
                            }
                        ?>
                    </select>
                </label>
                <label for="head_number" class="flex-column gap-sm keep">
                    <span class="label_title">Head's Account Number</span>
                    <input type="text" name="head_number" id="head_number" placeholder="Account Number">
                </label>
            </div>         
        </div>
        <div class="btn wmax-sm w-full w-fluid-child sm-auto p-lg">
            <button class="primary" name="submit" value="add_split">Add New Split</button>
        </div>
    </form>
    <?php else: ?>
    <div class="form">
        <div class="body">
            <div class="joint">
                <label class="flex-column sp-med gap-sm">
                    <span class="label_title">Split Code [Admission]</span>
                    <input type="text" class="white" readonly value="<?= empty($trans_split["split_code_admission"]) ? "Processing" : $trans_split["split_code_admission"] ?>">
                </label>
                <label class="flex-column sp-med gap-sm">
                    <span class="label_title">Split Code [Management]</span>
                    <input type="text" class="white" readonly value="<?= empty($trans_split["split_code_management"]) ? "Processing" : $trans_split["split_code_management"] ?>">
                </label>
            </div>

            <div class="joint">
                <?php if($adminIsPaid): ?>
                <label class="flex-column sp-med gap-sm">
                    <span class="label_title">Admin Account [Bank]</span>
                    <input type="text" class="white" readonly value="<?= $trans_split["admin_bank"] ?>">
                </label>
                <label class="flex-column sp-med gap-sm">
                    <span class="label_title">Admin Account [Number]</span>
                    <input type="text" class="white" readonly value="<?= $trans_split["admin_number"] ?>">
                </label>
                <?php endif; ?>
                <label class="flex-column sp-med gap-sm">
                    <span class="label_title">Head Account [Bank]</span>
                    <input type="text" class="white" readonly value="<?= $trans_split["head_bank"] ?>">
                </label>
                <label class="flex-column sp-med gap-sm">
                    <span class="label_title">Head Account [Number]</span>
                    <input type="text" class="white" readonly value="<?= $trans_split["head_number"] ?>">
                </label>
            </div>
            <div class="p-lg-lr wmax-md sm-auto p-xlg-tp border txt-al-c">
                <?php if(boolval($trans_split["status"]) === true): ?>
                <p>Your account is active and students can now make payment directly to you through the system.</p>
                <?php else: ?>
                <p>Your account has not been confirmed yet. Please wait as your payment account is registered.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
</section>
<?php endif; ?>

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
                <?php
                    $editable = $role_id <= 2;
                    if($editable): 
                ?>
                <label for="send_name" class="flex-wrap flex-column">
                    <span class="label_title">Sender Name</span>
                    <input type="text" name="send_name" id="send_name">
                </label>
                <label for="send_phone" class="flex-wrap flex-column">
                    <span class="label_title">Transaction Phone Number</span>
                    <input type="text" name="send_phone" id="send_phone">
                </label>
                <label for="student_count" class="flex-wrap flex-column">
                    <span class="label_title">Students Cleared</span>
                    <input type="text" name="student_count" id="student_count">
                </label><?php endif; ?>
            </div>
            <div class="joint">
                <label for="trans_ref" class="flex-wrap flex-column">
                    <span class="label_title">Transaction Reference</span>
                    <input type="text" name="trans_ref" id="trans_ref" style="width:100%" <?= !$editable ? "disabled" : "" ?> />
                </label>
                <label for="channel" class="flex-wrap flex-column">
                    <span class="label_title">Channel</span>
                    <input type="text" name="channel" id="channel" style="width:100%" disabled>
                    <?php if($editable): ?>
                    <span class="item-event info" id="change_channel">Update Channel</span>
                    <?php endif; ?>
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
                    <input type="text" name="amount" id="amount" style="width:100%" <?= !$editable ? "disabled" : "" ?> />
                    <span class="item-event date">Amount above is the full amount sent, this is without the deduction</span>
                </label>
                <label for="deduction" class="flex-wrap flex-column">
                    <span class="label_title">Deduction for Transaction</span>
                    <input type="text" name="deduction" id="deduction" style="width:100%" <?= !$editable ? "disabled" : "" ?>>
                    <span class="item-event date">Amount above is the amount deducted by service provider during transfer</span>
                </label>
            </div>
            <div class="joint">
                <label for="date" class="flex-wrap flex-column">
                    <span class="label_title">Date Sent</span>
                    <input type="text" name="date" id="date" style="width:100%" <?= !$editable ? "disabled" : "" ?> />
                    <?php if($editable):?>
                    <span class="item-event info" id="change_date">Update Date</span>
                    <?php endif; ?>
                </label>
                <?php if($editable): ?>
                <label for="update_date" class="flex-wrap flex-column no_disp">
                    <span class="label_title">Update Date</span>
                    <input type="date" name="update_date" id="update_date" style="width: 100%">
                </label>
                <?php endif; ?>
                <label for="status" class="flex-wrap flex-column">
                    <span class="label_title">Status</span>
                    <input type="text" name="status" id="status" style="width:100%" disabled>
                    <?php 
                        if($editable){
                            echo "<span id='change_status' class='item-event info'>Change Status</span>";
                        }
                    ?>
                </label>
                <?php if($editable):?>
                <label for="update_status" class="flex-wrap flex-column no_disp">
                    <span class="label_title">Update Status</span>
                    <select name="update_status" id="update_status" style="width:100%">
                        <option value="">Select Status</option>
                        <option value="Sent">Sent</option>
                        <option value="Pending">Pending</option>
                    </select>
                </label>
                <?php endif; ?>
            </div>
            <?php if($editable): ?>
            <label for="submit" class="btn">
                <button type="submit" name="submit" id="submit">Update</button>
            </label>
            <label for="form_load">
                <span></span>
            </label>
            <?php endif; ?>
        </div>
    </div>
    <div class="btn">
        <button class="red" type="reset">Close</button>
    </div>
</div>
<?php else: ?>
<section>
    <div class="body empty">
        <p>No transaction details are available for this account.</p>
    </div>
</section>
<?php endif; close_connections(); ?>

<script src="<?= "$url/admin/assets/scripts/trans.js?v=".time() ?>"></script>