<?php 
    include_once("session.php");

    //add nav point session
    $_SESSION["nav_point"] = "trans_splits";

    function payable_amount(){
        // get the id and price of the yet to enter
        $data = decimalIndexArray(fetchData1(["school_id", "access_price"], "accesspay", "active=1", 0));
        if($data){
            foreach($data as $d){
                $response[$d["school_id"]] = $d["access_price"];
            }
        }else{
            $response = null;
        }

        return $response;
    }

    $payable_amount = payable_amount();
?>

<section class="section_container">
    <div class="content" style="background-color: #007bff;">
        <div class="head">
            <h2><?= $total_schools = fetchData("COUNT(id) as total", "schools", "")["total"] ?></h2>
        </div>
        <div class="body">
            <span><?= $total_schools != 1 ? "Schools":"School" ?> Registered</span>
        </div>
    </div>

    <div class="content" style="background-color: #20c997">
        <div class="head">
            <h2><?= $registered = fetchData("COUNT(schoolID) as total","transaction_splits","status=TRUE")["total"] ?></h2>
        </div>
        <div class="body">
            <span>Registered Splits</span>
        </div>
    </div>

    <div class="content" style="background-color: #dc3545">
        <div class="head">
            <h2><?= $pending = fetchData("COUNT(schoolID) as total","transaction_splits","status=FALSE")["total"] ?></h2>
        </div>
        <div class="body">
            <span>Pending Splits</span>
        </div>
    </div>
</section>

<section class="p-lg-lr p-xlg-tp color-pink txt-al-c">
    <p>This page is used to generate add split codes into the system. These Split codes are what enables a school to be paid directly by the payment gateway</p>
</section>

<section class="trans_menu">
    <div class="btn wmax-sm sm-auto gap-sm w-full flex-eq flex p-lg flex-wrap">
        <button class="plain-r primary section_btn" data-section="pending_splits">Pending Splits</button>
        <button class="plain-r primary section_btn" data-section="registered_splits">Registered Splits</button>
        <?php if($user_details["role"] == 1): ?>
        <button class="plain-r primary section_btn" data-section="create_split">Create Split</button>
        <?php endif ?>
    </div>
</section>

<section id="pending_splits" class="btn_section" style="overflow: auto">
    <?php if($pending > 0): ?>
        <table class="full" style="overflow: auto;">
            <thead>
                <td>School Name</td>
                <td>Split Code [Admission]</td>
                <td>Split Code [Management]</td>
                <td>Payable Amount</td>
                <td>Bank Name [Admin]</td>
                <td>Bank Number [Admin]</td>
                <td>Bank Name [Head]</td>
                <td>Bank Number[Head]</td>
            </thead>
            <tbody style="overflow: hidden;"><?php 
                $sql = "SELECT t.*, s.schoolName FROM transaction_splits t JOIN schools s 
                    ON s.id = t.schoolID WHERE t.status = 0";
                $query = $connect->query($sql);
                while($row = $query->fetch_assoc()):
            ?>
                <tr>
                    <td><?= $row["schoolName"] ?></td>
                    <td class="admission_code white" contenteditable="true"><?= $row["split_code_admission"] ?></td>
                    <td class="management_code white" contenteditable="true"><?= $row["split_code_management"] ?></td>
                    <td><?= $payable_amount[$row["schoolID"]] ?? 0 ?></td>
                    <td><?= $row["admin_bank"] ?></td>
                    <td><?= $row["admin_number"] ?></td>
                    <td><?= $row["head_bank"] ?></td>
                    <td><?= $row["head_number"] ?></td>
                    <?php if($user_details["role"] == 1): ?>
                    <td>
                        <span class="item-event approve" data-item-id="<?= $row["schoolID"] ?>">Approve</span>
                    </td>
                    <?php endif ?>
                </tr>
            <?php endwhile; ?>
            </tbody>
            <tfoot></tfoot>
        </table>
    <?php else: ?>
        <div class="empty txt-al-c p-xlg-tp p-lg-lr">
            <p>There are no pending splits at this moment</p>
        </div>
    <?php endif; ?>
</section>

