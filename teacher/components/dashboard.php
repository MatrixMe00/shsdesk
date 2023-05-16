<?php include_once("compSession.php"); $_SESSION["active-page"] = "dashboard" ?><section class="d-section lt-shade">
    <div class="img txt-al-c">
        <img src="<?= "$mainRoot/assets/images/icons/city hall.png" ?>" class="rect-xsm" alt="school logo">
    </div>
    <div class="time flex flex-column gap-md txt-al-c">
        <div class="flex flex-space-content txt-fs">
            <span>Current Year: 2021/2022</span>
            <span>Current Term: 1</span>
        </div>
        <div class="flex flex-column">
            <div id="cur_time" class="txt-fl3 txt-bold"></div>
            <div id="date" class="txt-fs">May 14, 2023</div>
        </div>
    </div>
</section>

<section class="flex d-section flex-wrap gap-sm p-lg card-section">
    <div class="card v-card gap-lg purple p-med sm-rnd flex-wrap">
        <span class="">Students</span>
        <span class="txt-fl3 txt-bold">20</span>
    </div>
    <div class="card v-card gap-lg orange p-med sm-rnd flex-wrap">
        <span class="">Class Count</span>
        <span class="txt-fl3 txt-bold"><?= $max = rand(1,10) ?></span>
    </div>
</section>

<section class="d-section flex flex-wrap gap-md p-sm sm-xlg-tp">
    <div id="my_classes" class="lt-shade window gap-xsm">
        <div class="head flex flex-space-content light sp-med">
            <div class="title">
                <span class="txt-bold">Your Classes</span>
            </div>
            <div class="controls">
                <div class="mini" title="Minimize">
                    <span></span>
                </div>
                <div class="maxi" title="Maximize">
                    <span></span>
                </div>
            </div>
        </div>
        <div class="body flex flex-wrap m-sm">
            <?php for($i=1; $i <= $max; $i++) :
                $grade = giveGrade(rand(50, 95));
                $total = rand(20,50);
            ?>
            <div class="sp-med w-full lt-shade">
                <div class="top txt-fl sm-med-b">
                    <span>Classname - Subject</span>
                </div>
                <div class="middle flex flex-column">
                    <span><?= $total ?> Students</span>
                    <span>Average Grade: <?= $grade ?></span>
                </div>
            </div>
            <?php endfor; ?>
        </div>
    </div>

    <div id="best_students" class="lt-shade window gap-xsm">
        <div class="head flex flex-space-content light sp-med">
            <div class="title">
                <span class="txt-bold">Best Students [Top 5]</span>
            </div>
            <div class="controls">
                <div class="mini" title="Minimize">
                    <span></span>
                </div>
                <div class="maxi" title="Maximize">
                    <span></span>
                </div>
            </div>
        </div>
        <div class="body flex flex-wrap txt-al-c m-sm">
            <?php 
                $max = 5;
                $mark = 95;
                for($i = 1; $i <= $max; $i++):
                    $mark = number_format((rand(70,$mark)), 1);
                    $grade = giveGrade($mark);
            ?>
            <div class="sp-med w-full lt-shade">
                <div class="top flex flex-column sm-sm-b">
                    <span>Student Name</span>
                    <span class="txt-fs">- Class Name -</span>
                </div>
                <div class="middle txt-fs">
                    <span>Grade <?= $grade ?> [<?= $mark ?>%]</span>
                </div>
            </div>
            <?php endfor; ?>
        </div>
    </div>
</section>

<section class="d-section lt-shade sm-xlg-t">
    <div class="head txt-al-c">
        <h2>Announcements</h2>
    </div>
    <div class="body sm-med-t m-lg-tp wmax-md sm-auto">
        <div class="sp-med w-full lt-shade-h">
            <h3 class="top">Announcement Head</h3>
            <div class="middle">Lorem ipsum dolor sit amet consectetur adipisicing elit. Consequuntur distinctio nemo ullam ea impedit quod provident et cumque, necessitatibus magni doloremque corrupti. Consectetur a soluta quos modi corporis sit voluptatibus!</div>
        </div>

        <div class="sp-med w-full lt-shade">
            <div class="middle">No Announcements have been made</div>
        </div>
    </div>
</section>

<script>
    $(document).ready(function() {
        function displayTime() {
            var date = new Date();
            var hours = date.getHours();
            var minutes = date.getMinutes();
            var seconds = date.getSeconds();
            var ampm = hours >= 12 ? 'PM' : 'AM';
            
            hours = hours % 12;
            hours = hours ? hours : 12; // If the hour is "0", make it "12"
            minutes = minutes < 10 ? '0' + minutes : minutes;
            seconds = seconds < 10 ? '0' + seconds : seconds;
            
            var timeString = hours + ':' + minutes + ':' + seconds + ' ' + ampm;
            
            $('#cur_time').html(timeString); // Display the time inside the element with ID "cur_time"
        }
        
        setInterval(displayTime, 1000); // Refresh the time every 1 second
    });

    /*$(".window .mini, .window .maxi").click(function(){
        let mini; let maxi;
        if($(this).hasClass("mini")){
            mini = $(this)
            maxi = $(this).siblings("maxi")
        }else{
            maxi = $(this)
            mini = $(this).siblings("mini")
        }

        $([maxi,mini]).toggleClass("no_disp")
    })*/

</script>