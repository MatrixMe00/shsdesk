<?php require_once "compSession.php"; $_SESSION["active-page"] = "code" ?>
<section class="d-section txt-al-c">
    <h1 class="sm-lg-b">Purchase your Access Code</h1>
    <form action="" class="wmax-md gap-med txt-al-l w-full sm-auto-lr lt-shade-h">
        <div class="body flex flex-column gap-sm flex-eq sm-med-tp">
            <div class="joint gap-sm">
                <label for="indexNumber" class="flex flex-column">
                    <span class="label_title">Index Number</span>
                    <input type="text" name="indexNumber" id="indexNumber" value="<?= $student["indexNumber"] ?>" placeholder="Index Number" readonly>
                </label>
                <label for="lname" class="flex flex-column">
                    <span class="label_title">Lastname</span>
                    <input type="text" name="lname" id="lname" value="<?= $student["Lastname"] ?>" placeholder="Lastname" readonly>
                </label>
                <label for="oname" class="flex flex-column">
                    <span class="label_title">Othername(s)</span>
                    <input type="text" name="oname" id="oname" value="<?= $student["Othernames"] ?>" placeholder="Othername(s)" readonly>
                </label>
            </div>
            <div class="joint gap-sm">
                <label for="email" class="flex flex-column">
                    <span class="label_title">Email</span>
                    <input type="email" name="email" id="email" value="<?= $student["Email"] ?>" placeholder="Email">
                </label>
                <label for="phoneNumber" class="flex flex-column">
                    <span class="label_title">Phone Number</span>
                    <input type="tel" name="phoneNumber" class="tel" id="phoneNumber" value="" placeholder="Phone Number">
                </label>
            </div>
            <label class="btn w-full">
                <button class="teal w-full sp-lg">Make Payment</button>
            </label>
        </div>
    </form>
</section>

<section class="no_disp d-section txt-al-c">
    <h1 class="sm-lg-b">Your Active Access Code</h1>
    <form action="#" class="wmax-md gap-med txt-al-l w-full sm-auto-lr lt-shade-h">
        <div class="body gap-sm flex flex-column flex-eq sm-med-tp">
            <div class="joint gap-sm">
                <label for="indexNumber" class="flex flex-column">
                    <span class="label_title">Index Number</span>
                    <input type="text" name="indexNumber" value="0123456789" placeholder="Index Number" readonly>
                </label>
                <label for="owner" class="flex flex-column">
                    <span class="label_title">Owner</span>
                    <input type="text" name="owner" value="Lastname Othername(s)" placeholder="Owner" readonly>
                </label>
            </div>
            <div class="joint gap-sm">
                <label for="owner" class="flex flex-column">
                    <span class="label_title">Purchase Number [Phone]</span>
                    <input type="tel" name="owner" value="0123456789" placeholder="Owner" readonly>
                </label>
                <label for="purchase_date" class="flex flex-column">
                    <span class="label_title">Date Purchased</span>
                    <input type="datetime-local" name="purchase_date" value="<?= date("Y-m-d H:i:s") ?>" readonly>
                </label>
                <label for="expiry_date" class="flex flex-column">
                    <span class="label_title">Expiry Date</span>
                    <input type="datetime-local" name="expiry_date" value="<?= date("Y-m-d H:i:s") ?>" readonly>
                </label>
            </div>
            <div class="label no-border txt-fl3 flex-all-center w-full sm-xlg-t">
                <strong>ABCD1234</strong>
            </div>
        </div>
    </form>
</section>

<section class="d-section txt-fl txt-al-c">
    <div class="btn p-lg ">
        <button id="activate_btn" class="primary" data-activate="true">Activate Code</button>
    </div>
</section>


<script>
    $("#activate_btn").click(function(){
        var activate = $(this).attr("data-activate")

        $(".d-section").toggleClass("no_disp")
        $(this).parents(".d-section").removeClass("no_disp")

        if(activate == "false"){
            $(this).attr("data-activate","true")
            $(this).html("Activate Code")
            $("#my_report").addClass("no_disp")
        }else{
            $(this).attr("data-activate","false")
            $(this).html("Deactivate Code")
            $("#my_report").removeClass("no_disp")
        }

        $("#active_access, #nonactive_access").toggleClass("no_disp")
    })

    $("form").submit(function(e){
        e.preventDefault()
        alert($(this).serialize())
    })
</script>