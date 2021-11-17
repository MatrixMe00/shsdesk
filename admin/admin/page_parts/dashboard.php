<?php require_once("../../../includes/session.php")?>
<section class="section_container">
    <div class="content" style="background-color: #007bff;">
        <div class="head">
            <h2>
                <?php
                    $res = $connect->query("SELECT enrolDate FROM enrol_table");
                    $i = 0;

                    if($res->num_rows > 0){
                        while($row = $res->fetch_assoc()){
                            if(date('l',strtotime($row['enrolDate'])) == date('l')){
                                $i += 1;
                            }else{
                                continue;
                            }
                        }
                    }

                    echo $i;
                ?>
            </h2>
        </div>
        <div class="body">
            <span>New Registers this week</span>
        </div>
    </div>

    <div class="content" style="background-color: #20c997">
        <div class="head">
            <h2>
                <?php
                    $res = $connect->query("SELECT indexNumber FROM enrol_table");
                    
                    echo $res->num_rows;
                ?>
            </h2>
        </div>
        <div class="body">
            <span>Students Registered</span>
        </div>
    </div>

    <div class="content" style="background-color: #ffc107">
        <div class="head">
            <h2>0</h2>
        </div>
        <div class="body">
            <span>Vistors today</span>
        </div>
    </div>

    <div class="content" style="background-color: #dc3545">
        <div class="head">
            <h2>
                <?php
                    $res = $connect->query("SELECT enrolment_code FROM cssps WHERE expired = FALSE");
                    
                    echo $res->num_rows;
                ?>
            </h2>
        </div>
        <div class="body">
            <span>Students left to register</span>
        </div>
    </div>
</section>

<section class="section_container">
    <div class="content" style="background-color: #17a2b8;">
        <div class="head">
            <h2>
                <?php
                    $res = $connect->query("SELECT indexNumber FROM enrol_table WHERE interest = 'Athletics'");
                    
                    echo $res->num_rows;
                ?>
            </h2>
        </div>
        <div class="body">
            <span>Interested in Athletics</span>
        </div>
    </div>

    <div class="content" style="background-color: #28a745">
        <div class="head">
            <h2>
                <?php
                    $res = $connect->query("SELECT indexNumber FROM enrol_table WHERE interest = 'Football'");
                    
                    echo $res->num_rows;
                ?>
            </h2>
        </div>
        <div class="body">
            <span>Interested in Football</span>
        </div>
    </div>

    <div class="content" style="background-color: #fd7e14">
        <div class="head">
            <h2>
                <?php
                    $res = $connect->query("SELECT indexNumber FROM enrol_table WHERE interest = 'Debate Club'");
                    
                    echo $res->num_rows;
                ?>
            </h2>
        </div>
        <div class="body">
            <span>Interested in Debate Club</span>
        </div>
    </div>

    <div class="content" style="background-color: #6610f2">
        <div class="head">
            <h2>
                <?php
                    $res = $connect->query("SELECT indexNumber FROM enrol_table WHERE interest = 'Others'");
                    
                    echo $res->num_rows;
                ?>
            </h2>
        </div>
        <div class="body">
            <span>Have other interests</span>
        </div>
    </div>
</section>