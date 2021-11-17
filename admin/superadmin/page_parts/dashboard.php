<?php include_once("../../../includes/session.php")?>

<section class="section_container">
    <div class="content" style="background-color: #007bff;">
        <div class="head">
            <h2>
                <?php
                $res = $connect->query("SELECT id FROM schools");
                echo $res->num_rows;
                ?>
            </h2>
        </div>
        <div class="body">
            <span>Schools Registered</span>
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
            <span>Students on System</span>
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
                $res = $connect->query("SELECT id FROM schools WHERE Active = FALSE");
                echo $res->num_rows;
                ?>
            </h2>
        </div>
        <div class="body">
            <span>Schools Deactivated</span>
        </div>
    </div>
</section>