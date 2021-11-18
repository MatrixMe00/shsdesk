<?php include_once("../../../includes/session.php") ?>
<section class="section_container">
    <div class="content" style="background-color: #007bff;">
        <div class="head">
            <h2>
                <?php
                $res = $connect->query("SELECT id FROM faq");
                echo $res->num_rows;
                ?>
            </h2>
        </div>
        <div class="body">
            <span>Questions Asked</span>
        </div>
    </div>

    <div class="content" style="background-color: #20c997">
        <div class="head">
            <h2>
                <?php
                $res = $connect->query("SELECT id 
                    FROM faq
                    WHERE Answer IS NOT NULL");
                echo $res->num_rows;
                ?>
            </h2>
        </div>
        <div class="body">
            <span>Questions Answered</span>
        </div>
    </div>

    <div class="content" style="background-color: #dc3545">
        <div class="head">
            <h2>
                <?php
                $res = $connect->query("SELECT id 
                    FROM faq 
                    WHERE Answer IS NULL");
                echo $res->num_rows;
                ?>
            </h2>
        </div>
        <div class="body">
            <span>Questions Unanswered</span>
        </div>
    </div>

    <!-- <div class="content" style="background-color: #ffc107">
        <div class="head">
            <h2>0</h2>
        </div>
        <div class="body">
            <span>Vistors today</span>
        </div>
    </div> -->
</section>