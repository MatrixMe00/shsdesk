<?php include_once("../../../includes/session.php")?>
<section class="section_container">
    <div class="content" style="background-color: #007bff;">
        <div class="head">
            <h2>
                <?php
                    $user_school_id = 1;
                    $res = $connect->query("SELECT indexNumber FROM house_allocation WHERE schoolID = $user_school_id");
                    
                    echo $res->num_rows;
                ?>
            </h2>
        </div>
        <div class="body">
            <span>Students Registered</span>
        </div>
    </div>

    <div class="content" style="background-color: #20c997">
        <div class="head">
            <h2>
                <?php
                    $res = $connect->query("SELECT indexNumber FROM house_allocation WHERE schoolID = $user_school_id AND studentGender='Male' AND boardingStatus = TRUE");
                    
                    echo $res->num_rows;
                ?>
            </h2>
        </div>
        <div class="body">
            <span>Males Allocated Houses</span>
        </div>
    </div>

    <div class="content" style="background-color: #ffc107">
        <div class="head">
            <h2>
                <?php
                    $res = $connect->query("SELECT indexNumber FROM house_allocation WHERE schoolID = $user_school_id AND studentGender='Female' AND boardingStatus = TRUE");
                    
                    echo $res->num_rows;
                ?>
            </h2>
        </div>
        <div class="body">
            <span>Females Allocated Houses</span>
        </div>
    </div>

    <div class="content" style="background-color: #dc3545">
        <div class="head">
            <h2>
                <?php
                    $user_school_id = 1;
                    $res = $connect->query("SELECT indexNumber FROM house_allocation WHERE schoolID = $user_school_id AND boardingStatus = FALSE");
                    
                    echo $res->num_rows;
                ?>
            </h2>
        </div>
        <div class="body">
            <span>Day Students</span>
        </div>
    </div>
</section>

<section class="flex flex-wrap flex-center-align">
    <div class="btn">
        <button onclick="$('#modal_3').removeClass('no_disp')">Add New House</button>
    </div>
    <div class="btn">
        <button>Generate Report</button>
    </div>
</section>

<section>
    <div class="head">
        <h2>List of Houses in your school</h2>
    </div>
    <div class="body">
        <?php
            $res = $connect->query("SELECT id FROM houses WHERE schoolID = $user_school_id");

            if($res->num_rows){
        ?>
        <table>
            <thead>
                <tr>
                    <td>No.</td>
                    <td>House Name</td>
                    <td>Gender</td>
                    <td>Rooms</td>
                    <td>Heads Per Room</td>
                    <td>Occupancy</td>
                    <td>Status</td>
                </tr>
            </thead>
            <tbody>
                <?php while($row=$res->fetch_assoc()){?>
                <tr>
                    <td>1</td>
                    <td>Acolatse</td>
                    <td>Females</td>
                    <td>15</td>
                    <td>30</td>
                    <td>18</td>
                    <td>Not Full</td>
                </tr>
                <?php } ?>
                <!-- <tr>
                    <td>2</td>
                    <td>Vodzi</td>
                    <td>Males</td>
                    <td>15</td>
                    <td>30</td>
                    <td>25</td>
                    <td>Not Full</td>
                </tr> -->
            </tbody>
        </table>
        <?php }else{
                echo "<p style=\"margin-top: 5px; padding: 5px; text-align: center; background-color: white; border: 1px dashed lightgrey;\">No data to be displayed</p>";
            }
        ?>
    </div>
</section>