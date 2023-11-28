<?php
    include_once("auth.php");

    //set nav_point session
    $_SESSION["nav_point"] = "programs";

    //check if current school is a day school
    $isDay = $stat = getSchoolDetail($user_school_id, true)["residence_status"];
    if($isDay == "day"){
        $isDay = true;
    }else{
        $stat = "boarder";
        $isDay = false;
        $day_studs = (int) fetchData("COUNT(ho.indexNumber) as total",
            ["join"=>"house_allocation houses", "alias" => "ho h", "on" => "houseID id"],
            ["h.schoolID=$user_school_id", "boardingStatus='Day'", "current_data=1"],0,"AND"
        )["total"];
        $displaced_studs = (int) $connect->query(
            "SELECT COUNT(indexNumber) AS total 
                FROM house_allocation 
                WHERE schoolID=$user_school_id AND current_data = 1 AND NOT EXISTS (
                    SELECT 1 
                    FROM houses 
                    WHERE houses.id = house_allocation.houseID 
                    AND houses.schoolID = $user_school_id
                )"
        )->fetch_assoc()["total"];
    }

    $male_allocation = (int) fetchData("COUNT(ho.indexNumber) as total",
        ["join"=>"house_allocation houses", "alias" => "ho h", "on" => "houseID id"],
        ["h.schoolID=$user_school_id", "studentGender='Male'", "boardingStatus='$stat'", "current_data=1"],0,"AND"
    )["total"];
    $female_allocation = (int) fetchData("COUNT(ho.indexNumber) as total",
        ["join"=>"house_allocation houses", "alias" => "ho h", "on" => "houseID id"],
        ["h.schoolID=$user_school_id", "studentGender='Female'", "boardingStatus='$stat'", "current_data=1"],0,"AND"
    )["total"];
?>

<section class="section_container">
    <div class="content" style="background-color: #007bff;">
        <div class="head">
            <h2><?php echo $male_allocation + $female_allocation + (int) $day_studs; ?></h2>
        </div>
        <div class="body">
            <span>Students Registered Houses</span>
        </div>
    </div>

    <div class="content" style="background-color: #20c997">
        <div class="head">
            <h2>
                <?= !$isDay ? ($male_allocation + $female_allocation) : $male_allocation ?>
            </h2>
        </div>
        <div class="body">
            <span><?= !$isDay ? "Boarding Students" : "Males Allocated Houses" ?></span>
        </div>
    </div>

    <?php if($isDay): ?>
    <div class="content" style="background-color: #ffc107">
        <div class="head">
            <h2>
                <?= $female_allocation ?>
            </h2>
        </div>
        <div class="body">
            <span>Females Allocated Houses</span>
        </div>
    </div>
    <?php endif; ?>
    
    <?php if(!$isDay):?>
    <div class="content yellow">
        <div class="head">
            <h2>
                <?= $day_studs ?>
            </h2>
        </div>
        <div class="body">
            <span>Day Students</span>
        </div>
    </div>
    <div class="content red">
        <div class="head">
            <h2>
                <?= $displaced_studs ?>
            </h2>
        </div>
        <div class="body">
            <span>Misplaced Housed Students</span>
        </div>
    </div>
    <?php endif; ?>
</section>

<section>
    <div class="flex flex-wrap flex-center-align w-full flex-eq wmax-sm">
        <div class="btn sm-unset w-full w-full-child">
            <button onclick="$('#modal').removeClass('no_disp')" class="indigo sp-lg">Add New House</button>
        </div>
        <?php if(isset($_SESSION["real_status"]) && $_SESSION["real_status"]){?>
        <div class="btn sm-unset w-full w-full-child">
            <button name="submit" value="houses" class="request_btn cyan sp-lg">Generate Report</button>
        </div>
        <?php } ?>
    </div>
    
</section>

