<?php @include_once('../../../includes/session.php')?>

<div class="flex flex-column flex-center-align flex-center-content my_loader">
    <div id="getLoader"></div>
    <span class="item-event cancel" style="color: white; margin-top: 10px; padding-left: 10px; text-align: center">Cancel</span>
</div>

<form action="<?php echo $url?>/admin/admin/submit.php" method="POST" name="updateHouseForm" method="post" class="no_disp">
    <div class="head">
        <h2>Update Details For <span id="houseName"></span></h2>
    </div>
    <div class="body">
        <div class="message_box success no_disp">
            <span class="message">Here is a test message</span>
            <div class="close"><span>&cross;</span></div>
        </div>
        <input type="hidden" name="house_id">
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
        
        <div id="male_house" style="display: none">
            <div class="joint">
                <label for="male_house_room_total">
                    <span class="label_image">
                        <img src="<?php echo $url?>/assets/images/icons/push-outline.svg" alt="total rooms">
                    </span>
                    <input type="number" name="male_house_room_total" id="male_house_room_total" placeholder="Total Number of Rooms [male]*" min="1"
                    title="Enter the total number of rooms in the house for males">
                </label>
                <label for="male_head_per_room">
                    <span class="label_image">
                        <img src="<?php echo $url?>/assets/images/icons/people-outline.svg" alt="head per room">
                    </span>
                    <input type="number" name="male_head_per_room" id="male_head_per_room" placeholder="Number of heads per room [male]*" min="1"
                    title="Enter the number of male students required to make a room in the house full">
                </label>
            </div>
        </div>
        
        <div id="female_house" style="display: none">
            <div class="joint">
                <label for="female_house_room_total">
                    <span class="label_image">
                        <img src="<?php echo $url?>/assets/images/icons/push-outline.svg" alt="total rooms">
                    </span>
                    <input type="number" name="female_house_room_total" id="female_house_room_total" placeholder="Total Number of Rooms [female]*" min="1"
                    title="Enter the total number of rooms in the house for females">
                </label>
                <label for="female_head_per_room">
                    <span class="label_image">
                        <img src="<?php echo $url?>/assets/images/icons/people-outline.svg" alt="head per room">
                    </span>
                    <input type="number" name="female_head_per_room" id="female_head_per_room" placeholder="Number of heads per room [female]*" min="1"
                    title="Enter the number of female students required to make a room in the house full">
                </label>
            </div>
        </div>

        <div class="flex">
            <label for="submit" class="btn">
                <button type="submit" name="submit" value="updateHouse">Update House</button>
            </label>
            <label for="cancel" class="btn">
                <button type="reset" name="cancel" value="cancel" onclick="$(this).parents('#modal_1').addClass('no_disp')">Cancel</button>
            </label>
        </div>
    </div>
</form>