<section id="registered_splits" class="btn_section" style="overflow: auto">
    <?php if($registered > 0): ?>
        <table class="full" style="overflow: hidden;">
            <thead>
                <td>School Name</td>
                <td>Split Code [Admission]</td>
                <td>Split Code [Management]</td>
                <td>Payable Amount</td>
                <td>Bank Name [Admin]</td>
                <td>Bank Number [Admin]</td>
                <td>Bank Name [Head]</td>
                <td>Bank Number[Head]</td>
            </thead>
            <tbody style="overflow: auto;"><?php 
                $sql = "SELECT t.*, s.schoolName FROM transaction_splits t JOIN schools s 
                    ON s.id = t.schoolID WHERE t.status = 1";
                $query = $connect->query($sql);
                while($row = $query->fetch_assoc()):
            ?>
                <tr>
                    <td><?= $row["schoolName"] ?></td>
                    <td><?= $row["split_code_admission"] ?></td>
                    <td><?= $row["split_code_management"] ?></td>
                    <td><?= $payable_amount[$row["schoolID"]] ?? 0 ?></td>
                    <td><?= $row["admin_bank"] ?></td>
                    <td><?= $row["admin_number"] ?></td>
                    <td><?= $row["head_bank"] ?></td>
                    <td><?= $row["head_number"] ?></td>
                    <?php if($user_details["role"] == 1): ?>
                    <td>
                        <span class="item-event reject" data-item-id="<?= $row["schoolID"] ?>">Pending</span>
                    </td>
                    <?php endif ?>
                </tr>
            <?php endwhile; ?>
            </tbody>
            <tfoot></tfoot>
        </table>
    <?php else: ?>
        <div class="empty txt-al-c p-xlg-tp p-lg-lr">
            <p>There are no registered splits at this moment</p>
        </div>
    <?php endif; ?>
</section>
<?php if($user_details["role"] == 1): ?>
<section id="create_split" class="btn_section">
    <form name="add_new_split" method="POST" action="<?= "$url/admin/superadmin/submit.php" ?>">
        <div class="head">
            <h2>Create a New Split</h2>
        </div>
        <div class="body relative">
            <div class="message_box sticky top no_disp">
                <span class="message"></span>
                <div class="close"><span>&cross;</span></div>
            </div>
            <div class="joint">
                <label for="school_id" class="flex-column gap-sm">
                    <span class="label_title">Select a School</span>
                    <select name="school_id" id="school_id">
                        <option value="">Select A School</option>
                        <?php
                            $result = $connect->query("SELECT id,schoolName FROM schools WHERE Active=TRUE");
                            if($result->num_rows > 0){
                                while($row = $result->fetch_assoc()){
                                    echo "<option value=\"{$row['id']}\">{$row['schoolName']}</option>
                                    ";
                                }
                            }else{
                                echo "<option value=\"\">No Active School in system</option>";
                            }
                        ?>
                    </select>
                </label>
                <label for="split_code_admission" class="flex-column gap-sm">
                    <span class="label_title">Admission Split Code</span>
                    <input type="text" name="split_code_admission" id="split_code_admission" placeholder="SPL_xxxxxx">
                </label>
                <label for="split_code_management" class="flex-column gap-sm">
                    <span class="label_title">Management Split Code</span>
                    <input type="text" name="split_code_management" id="split_code_management" placeholder="SPL_xxxxxx">
                </label>
            </div>
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
</section>
<?php endif ?>

<script>
    $(document).ready(function(){
        //hide all sections where necessary
        $(".btn_section").addClass("no_disp")
        $(".section_btn:nth-child(1)").click()
    })

    $(".section_btn").click(function(){
        $(".section_btn:not(.plain-r)").addClass("plain-r")
        $(this).removeClass("plain-r")

        //show associate section
        $(".btn_section:not(.no_disp)").addClass("no_disp")
        $("section#" + $(this).attr("data-section")).removeClass("no_disp")
    })

    $("select.account_type").change(function(){
        const mode = $(this).attr("data-mode");
        const value = $(this).val();
        const parent = $(this).parent();

        $(parent).siblings("label:not(.keep)").addClass("no_disp")
        
        if(value != ""){
            $(parent).siblings("label#"+mode+"_"+value).removeClass("no_disp")
        }
    })

    $(".bank").change(function(){
        const local_bank = $(this).parents(".joint").find(".bank_name")
        local_bank.val($(this).val())
    })

    $("form").submit(function(e){
        e.preventDefault();

        let response = formSubmit($(this), $(this).find("button[name=submit]"), true)
        let type = ""; time = 0;
        
        if(response === true){
            type="success";
            response = "Data was added successfully";

            setTimeout(function(){
                $("#lhs .item.active").click()
            },1000)
        }else{
            type="error";
        }
        messageBoxTimeout("add_new_split", response, type, time)
    })

    $(".item-event.approve, .item-event.reject").click(function(){
        const this_school_id = $(this).attr("data-item-id")
        let stat = 1
        let admission_code = ""
        let management_code = ""

        if(parseInt(this_school_id) > 0){
            if($(this).hasClass("reject")){
                stat = 0
            }else{
                admission_code = $(this).parents("tr").find(".admission_code").html()
                management_code = $(this).parents("tr").find(".management_code").html()
            }
            
            $.ajax({
                url: "./superadmin/submit.php",
                data: {submit: "approve_split", status: stat, school_id: this_school_id, a_code: admission_code, m_code: management_code},
                timeout: 30000,
                method: "POST",
                success: function(response){
                    if(response == "success"){
                        alert_box("Update was successful", "success", 2)
                        $("#lhs .item.active").click()
                    }else{
                        alert_box(response, "danger")
                    }
                }
            })
        }else{
            alert_box("School index is invalid", "danger")
        }
    })
</script>
<?php close_connections(); ?>