<section>
    <div class="head">
        <h2>House Status [Current Admission]</h2>
    </div>
    <div class="body">
        <?php
            $houses = decimalIndexArray(fetchData(
                [
                    "h.id", "h.title", "h.maleTotalRooms", "h.maleHeadPerRoom", "h.femaleTotalRooms", "h.femaleHeadPerRoom", 
                    "COUNT(ho.indexNumber) as total", "ho.studentGender", "ho.boardingStatus"
                ],
                ["join" => "houses house_allocation", "alias" => "h ho", "on" => "id houseID"],
                ["h.schoolID=$user_school_id", "ho.current_data=1", "(LOWER(ho.boardingStatus)='$stat'", "ho.boardingStatus IS NULL)"], 0, where_binds: ["AND", "AND", "OR"], 
                group_by: [
                    "h.id", "h.title", "h.maleTotalRooms", "h.maleHeadPerRoom", "h.femaleTotalRooms", "h.femaleHeadPerRoom",
                    "h.title","ho.studentGender", "ho.boardingStatus"
                ], join_type: "left outer"
            ));
            
            if(is_array($houses)):
        ?>
        <table class="full">
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
                    foreach($houses as $house) : 
                ?>
                <tr data-item-id="<?php echo $house["id"] ?>">
                    <td><?php echo ++$count ?></td>
                    <td><?php echo $house["title"] ?></td>
                    <td><?php echo $house["studentGender"] ?? "m/f" ?></td>
                    <td><?php echo !is_null($gen = $house["studentGender"]) ? $house[strtolower($gen)."TotalRooms"] : ($house["maleTotalRooms"] ?? $house["femaleTotalRooms"]) ?></td>
                    <td><?php echo !is_null($gen = $house["studentGender"]) ? $house[strtolower($gen)."HeadPerRoom"] : ($house["maleHeadPerRoom"] ?? $house["femaleHeadPerRoom"]) ?></td>
                    <td><?php echo $house["total"] ?></td>
                    <td><?php 
                        if(!is_null($house["studentGender"])){
                            if($house["total"] == ($house["maleHeadPerRoom"] * $house["maleTotalRooms"])){
                                echo "Full";
                            }elseif($house["total"] > ($house["maleHeadPerRoom"] * $house["maleTotalRooms"])){
                                echo "Overboard";
                            }else{
                                echo "Not Full";
                            }
                        }else{
                            echo "Not Full";
                        }                            
                    ?></td>
                    <td class="flex flex-wrap">
                        <span class="item-event edit">Edit</span>
                        <span class="item-event delete">Delete</span>
                    </td>
                </tr>
                <?php endforeach ?>
            </tbody>
        </table>
        <?php elseif($houses = decimalIndexArray(fetchData("*","houses","schoolID=$user_school_id"))): ?>
        <table class="full">
            <thead>
                <tr>
                    <td>No.</td>
                    <td>House Name</td>
                    <td>Gender</td>
                    <td>Rooms</td>
                    <td>Heads Per Room</td>
                </tr>
            </thead>
            <tbody>
                <?php $count = 0; foreach($houses as $house): ?>
                <tr data-item-id="<?php echo $house["id"] ?>">
                    <td><?= ++$count ?></td>
                    <td><?= $house["title"] ?></td>
                    <td><?= strtolower($house["gender"]) == "both" ? "m/f" : ucfirst($house["gender"]) ?></td>
                    <td>
                        <?php 
                            if(strtolower($house["gender"]) == "both"){
                                echo "{$house['maleTotalRooms']} / {$house['femaleTotalRooms']}";
                            }else{
                                echo $house["maleTotalRooms"] ?? $house["femaleTotalRooms"];
                            }
                        ?>
                    </td>
                    <td>
                        <?php 
                            if(strtolower($house["gender"]) == "both"){
                                echo "{$house['maleHeadPerRoom']} / {$house['femaleHeadPerRoom']}";
                            }else{
                                echo $house["maleHeadPerRoom"] ?? $house["femaleHeadPerRoom"];
                            }
                        ?>
                    </td>
                    <td class="flex flex-wrap">
                        <span class="item-event edit">Edit</span>
                        <span class="item-event delete">Delete</span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else:
                echo "<p style=\"margin-top: 5px; padding: 5px; text-align: center; background-color: white; border: 1px dashed lightgrey;\">No data to be displayed</p>";
            endif;
        ?>
    </div>
</section>

