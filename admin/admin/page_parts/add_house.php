<form action="<?php echo $url?>/admin/admin/submit.php" method="POSTname="addHouseForm" method="post">
    <div class="head">
        <h2>Add A New House</h2>
    </div>
    <div class="body">
        <input type="hidden" name="school_id" value="<?php echo $school_id?>">
        <label for="house_name">
            <span class="label_image">
                <img src="<?php echo $url?>/assets/images/icons/home.png" alt="house name">
            </span>
            <input type="text" name="house_name" id="house_name" placeholder="Name of House*" required>
        </label>
        <div>
            <p style="padding-left: 12px">Choose the gender qualified for this house</p>
            <div class="flex">
                <label for="gender" class="radio">
                    <input type="radio" name="gender" id="gender_male" value="Male">
                    <span class="label_title">Male</span>
                </label>
                <label for="gender" class="radio">
                    <input type="radio" name="gender" id="gender_female" value="Female">
                    <span class="label_title">Female</span>
                </label>
            </div>
        </div>
        
        <div class="joint">
            <label for="house_room_total">
                <span class="label_image">
                    <img src="<?php echo $url?>/assets/images/icons/push-outline.svg" alt="total rooms">
                </span>
                <input type="number" name="house_room_total" id="house_room_total" placeholder="Total Number of Rooms*" min="1" required>
            </label>
            <label for="head_per_room">
                <span class="label_image">
                    <img src="<?php echo $url?>/assets/images/icons/people-outline.svg" alt="head per room">
                </span>
                <input type="number" name="head_per_room" id="head_per_room" placeholder="Number of heads per room*" min="1" required>
            </label>
        </div>

        <div class="flex">
            <label for="submit" class="btn">
                <button type="submit" name="submit" value="addHouse">Add House</button>
            </label>
            <label for="cancel" class="btn">
                <button type="reset" name="cancel" value="cancel" onclick="$(this).parents('#modal_3').addClass('no_disp')">Cancel</button>
            </label>
        </div>
    </div>
</form>

<script>
    $("form[name=addHouseForm]").submit(function(e){
        e.preventDefault();

        dataString = $(this).serialize() + "&submit=" + $("button[name=submit]").val() + "_ajax";
        // formSubmit($(this), $("form[name=addHouseForm] button[name=submit]"));

        alert(dataString);
    })
</script>