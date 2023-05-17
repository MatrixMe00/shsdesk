<?php 
    include_once("compSession.php"); 
    $_SESSION["active-page"] = "dashboard";

    //useful variables in this page
    $course_ids = explode(" ", $teacher["course_id"]);
    $program_ids = explode(" ", $teacher["program_ids"]);
    $result_type = fetchData("school_result","admissiondetails","schoolID=".$teacher["school_id"])["school_result"];
?>
<section class="d-section lt-shade">
    <div class="img txt-al-c">
        <img src="<?= "$mainRoot/assets/images/icons/city hall.png" ?>" class="rect-xsm" alt="school logo">
    </div>
    <div class="time flex flex-column gap-md txt-al-c">
        <div class="flex flex-space-content txt-fs">
            <span>Current Year: <?= fetchData("academicYear","admissiondetails","schoolID=".$teacher["school_id"])["academicYear"] ?></span>
            <span>Current Term: 1</span>
        </div>
        <div class="flex flex-column">
            <div id="cur_time" class="txt-fl3 txt-bold"></div>
            <div id="date" class="txt-fs"><?= date("F d, Y") ?></div>
        </div>
    </div>
</section>

<section class="flex d-section flex-wrap gap-sm p-lg card-section">
    <div class="card v-card gap-lg purple p-med sm-rnd flex-wrap">
        <span class="">Students</span>
        <span class="txt-fl3 txt-bold">
            <?php             
                $sql = "SELECT COUNT(s.indexNumber) AS total
                    FROM students_table s
                    JOIN program p ON s.program_id = p.program_id
                    JOIN teachers t ON p.school_id = t.school_id
                    WHERE CONCAT(p.program_id, ' ') IN (" . implode(' ', $program_ids) . ")";
                $query = $connect2->query($sql);
                echo $query->fetch_assoc()["total"];
            ?>
        </span>
    </div>
    <div class="card v-card gap-lg orange p-med sm-rnd flex-wrap">
        <span class="">Class Count</span>
        <span class="txt-fl3 txt-bold"><?= count($program_ids)-1 ?></span>
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
            <?php 
                $sql = "SELECT p.program_id, p.program_name, p.short_form as short_p, c.course_name, c.course_id, c.short_form as short_c
                    FROM courses c
                    JOIN program p ON p.school_id = c.school_id
                    WHERE ('".implode(' ',$course_ids)."' LIKE CONCAT('%',c.course_id, ' ', '%')) AND ('".implode(" ",$program_ids)."' LIKE CONCAT('%',p.program_id, ' ', '%'))";
                $query = $connect2->query($sql);
                if($query->num_rows > 0) : 
                    while($class = $query->fetch_assoc()) :

            ?>
            <div class="sp-med w-full lt-shade">
                <div class="top txt-fl sm-med-b">
                    <span><?= $class["program_name"] ?> - <?= $class["course_name"] ?></span>
                </div>
                <div class="middle flex flex-column">
                    <span><?= fetchData1("COUNT(indexNumber) as total","students_table","program_id=".$class["program_id"])["total"] ?> Students</span>
                    <span>Average Grade: <?php 
                        $average = fetchData1("AVG(mark) as averageMark","results","course_id=".$class["course_id"]." AND teacher_id=".$teacher["teacher_id"])["averageMark"];
                        echo giveGrade($average, $result_type)
                    ?></span>
                </div>
            </div>
            <?php endwhile;
                else: ?>
            <div class="sp-med w-full lt-shade p-lg txt-al-c">
                <p>You have not been added to any class yet</p>
            </div>
            <?php endif; ?>
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
                $sql = "SELECT CONCAT(s.Lastname, ' ', s.Othernames) as student_name, p.program_name, AVG(r.mark) as averageMark
                    FROM students_table s JOIN results r ON s.indexNumber=r.indexNumber
                    JOIN program p ON p.program_id=s.program_id
                    JOIN teachers t ON t.school_id=p.school_id
                    WHERE '".implode(" ",$program_ids)."' LIKE CONCAT('%',s.program_id,' ','%')
                    GROUP BY s.indexNumber ORDER BY averageMark DESC LIMIT 5
                ";
                $query = $connect2->query($sql);
                if($query->num_rows > 0) :
                    while($student = $query->fetch_assoc()) :
            ?>
            <div class="sp-med w-full lt-shade">
                <div class="top flex flex-column sm-sm-b">
                    <span><?= ucwords($student["student_name"]) ?></span>
                    <span class="txt-fs">- <?= $student["program_name"] ?> -</span>
                </div>
                <div class="middle txt-fs">
                    <span>Grade <?= giveGrade($student["averageMark"], $result_type) ?> [<?= $student["averageMark"] ?>%]</span>
                </div>
            </div>
            <?php endwhile; else: ?>
            <div class="sp-med w-full lt-shade p-lg txt-al-c">
                <p>No student data to be displayed yet</p>
            </div>
            <?php endif;  ?>
        </div>
    </div>
</section>

<section class="d-section lt-shade sm-xlg-t">
    <div class="head txt-al-c">
        <h2>Announcements</h2>
    </div>
    <div class="body sm-med-t m-lg-tp wmax-md sm-auto">
        <?php 
            $sql = "SELECT heading, body, date 
                FROM announcement WHERE school_id = {$teacher['school_id']} AND audience IN ('teachers', 'all')";
            $query = $connect2->query($sql);

            if($query->num_rows > 0) :
                while($message = $query->fetch_assoc()) :
        ?>
        <div class="sp-med w-full lt-shade-h">
            <h3 class="top"><?= $message["heading"] ?></h3>
            <div class="middle sm-med-tp"><?= $message["body"] ?></div>
            <div class="foot">
                <span class="txt-fs color-dark"><?= date("F d, Y", strtotime($message["date"])) ?></span>
            </div>
        </div>
        <?php endwhile; else: ?>
        <div class="sp-med w-full p-lg txt-al-c lt-shade">
            <div class="middle">No Announcements have been made</div>
        </div>
        <?php endif; ?>
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