<section>
    <div class="head">
        <h2>House Allocation Summary</h2>
    </div>
    <?php 
        $houses_db = decimalIndexArray(fetchData(
            ["h.title", "COUNT(ho.indexNumber) as total", "ho.studentGender", "ho.boardingStatus"],
            ["join" => "houses house_allocation", "alias" => "h ho", "on" => "id houseID"],
            ["h.schoolID=$user_school_id"], 0, group_by: ["h.title","ho.studentGender", "ho.boardingStatus"], join_type: "left"
        ));

        $houses = [];
        
        if($houses_db){
            $house_title = "";
            $ttl_m = $ttl_f = $ttl_b = $ttl_d = 0;

            foreach($houses_db as $house){
                $house_title = $house["title"];
                if(!in_array($house_title, array_column($houses, "title"))){
                    foreach($houses_db as $hs){
                        if($house_title == $hs["title"]){
                            if($hs["studentGender"] == "Male"){
                                $ttl_m += (int) $hs["total"];
                            }else{
                                $ttl_f += (int) $hs["total"];
                            }
    
                            if($hs["boardingStatus"] == "Boarder"){
                                $ttl_b += (int) $hs["total"];
                            }else{
                                $ttl_d += (int) $hs["total"];
                            }
                        }
                    }
                }else{
                    continue;
                }
    
                //store data into houses array
                $houses[] = [
                    "title" => $house_title, "border" => $ttl_b,
                    "males" => $ttl_m, "females" => $ttl_f, "day" => $ttl_d
                ];
    
                //reset variables
                $house_title = "";
                $ttl_m = $ttl_f = $ttl_b = $ttl_d = 0;
            }
        }
    ?>
    <div class="body">
        <table>
            <thead>
                <tr>
                    <td>No.</td>
                    <td>House Name</td>
                    <td>Males</td>
                    <td>Females</td>
                    <td>Boarders</td>
                    <td>Day</td>
                </tr>
            </thead>
            <tbody>
                <?php if(count($houses) > 0): 
                    $count = 1;
                    foreach($houses as $house) :
                ?>
                <tr>
                    <td><?= $count++ ?></td>
                    <td><?= $house["title"] ?></td>
                    <td><?= $house["males"] ?></td>
                    <td><?= $house["females"] ?></td>
                    <td><?= $house["border"] ?></td>
                    <td><?= $house["day"] ?></td>
                </tr>
                <?php endforeach; else: ?>
                <tr class="empty">
                    <td colspan="6">No results to display</td>
                </tr>
                <?php endif;?>
            </tbody>
        </table>
    </div>
</section>

<?php if($displaced_studs > 0): ?>
<section>
    <div class="head">
        <h2>Misplaced Students</h2>
    </div>
    <div class="body">
        <div class="border b-warning txt-al-c sp-med-lr sp-xlg-tp">
            <p>These are students who have registered but their houses have recently been deleted</p>
            <p>You currently have <u><?= $displaced_studs ?></u> students in this category</p>
        </div>
        <div class="btn p-lg sm-auto wmax-3xs w-full w-fluid-child">
            <button class="secondary" id="resolve">Resolve Issue</button>
        </div>
    </div>
</section>
<?php endif; ?>

<div id="modal" class="fixed flex flex-center-content flex-center-align form_modal_box no_disp">
    <?php include_once($rootPath."/admin/admin/page_parts/add_house.php")?>
</div>

<div id="modal_1" class="fixed flex flex-center-content flex-center-align form_modal_box no_disp">
    <?php include_once($rootPath."/admin/admin/page_parts/updateHouse.php")?>
</div>

<?php if(isset($_REQUEST["school_id"])){?>
<script src="<?php echo $url?>/admin/admin/assets/scripts/calledJS/addHouse.min.js?v=<?php echo time()?>"></script>
<script src="<?php echo $url?>/admin/admin/assets/scripts/calledJS/general.min.js?v=<?php echo time()?>"></script>
<?php }else{?>
<script src="<?php echo $url?>/admin/admin/assets/scripts/addHouse.min.js?v=<?php echo time()?>"></script>
<?php } ?>
<script src="<?php echo $url?>/assets/scripts/form/general.min.js?v=<?php echo time()?>"></script>