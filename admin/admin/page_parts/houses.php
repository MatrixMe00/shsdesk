<?php
if(isset($_REQUEST["school_id"]) && !empty($_REQUEST["school_id"])){
    $user_school_id = $_REQUEST["school_id"];
    $user_details = getUserDetails($_REQUEST["user_id"]);
    
    include_once("../../includes/session.php");
}else{
    include_once("../../../includes/session.php");

    //set nav_point session
    $_SESSION["nav_point"] = "House";
    }

    //check if current school is a day school
    $isDay = getSchoolDetail($user_school_id, true)["residence_status"];
    if($isDay == "day"){
        $isDay = true;
    }else{
        $isDay = false;
    }
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
                    if($isDay){
                        $res = $connect->query("SELECT indexNumber FROM house_allocation WHERE schoolID = $user_school_id AND studentGender='Male' AND boardingStatus = 'Day'");
                    }else{
                        $res = $connect->query("SELECT indexNumber FROM house_allocation WHERE schoolID = $user_school_id AND studentGender='Male' AND boardingStatus = 'Boarder'");
                    }                    
                    
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
                    if($isDay){
                        $res = $connect->query("SELECT indexNumber FROM house_allocation WHERE schoolID = $user_school_id AND studentGender='Female' AND boardingStatus = 'Day'");
                    }else{
                        $res = $connect->query("SELECT indexNumber FROM house_allocation WHERE schoolID = $user_school_id AND studentGender='Female' AND boardingStatus = 'Boarder'");
                    }                    
                    
                    echo $res->num_rows;
                ?>
            </h2>
        </div>
        <div class="body">
            <span>Females Allocated Houses</span>
        </div>
    </div>
    
    <?php if(!$isDay){?><div class="content" style="background-color: #dc3545">
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
    </div><?php } ?>
</section>

<section class="flex flex-wrap flex-center-align">
    <div class="btn">
        <button onclick="$('#modal').removeClass('no_disp')" class="secondary">Add New House</button>
    </div>
    <?php if(isset($_SESSION["real_status"]) && $_SESSION["real_status"]){?>
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
                    while($row=$res->fetch_assoc()){
                    
                    if($row["gender"] == "Both"){
                ?>
                <tr data-item-id="<?php echo $row["id"] ?>">
                    <td><?php echo ++$count ?></td>
                    <td><?php echo $row["title"] ?></td>
                    <td><?php echo "Male" ?></td>
                    <td><?php echo $row["maleTotalRooms"] ?></td>
                    <td><?php echo $row["maleHeadPerRoom"] ?></td>
                    <td>
                        <?php
                            if($isDay){
                                $sql = "SELECT COUNT(indexNumber) AS total
                                    FROM house_allocation
                                    WHERE schoolID=$user_school_id AND houseID=".$row["id"]." AND boardingStatus = 'Day' AND studentGender='Male'";
                            }else{
                                $sql = "SELECT COUNT(indexNumber) AS total
                                    FROM house_allocation
                                    WHERE schoolID=$user_school_id AND houseID=".$row["id"]." AND boardingStatus = 'Boarder' AND studentGender='Male'";
                            }
                            
                            $query = $connect->query($sql);
                            
                            $tot = $query->fetch_assoc()["total"];
                            echo $tot;
                        ?>
                    </td>
                    <td><?php 
                        if($tot == ($row["maleHeadPerRoom"] * $row["maleTotalRooms"])){
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

                <tr data-item-id="<?php echo $row["id"] ?>">
                    <td><?php echo ++$count ?></td>
                    <td><?php echo $row["title"] ?></td>
                    <td><?php echo "Female" ?></td>
                    <td><?php echo $row["femaleTotalRooms"] ?></td>
                    <td><?php echo $row["femaleHeadPerRoom"] ?></td>
                    <td>
                        <?php 
                            if($isDay){
                                $sql = "SELECT COUNT(indexNumber) AS total
                                    FROM house_allocation
                                    WHERE schoolID=$user_school_id AND houseID=".$row["id"]." AND boardingStatus = 'Boarder' AND studentGender='Female'";
                            }else{
                                $sql = "SELECT COUNT(indexNumber) AS total
                                    FROM house_allocation
                                    WHERE schoolID=$user_school_id AND houseID=".$row["id"]." AND boardingStatus = 'Boarder' AND studentGender='Female'";
                            }
                            
                            $query = $connect->query($sql);
                            
                            $tot = $query->fetch_assoc()["total"];
                            echo $tot;
                        ?>
                    </td>
                    <td><?php 
                        if($tot == ($row["femaleHeadPerRoom"] * $row["femaleTotalRooms"])){
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
                <?php
                    }else{
                ?>
                <tr data-item-id="<?php echo $row["id"] ?>">
                    <?php 
                        $totalRooms = 0;
                        $headPerRoom = 0;

                        if($row["gender"] == "Male"){
                            $totalRooms = $row["maleTotalRooms"];
                            $headPerRoom = $row["maleHeadPerRoom"];
                        }else{
                            $totalRooms = $row["femaleTotalRooms"];
                            $headPerRoom = $row["femaleHeadPerRoom"];
                        }
                    ?>
                    <td><?php echo ++$count ?></td>
                    <td><?php echo $row["title"] ?></td>
                    <td><?php echo $row["gender"] ?></td>
                    <td><?php echo $totalRooms ?></td>
                    <td><?php echo $headPerRoom ?></td>
                    <td>
                        <?php 
                            if($isDay){
                                $sql = "SELECT COUNT(indexNumber) AS total
                                    FROM house_allocation
                                    WHERE schoolID=$user_school_id AND houseID=".$row["id"]." AND boardingStatus = 'Day'";
                            }else{
                                $sql = "SELECT COUNT(indexNumber) AS total
                                    FROM house_allocation
                                    WHERE schoolID=$user_school_id AND houseID=".$row["id"]." AND boardingStatus = 'Boarder'";
                            }
                            
                            $query = $connect->query($sql);
                            
                            $tot = $query->fetch_assoc()["total"];
                            echo $tot;
                        ?>
                    </td>
                    <td><?php 
                        if($tot == ($headPerRoom * $totalRooms)){
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

<?php if(isset($_REQUEST["school_id"])){?>
<script src="<?php echo $url?>/admin/admin/assets/scripts/calledJS/addHouse.js?v=<?php echo time()?>" async></script>
<script src="<?php echo $url?>/admin/admin/assets/scripts/calledJS/general.js?v=<?php echo time()?>" async></script>
<?php }else{?>
<script src="<?php echo $url?>/admin/admin/assets/scripts/addHouse.js?v=<?php echo time()?>" async></script>
<?php } ?>
<script src="<?php echo $url?>/assets/scripts/form/general.js?v=<?php echo time()?>" async></script>