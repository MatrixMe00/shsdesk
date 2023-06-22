<?php include_once("compSession.php"); $_SESSION["active-page"] = "profile" ?>
<section class="p-section flex-wrap flex p-lg flex-align-start">
    <div id="lhs" class="lt-shade flex-all-center flex-column gap-lg">
        <div class="name txt-al-c">
            <h2 class="txt-fm"><?= $student["Lastname"]." ".$student["Othernames"] ?></h2>
            <p class="txt-fs">@<?= $student["indexNumber"] ?></p>
        </div>
        <div>
            <div class="border w-auto sp-med rnd txt-al-c">
                <img src="<?= $url ?>/assets/images/icons/person-outline.svg" class="icon-xsm" alt="">
            </div>
            <div class="btn w-full sm-med-t">
                <button class="w-full sp-med cyan" disabled>Change Profile Image</button>
            </div>
        </div>        
    </div>
    <div id="rhs" class="lt-shade">
        <h1>Edit Profile</h1>
        <form action="<?= $url ?>/submit.php" method="POST" class="flex flex-column gap-sm" name="profileForm">
            <div class="joint gap-sm">
                <label for="lname" class="flex flex-column">
                    <span class="label_title">Lastname</span>
                    <input type="text" name="lname" id="lname" value="<?= $student["Lastname"] ?>" 
                        placeholder="Lastname"  readonly>
                </label>
                <label for="oname" class="flex flex-column">
                    <span class="label_title">Othername(s)</span>
                    <input type="text" name="oname" id="oname" value="<?= $student["Othernames"] ?>" 
                        placeholder="Othername(s)" readonly>
                </label>
                <label for="indexNumber" class="flex flex-column">
                    <span class="label_title">Index Number</span>
                    <input type="text" name="indexNumber" id="indexNumber" value="<?= $student["indexNumber"] ?>" 
                        placeholder="indexNumber" readonly>
                </label>
                <label for="programme" class="flex flex-column">
                    <span class="label_title">Course Program</span>
                    <input type="text" name="programme" id="programme" placeholder="Course Program" value="<?= $student["programme"] ?>" readonly>
                </label>
                <label for="residence" class="flex flex-column">
                    <span class="label_title">Residential Status</span>
                    <input type="text" name="residence" id="residence" placeholder="Residential Status" value="<?= $student["boardingStatus"] ?>" readonly>
                </label>
                <label for="house" class="flex flex-column">
                    <span class="label_title">House Name</span>
                    <input type="text" name="house" id="house" placeholder="House Name" value="<?php
                        $house = fetchData("title","houses","id=".$student["houseID"]);
                        echo is_array($house) ? $house["title"] : "Undefined";
                    ?>" readonly>
                </label>
            </div>
            <hr class="sm-xlg-tp" />
            <div class="joint gap-sm">
                <label for="email" class="flex flex-column">
                    <span class="label_title">Email [optional]</span>
                    <input type="email" name="email" id="email" placeholder="Email" id="email" value="<?= $student["Email"] ?>">
                </label>
                <label for="username" class="flex flex-column">
                    <span class="label_title">Username [optional]</span>
                    <input type="text" name="username" id="username" placeholder="Username" value="<?= $student["username"] ?>">
                </label>
                <label for="password_o" class="flex flex-column">
                    <span class="label_title">Current Password</span>
                    <input type="password" name="password_o" id="password_o" placeholder="Current Password">
                </label>
                <label for="password_n" class="flex flex-column">
                    <span class="label_title">New Password</span>
                    <input type="password" name="password_n" id="password_n" placeholder="New Password">
                </label>
                <label for="primary_contact" class="flex flex-column">
                    <span class="label_title">Contact of Guardian</span>
                    <input type="tel" name="primary_contact" id="primary_contact" placeholder="Guardian Phone Number" value="<?php 
                        if(!empty($student["guardianContact"])){
                            echo remakeNumber($student["guardianContact"], false, false);
                        }
                        
                    ?>"
                        minlength="10" maxlength="10">
                </label>
            </div>
            <div class="btn w-full sm-unset-lr sm-xlg-t">
                <button class="primary sp-lg w-full" name="submit" value="update_profile">Update</button>
            </div>
        </form>
    </div>
</section>

<script>
    $("form").submit(async function(e){
        e.preventDefault()
        const response = await formSubmit($(this), $(this).find("button[name=submit]"), false)
        if(response === true){
            alert_box("Changes have been effected")
            $("#lhs .item.active").click()
        }else{
            alert_box(response, "danger", 5)
        }
    })
</script>