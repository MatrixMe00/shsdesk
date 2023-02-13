<?php @include_once('../../../includes/session.php')?>

<form action="<?php echo $url?>/admin/admin/submit.php" method="POST" name="addHouseForm" method="post">
    <div class="head">
        <h2>Add A New House</h2>
    </div>
    <div class="body">
        <div class="message_box success no_disp">
            <span class="message">Here is a test message</span>
            <div class="close"><span>&cross;</span></div>
        </div>
        <input type="hidden" name="school_id" value="<?php echo $user_school_id?>">
        <label for="house_name">
            <span class="label_image">
                <img src="<?php echo $url?>/assets/images/icons/home.png" alt="house name">
            </span>
            <input type="text" name="house_name" id="house_name" placeholder="Name of House*" required
            title="Please enter the name of the house in the field">
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
    </div>
    <div class="foot flex-all-center w-full-child">
        <div class="flex flex-wrap w-fluid-child flex-eq gap-sm wmax-xs">
            <label for="submit" class="btn sm-unset sp-unset w-fluid-child">
                <button type="submit" name="submit" class="primary sp-med" value="addHouse">Add House</button>
            </label>
            <label for="close" class="btn sm-unset sp-unset w-fluid-child">
                <button type="reset" name="cancel" value="cancel" onclick="$(this).parents('#modal').addClass('no_disp')" class="secondary sp-med">Cancel</button>
            </label>
        </div>
    </div>
</form>