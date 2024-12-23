<?php 
    include_once("auth.php");

    //set nav_point session
    $_SESSION["nav_point"] = "exeat";
?>
<section>
    <form action="<?php echo $url?>/admin/admin/submit.php" method="post" name="exeatForm">
        <div class="head">
            <h2>Provide Student Exeat</h2>
        </div>
        <div class="body">
            <div class="message_box success no_disp">
                <span class="message">Here is a test message</span>
                <div class="close"><span>&cross;</span></div>
            </div>
            <div class="joint">
                <input type="hidden" name="school_id" value="<?php echo $user_school_id?>">
                <label for="student_index">
                    <span class="label_image">
                        <img src="<?php echo $url?>/assets/images/icons/person-outline.svg" alt="name">
                    </span>
                    <input type="text" name="student_index" id="student_index" placeholder="index number Student*"
                    required title="Index number of the student should be delivered here" pattern="[0-9]*" class="text_input">
                </label>
                <label for="exeat_town">
                    <span class="label_image">
                        <img src="<?php echo $url?>/assets/images/icons/home.png" alt="town">
                    </span>
                    <input type="text" name="exeat_town" id="exeat_town" placeholder="Enter the name of the town*" 
                    title="Name of destination town by the student should be entered here" required
                    class="text_input">
                </label>
            </div>
            <div class="joint flex-wrap">
                <label for="exeat_date" class="flex-column flex-align-start flex-content-start date">
                    <span class="label_title">
                        Date for exeat
                    </span>
                    <input type="date" style="width: 100%" name="exeat_date" id="exeat_date" title="Date for exeat" required
                    value="<?php echo date("Y-m-d") ?>" class="text_input">
                </label>
                <label for="return_date" class="flex-column flex-align-start flex-content-start date">
                    <span class="label_title">
                        Date for Returning
                    </span>
                    <input type="date" style="width: 100%" name="return_date" id="return_date" title="Date for returning" required
                    class="text_input">
                </label>
                <label for="exeat_type">
                    <span class="label_image">
                        <img src="<?php echo $url?>/assets/images/icons/logout.png" alt="type">
                    </span>
                    <select name="exeat_type" id="exeat_type" class="text_input">
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

            <label for="submit" class="btn sm-md-t w-full-child w-full wmax-xs">
                <button type="submit" name="submit" value="exeat_request" class="primary sp-lg">Give Exeat</button>
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
        $result = $connect2->query($sql);

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
                <tr data-item-id="<?php echo $row["id"] ?>">
                    <td><?php 
                            $column = "Lastname, Othernames";
                            $table = "students_table";
                            $where = "indexNumber='".$row["indexNumber"]."'";

                            $data = fetchData1($column, $table, $where);

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
                    <td class="ret_date"><?php 
                        if(!is_null($row["returnDate"]))
                            echo date("M j, Y", strtotime($row["returnDate"])) 
                    ?></td>
                    <td class="ret"><?php
                            if($row["returnStatus"] == false){
                                echo "Not Returned";
                            }else{
                                echo "Returned";
                            }
                        ?>
                    </td>
                    <?php if($row["returnStatus"] == false){?>
                    <td class="edit">
                        <span class="item-event sign-return" data-item-id="<?php echo $row["id"]?>">Sign Returned</span>
                    </td>
                    <?php } ?>
                </tr>
                <?php }?>
            </tbody>
        </table>
    </div>
    <div class="foot">
        <div class="btn">
            <button title="Generates a report for the term on exeats made" class="teal request_btn" value="exeat">Generate Report</button>
        </div>
    </div>
    <?php }else{ ?>
    <div class="body empty">
        <p>No exeat has been made</p>
    </div>
    <?php } ?>
</section>

<div id="modal" class="fixed flex flex-center-content flex-center-align form_modal_box no_disp">
    <div class="form" name="studentExeatDetails">
        <div class="head">
            <h2>Exeat Details</h2>
        </div>
        <div class="body">
            <!--Student details-->
            <div class="joint">
                <label class="flex-wrap flex-column" for="indexNumber">
                    <span class="label_title" style="margin-right: 5px;">Index Number</span>
                    <input type="text" name="indexNumber" id="indexNumber" disabled>
                </label>
                <label class="flex-wrap flex-column" for="fullname">
                    <span class="label_title" style="margin-right: 5px;">Full Name</span>
                    <input type="text" name="fullname" id="fullname" disabled>
                </label>
                <label class="flex-wrap flex-column" for="house">
                    <span class="label_title" style="margin-right: 5px;">Student's House</span>
                    <input type="text" name="house" id="house" disabled>
                </label>
            </div>
            
            <!--Exeat details-->
            <div class="joint">
                <label class="flex-wrap flex-column" for="exeat_town">
                    <span class="label_title" style="margin-right: 5px;">Town vouched for</span>
                    <input type="text" name="exeat_town" id="exeat_town" disabled>
                </label>
                <label class="flex-wrap flex-column" for="exeat_date">
                    <span class="label_title" style="margin-right: 5px;">Date exeat was taken</span>
                    <input type="text" name="exeat_date" id="exeat_date" disabled>
                </label>
                <label class="flex-wrap flex-column" for="exp_date">
                    <span class="label_title" style="margin-right: 5px;">Expected return date</span>
                    <input type="text" name="exp_date" id="exp_date" disabled>
                </label>
                <label class="flex-wrap flex-column" for="ret_date" style="display: none">
                    <span class="label_title" style="margin-right: 5px;">Date student returned</span>
                    <input type="text" name="ret_date" id="ret_date" disabled>
                </label>
            </div>
            
            <!--Other details-->
            <label class="flex-wrap flex-column" for="exeat_reason">
                <span class="label_title" style="margin-right: 5px;">Reason for exeat</span>
                <div name="exeat_reason"style="background-color: #eee; padding: 5px; width: 100%">
                    <span id="exeat_reason"></span>
                </div>
            </label>

            <div class="joint">
                <label for="issueBy">
                    <span class="label_title" style="padding-right: 5px;">Issued By</span>
                    <input type="text" name="issueBy" id="issueBy" disabled>
                </label>
                <label for="returnStatus">
                    <span class="label_title" style="padding-right: 5px;">Return Status</span>
                    <input type="text" name="returnStatus" id="returnStatus" disabled>
                </label>
            </div>
            
        </div>
        <div class="foot">
            <div class="btn">
                <button type="reset" name="cancel">Cancel</button>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo $url?>/admin/admin/assets/scripts/exeat.min.js?v=<?php echo time()?>" async></script>
<script src="<?php echo $url?>/assets/scripts/form/general.min.js?v=<?php echo time()?>" async></script>
<?php close_connections() ?>