<?php include_once("../../../includes/session.php");

    //set nav_point session
    $_SESSION["nav_point"] = "House";
?>

<section class="section_container">
    <div class="content" style="background-color: #007bff;">
        <div class="head">
            <h2>
                <?php
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
                    $res = $connect->query("SELECT indexNumber FROM house_allocation WHERE schoolID = $user_school_id AND studentGender='Male' AND boardingStatus = 'Boarder'");
                    
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
                    $res = $connect->query("SELECT indexNumber FROM house_allocation WHERE schoolID = $user_school_id AND studentGender='Female' AND boardingStatus = 'Boarder'");
                    
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
                    $res = $connect->query("SELECT indexNumber FROM house_allocation WHERE schoolID = $user_school_id AND boardingStatus = 'Day'");
                    
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
        <button onclick="$('#modal').removeClass('no_disp')" class="secondary">Add New House</button>
    </div>
    <?php if($_SESSION["real_status"]){?>
    <div class="btn">
        <button name="submit" value="houses" class="request_btn cyan">Generate Report</button>
    </div>
    <?php } ?>
</section>

<section>
    <div class="head">
        <h2>List of Houses in your school</h2>
    </div>
    <div class="body">
        <?php
            $res = $connect->query("SELECT * FROM houses WHERE schoolID = $user_school_id");

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
                    <td>Occupants</td>
                    <td>Status</td>
                </tr>
            </thead>
            <tbody>
                <?php 
                    $count = 0;
                    while($row=$res->fetch_assoc()){?>
                <tr data-item-id="<?php echo $row["id"] ?>">
                    <td><?php echo ++$count ?></td>
                    <td><?php echo $row["title"] ?></td>
                    <td><?php echo $row["gender"] ?></td>
                    <td><?php echo $row["totalRooms"] ?></td>
                    <td><?php echo $row["headPerRoom"] ?></td>
                    <td>
                        <?php 
                            $sql = "SELECT COUNT(indexNumber) AS total
                                    FROM house_allocation
                                    WHERE schoolID=$user_school_id AND houseID=".$row["id"]." AND boardingStatus = 'Boarder'";
                            $query = $connect->query($sql);
                            
                            $tot = $query->fetch_assoc()["total"];
                            echo $tot;
                        ?>
                    </td>
                    <td><?php 
                        if($tot == ($row["headPerRoom"] * $row["totalRooms"])){
                            echo "Full";
                        }else{
                            echo "Not Full";
                        }
                    ?></td>
                    <td class="flex flex-wrap">
                        <span class="item-event edit">Edit</span>
                        <span class="item-event delete">Delete</span>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
        <?php }else{
                echo "<p style=\"margin-top: 5px; padding: 5px; text-align: center; background-color: white; border: 1px dashed lightgrey;\">No data to be displayed</p>";
            }
        ?>
    </div>
</section>

<div id="modal" class="fixed flex flex-center-content flex-center-align form_modal_box no_disp">
    <?php include_once($rootPath."/admin/admin/page_parts/add_house.php")?>
</div>

<div id="modal_1" class="fixed flex flex-center-content flex-center-align form_modal_box no_disp">
    <?php include_once($rootPath."/admin/admin/page_parts/updateHouse.php")?>
</div>

<script src="<?php echo $url?>/admin/admin/assets/scripts/addHouse.js?v=<?php echo time()?>"></script>
<script src="<?php echo $url?>/assets/scripts/form/general.js?v=<?php echo time()?>"></script>