<?php include_once("compSession.php"); $_SESSION["active-page"] = "profile" ?>
<section class="p-section flex-wrap flex p-lg flex-align-start">
    <div id="lhs" class="lt-shade flex-all-center flex-column gap-lg">
        <div class="name txt-al-c">
            <h2 class="txt-fm">Lastname Othername</h2>
            <p class="txt-fs">@username</p>
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
        <form action="<?= $url ?>/submit.php" method="POST" class="flex flex-column gap-sm">
            <div class="joint gap-sm">
                <label for="lname" class="flex flex-column">
                    <span class="label_title">Lastname</span>
                    <input type="text" name="lname" id="lname" value="<?= "" ?>" 
                        placeholder="Lastname"  readonly>
                </label>
                <label for="oname" class="flex flex-column">
                    <span class="label_title">Othername(s)</span>
                    <input type="text" name="oname" id="oname" value="<?= "" ?>" 
                        placeholder="Othername(s)" readonly>
                </label>
                <label for="teacher_id" class="flex flex-column">
                    <span class="label_title">Teacher ID</span>
                    <input type="text" name="teacher_id" id="teacher_id" value="<?= "" ?>" 
                        placeholder="Teacher ID" readonly>
                </label>
                <label for="gender" class="flex flex-column">
                    <span class="label_title">Gender</span>
                    <select name="gender" id="gender" class="white" disabled>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                    </select>
                </label>
                <label for="residence" class="flex flex-column">
                    <span class="label_title">Residential Status</span>
                    <input type="text" name="residence" id="residence" placeholder="Residential Status" value="<?= "" ?>" readonly>
                </label>
            </div>
            <hr class="sm-xlg-tp" />
            <div class="joint gap-sm">
                <label for="email" class="flex flex-column">
                    <span class="label_title">Email</span>
                    <input type="email" name="email" id="email" placeholder="Email" id="email">
                </label>
                <label for="password" class="flex flex-column">
                    <span class="label_title">Password</span>
                    <input type="password" name="password" id="password" placeholder="Password">
                </label>
                <label for="primary_contact" class="flex flex-column">
                    <span class="label_title">Contact Number</span>
                    <input type="tel" name="primary_contact" id="primary_contact" placeholder="Contact Number" value="<?= "" ?>">
                </label>
            </div>
            <div class="btn w-full sm-unset-lr sm-xlg-t">
                <button class="primary sp-lg w-full">Update</button>
            </div>
        </form>
    </div>
</section>

<script>
    $("form").submit(function(e){
        e.preventDefault()
        alert($(this).serialize())
    })
</script>