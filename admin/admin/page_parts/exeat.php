<?php include_once("../../../includes/session.php");

    //set nav_point session
    $_SESSION["nav_point"] = "exeat";
?>
<section>
    <form action="<?php echo $url?>/admin/admin/submit.php" method="post" name="exeatForm">
        <div class="head">
            <h2>Add A Student</h2>
        </div>
        <div class="body">
            <div class="message_box success no_disp">
                <span class="message">Here is a test message</span>
                <div class="close"><span>&cross;</span></div>
            </div>
            <div class="joint">
                <label for="student_index">
                    <span class="label_image">
                        <img src="<?php echo $url?>/assets/images/icons/person-outline.svg" alt="name">
                    </span>
                    <input type="text" name="student_index" id="student_index" placeholder="index number Student*"
                    required title="Index number of the student should be delivered here" pattern="[0-9]*">
                </label>
                <label for="exeat_town">
                    <span class="label_image">
                        <img src="<?php echo $url?>/assets/images/icons/home.png" alt="town">
                    </span>
                    <input type="text" name="exeat_town" id="exeat_town" placeholder="Enter the name of the town*" 
                    title="Name of destination town by the student should be entered here" required>
                </label>
            </div>
            <div class="joint">
                <label for="exeat_date" class="flex-column flex-align-start flex-content-start date">
                    <span class="label_title">
                        Date for exeat
                    </span>
                    <input type="date" name="exeat_date" id="exeat_date" title="Date for exeat" required
                    value="<?php echo date("Y-m-d") ?>">
                </label>
                <label for="return_date" class="flex-column flex-align-start flex-content-start date">
                    <span class="label_title">
                        Date for Returning
                    </span>
                    <input type="date" name="return_date" id="return_date" title="Date for exeat" required>
                </label>
                <label for="exeat_type">
                    <span class="label_image">
                        <img src="<?php echo $url?>/assets/images/icons/logout.png" alt="type">
                    </span>
                    <select name="exeat_type" id="exeat_type">
                        <option value="">Select type of exeat</option>
                        <option value="Internal">Internal Exeat</option>
                        <option value="External">External Exeat</option>
                    </select>
                </label>                
            </div>
            <label for="exeat_reason">
                <span class="label_image">
                    <img src="<?php echo $url?>/assets/images/icons/home.png" alt="reason">
                </span>
                <textarea name="exeat_reason" id="exeat_reason" placeholder="Reason for exeat [maximum of 80 characters]*" required 
                title="Provide a reason for the exeat" maxlength="80"></textarea>
            </label>

            <label for="submit" class="btn">
                <button type="submit" name="submit" value="exeat_request">Give Exeat</button>
            </label>
        </div>
    </form>
</section>

<section>
    <div class="head">
        <h2>Students on exeat</h2>
    </div>
    <?php 
        $sql = "SELECT * FROM exeat WHERE school_id = $user_school_id";
        $result = $connect->query($sql);

        if($result->num_rows > 0){
    ?>
    <div class="body">
        <table>
            <thead>
                <tr>
                    <td>Name</td>
                    <td>House</td>
                    <td>Town</td>
                    <td>Leave Date</td>
                    <td>Expected Return</td>
                    <td>Return Date</td>
                    <td>Status</td>  
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()){ ?>
                <tr>
                    <td><?php 
                            $column = "Lastname, Othernames";
                            $table = "cssps";
                            $where = "indexNumber='".$row["indexNumber"]."'";

                            $data = fetchData($column, $table, $where);

                            echo $data["Lastname"]." ".$data["Othernames"];
                        ?>
                    </td>
                    <td><?php 
                            $column = "title";
                            $table = "houses";
                            $where = "id=".$row["houseID"];

                            $data = fetchData($column, $table, $where);

                            echo $data["title"];
                        ?>
                    </td>
                    <td><?php echo $row["exeatTown"] ?></td>
                    <td><?php echo date("M j, Y", strtotime($row["exeatDate"])) ?></td>
                    <td><?php echo date("M j, Y", strtotime($row["expectedReturn"])) ?></td>
                    <td><?php echo date("M j, Y", strtotime($row["returnDate"])) ?></td>
                    <td><?php
                            if($row["returnStatus"] == false){
                                echo "Not Returned";
                            }else{
                                echo "Returned";
                            }
                        ?>
                    </td>
                    <?php if($row["returnStatus"] == false){?>
                    <td class="edit">
                        <span class="item-event" data-item-id="<?php echo $row["id"]?>">Sign Returned</span>
                    </td>
                    <?php } ?>
                </tr>
                <?php }?>
            </tbody>
        </table>
    </div>
    <div class="foot">
        <div class="btn">
            <button title="Generates a report for the term on exeats made">Generate Report</button>
        </div>
    </div>
    <?php }else{ ?>
    <div class="body empty">
        <p>No exeat has been made</p>
    </div>
    <?php } ?>
</section>

<script src="<?php echo $url?>/admin/admin/assets/scripts/exeat.js"></script>