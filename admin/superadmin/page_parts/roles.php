<?php include_once("session.php");

    //add nav point session
    $_SESSION["nav_point"] = "user_roles";

    $roles = decimalIndexArray(fetchData("*", "roles", "school_id = 0", 0));
    $constant_roles = [
        "admin", "school head", "developer", "system", "superadmin", "chass", "chass_t"
    ];
?>

<section>
    <div class="base-foot">
        <div class="btn">
            <button class="cyan">Add New Role</button>
        </div>
    </div>
</section>

<section>
    <div class="head">
        <h3>Admins on System</h3>
    </div>
    <div class="body">
        <label for="system_price">
            <span class="label_title">Current System Price</span>
            <input type="text" id="system_price" readonly value="<?= $system_usage_price + $system_up_gross ?>" />
        </label>
        <table class="full">
            <thead>
                <td>Role Text</td>
                <td>Percentage (%)</td>
                <td>Calculated Price</td>
            </thead>

            <tbody>
                <?php
                    if($roles): 
                    foreach($roles as $role): ?>
                <tr>
                    <td class="title"><?= htmlspecialchars($role["title"]) ?></td>
                    <td data-item-init="<?= $role["price"] ?>" class="price"><?= htmlspecialchars($role["price"]) ?></td>
                    <td class="amount_value"><?= number_format(htmlspecialchars(($role["price"] / 100) * $system_usage_price), 2) ?></td>
                    <?php if($admin_access === 5): ?>
                    <td>
                        <span class="item-event edit" data-item-id="<?= $role["id"] ?>">Edit</span>
                        <?php if(!in_array(strtolower($role["title"]), $constant_roles)): ?>
                            <span class="item-event delete" data-item-id="<?= $role["id"] ?>">Delete</span>
                        <?php endif; ?>

                    </td>
                    <?php endif; ?>

                </tr>
                <?php endforeach;
                    else:
                ?>
                <tr>
                    <td colspan="3">No roles found</td>
                </tr>
                <?php endif; ?>
            
            </tbody>
        </table>
    </div>
</section>

<script>
    $(document).ready(function(){
        const systemPrice = parseFloat($("#system_price").val());

        $(".btn button").on("click", function(){
            const roleTitle = prompt("Enter Role Title:");
            if(roleTitle){
                const rolePrice = prompt("Enter Role Percentage (%):");
                const priceValue = parseFloat(rolePrice);
                if(!isNaN(priceValue) && priceValue >= 0){
                    const accessValue = prompt("Enter Access Value (1 to 5):");
                    const accessLevel = parseInt(accessValue);
                    if(!isNaN(accessLevel) && accessLevel >= 1 && accessLevel <= 5){
                        const isSystemRole = confirm("Is this a system role? Click OK for Yes, Cancel for No.") ? 1 : 0;
                        $.post("/admin/superadmin/submit.php", {
                            submit: "add_user_role",
                            title: roleTitle,
                            price: priceValue,
                            access: accessLevel,
                            system_role: isSystemRole
                        }, function(response){
                            if(response.success){
                                location.reload();
                            } else {
                                alert_box(response.message || "Failed to add role.", "danger");
                            }
                        }, "json");
                    } else {
                        alert("Please enter a valid access value between 1 and 5.");
                    }
                } else {
                    alert("Please enter a valid non-negative number for the percentage.");
                }
            }
        });

        $(".item-event.edit").on("click", function(){
            const parent_tr = $(this).closest("tr");
            const itemId = $(this).data("item-id");
            const currentRow = $(this).closest("tr");
            const currentTitle = currentRow.find("td").eq(0).text();
            const currentPrice = currentRow.find("td[data-item-init]").data("item-init");

            if(currentTitle){
                const newPrice = prompt("Edit " + currentTitle + " Percentage (%):", currentPrice);
                const priceValue = parseFloat(newPrice);
                if(!isNaN(priceValue) && priceValue >= 0){
                    $.post("/admin/superadmin/submit.php", {
                        id: itemId,
                        price: priceValue,
                        submit: "edit_user_role"
                    }, function(response){
                        if(response.success){
                            parent_tr.find("td.price").data("item-init", priceValue).text(priceValue.toFixed(2));
                            parent_tr.find("td.amount_value").text(((priceValue / 100) * systemPrice).toFixed(2));
                        } else {
                            alert_box(response.message || "Failed to update role.", "danger");
                        }
                    }, "json");
                } else if(priceValue !== null && priceValue !== "") {
                    alert_box("Please enter a valid non-negative number for the percentage.", "danger");
                }
            }
        });
        $(".item-event.delete").on("click", function(){
            const itemId = $(this).data("item-id");
            const parent_tr = $(this).closest("tr");

            if(confirm("Are you sure you want to delete this role?")){
            $.post("/admin/superadmin/submit.php", {
                submit: "delete_user_role",
                id: itemId
            }, function(response){
                if(response.success){
                    alert_box("Role has been successfully deleted.", "success");
                    parent_tr.remove();
                } else {
                    if(response.can_switch){
                        const switchTitle = prompt("Deletion failed. Do you want to switch users to a different role? Enter the title of the role to switch to:");
                        if(switchTitle){
                        $.post("/admin/superadmin/submit.php", {
                            submit: "switch_user_role",
                            delete_id: itemId,
                            switch_title: switchTitle,
                            should_delete: 1
                        }, function(switchResponse){
                            if(switchResponse.success){
                                alert_box("Role deleted and users switched successfully.", "success");
                                parent_tr.remove();
                            } else {
                                alert_box(switchResponse.message || "Failed to switch users to the new role.", "danger");
                            }
                        }, "json");
                        }
                    } else {
                        alert_box(response.message || "Failed to delete role.", "danger");
                    }
                }
            }, "json");
            }
        });
    })
</script>