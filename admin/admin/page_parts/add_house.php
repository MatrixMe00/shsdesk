<form action="<?php echo $url?>/admin/admin/submit.php" method="POST" name="addHouseForm" method="post">
    <div class="head">
        <h2>Add A New House</h2>
    </div>
    <div class="body">
        <div id="message_box" class="success no_disp">
            <span class="message">Here is a test message</span>
            <div class="close"><span>&cross;</span></div>
        </div>
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
                <label for="gender" class="radio">
                    <input type="radio" name="gender" id="gender_both" value="Both">
                    <span class="label_title">Both</span>
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

        response = formSubmit($(this), $("form[name=addHouseForm] button[name=submit]"));

        if(response == true){
            message = "House has been added";
            type = "success";

            //close box and refresh page
            setTimeout(function(){
                $("form[name=addHouseForm] button[name=cancel]").click();
                $('#lhs .menu .item.active').click();
            },6000)
            
        }else{
            type = "error";

            if(response == "no-house-name"){
                message = "House name field is empty";
            }else if(response == "no-gender"){
                message = "Please select a gender type";
            }else if(response == "room-total-empty"){
                message = "Total rooms field is empty";
            }else if(response == "room-zero"){
                message = "Total number of rooms cannot be less than 1";
            }else if(response == "head-total-empty"){
                message = "Heads per room field is empty";
            }else if(response == "head-zero"){
                message = "Heads per room cannot be less than 1";
            }
        }

        messageBoxTimeout("addHouseForm",message, type);
    })
</script>