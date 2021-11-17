<?php include_once("../../../includes/session.php") ?>
<section class="section_container">
    <div class="content" style="background-color: #007bff;">
        <div class="head">
            <h2>
                <?php
                $res = $connect->query("SELECT indexNumber FROM house_allocation WHERE schoolID = 1");

                echo $res->num_rows;
                ?>
            </h2>
        </div>
        <div class="body">
            <span>Registered Students</span>
        </div>
    </div>

    <div class="content" style="background-color: #20c997">
        <div class="head">
            <h2>
                <?php
                $res = $connect->query("SELECT indexNumber FROM house_allocation WHERE schoolID = 1 AND boardingStatus = TRUE");

                echo $res->num_rows;
                ?>
            </h2>
        </div>
        <div class="body">
            <span>Boarding Students</span>
        </div>
    </div>

    <div class="content" style="background-color: #ffc107">
        <div class="head">
            <h2>
                <?php
                $res = $connect->query("SELECT indexNumber FROM house_allocation WHERE schoolID = 1 AND boardingStatus = FALSE");

                echo $res->num_rows;
                ?>
            </h2>
        </div>
        <div class="body">
            <span>Day Students</span>
        </div>
    </div>
</section>

<section class="section_container allocation flex-column flex-center-align">
    <div class="head">
        <h2>Boarding Students</h2>
    </div>
    <div class="body">
        <?php
            $res = $connect->query("SELECT * FROM house_allocation WHERE schoolID = 1 AND boardingStatus = TRUE");

            if($res->num_rows > 0){
        ?>
        <table>
            <thead>
                <tr>
                    <td>Index Number</td>
                    <td>Full name</td>
                    <td>House</td>
                    <td>Academic Year</td>
                </tr>
            </thead>
            <tbody>
            <?php
                while($row = $res->fetch_assoc()){
            ?>
                <tr>
                    <td>0151223654</td>
                    <td>John Doe</td>
                    <td>Acolatse</td>
                    <td>2021/2022</td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
        <?php }else{
            echo "<p style=\"margin-top: 5px; padding: 5px 10vw; background-color: white; border: 1px dashed lightgrey;\">No user data has been entered yet</p>";
        }?>
    </div>
</section>

<section class="section_container allocation flex-column flex-center-align">
    <div class="head">
        <h2>Day Students</h2>
    </div>
    <div class="body">
    <?php
            $res = $connect->query("SELECT * FROM house_allocation WHERE schoolID = 1 AND boardingStatus = FALSE");

            if($res->num_rows > 0){
        ?>
        <table>
            <thead>
                <tr>
                    <td>Index Number</td>
                    <td>Full name</td>
                    <td>House</td>
                    <td>Academic Year</td>
                </tr>
            </thead>
            <tbody>
            <?php
                while($row = $res->fetch_assoc()){
            ?>
                <tr>
                    <td>0151223654</td>
                    <td>John Doe</td>
                    <td>Acolatse</td>
                    <td>2021/2022</td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
        <?php }else{
            echo "<p style=\"margin-top: 5px; padding: 5px 10vw; background-color: white; border: 1px dashed lightgrey;\">No user data has been entered yet</p>";
        }?>
    </div>
</section>