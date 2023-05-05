<?php include_once("compSession.php"); $_SESSION["active-page"] = "profile" ?>
<section class="p-section flex-wrap flex p-lg flex-align-start">
    <div id="lhs" class="lt-shade flex-all-center flex-column gap-lg">
        <div class="name txt-al-c">
            <h2 class="txt-fm">Student Name</h2>
            <p class="txt-fs">@0123456789</p>
        </div>
        <div>
            <div class="border w-auto sp-med rnd txt-al-c">
                <img src="<?= $url ?>/assets/images/icons/person-outline.svg" class="icon-xsm" alt="">
            </div>
            <div class="btn w-full sm-med-t">
                <button class="w-full sp-med cyan">Change Profile Image</button>
            </div>
        </div>        
    </div>
    <div id="rhs" class="lt-shade">
        <h1>Edit Profile</h1>
        <form action="" class="flex flex-column gap-sm">
            <div class="joint gap-sm">
                <label for="lname" class="flex flex-column">
                    <span class="label_title">Lastname</span>
                    <input type="text" name="lname" id="lname" value="Lastname" 
                        placeholder="Lastname"  readonly>
                </label>
                <label for="oname" class="flex flex-column">
                    <span class="label_title">Othername(s)</span>
                    <input type="text" name="oname" id="oname" value="Othername(s)" 
                        placeholder="Othername(s)" readonly>
                </label>
                <label for="indexNumber" class="flex flex-column">
                    <span class="label_title">Index Number</span>
                    <input type="text" name="indexNumber" id="indexNumber" value="0123456789" 
                        placeholder="indexNumber" readonly>
                </label>
                <label for="programme" class="flex flex-column">
                    <span class="label_title">Course Program</span>
                    <input type="text" name="programme" id="programme" placeholder="Course Program" value="Course Name" readonly>
                </label>
                <label for="residence" class="flex flex-column">
                    <span class="label_title">Residential Status</span>
                    <input type="text" name="residence" id="residence" placeholder="Residential Status" value="Boarder" readonly>
                </label>
                <label for="house" class="flex flex-column">
                    <span class="label_title">House Name</span>
                    <input type="text" name="house" id="house" placeholder="House Name" value="House Name" readonly>
                </label>
            </div>
            <hr class="sm-xlg-tp" />
            <div class="joint gap-sm">
                <label for="email" class="flex flex-column">
                    <span class="label_title">Email [optional]</span>
                    <input type="email" name="email" id="email" placeholder="Email" id="email">
                </label>
                <label for="password" class="flex flex-column">
                    <span class="label_title">Password</span>
                    <input type="password" name="password" id="password" placeholder="Password">
                </label>
                <label for="primary_contact" class="flex flex-column">
                    <span class="label_title">Contact of Guardian</span>
                    <input type="tel" name="primary_contact" id="primary_contact" placeholder="Guardian Phone Number">
                </label>
                <label for="secondary_contact" class="flex flex-column">
                    <span class="label_title">Contact of Guardian 2 [optional]</span>
                    <input type="tel" name="secondary_contact" id="secondary_contact" placeholder="Guardian Phone Number 2">
                </label>
            </div>
            <div class="btn w-full sm-unset-lr sm-xlg-t">
                <button class="primary sp-lg w-full">Update</button>
            </div>
        </form>
    </div>
</section>