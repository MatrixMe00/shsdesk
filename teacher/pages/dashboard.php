<?php 
    include_once("compSession.php"); 
    $_SESSION["active-page"] = "dashboard";

    //variable to use within the script
    $result_type = fetchData("school_result","admissiondetails","schoolID=".$teacher["school_id"])["school_result"];
?>
<section class="d-section lt-shade white">
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
                $total_students = 0;
                $classes = fetchData1("DISTINCT(program_id)","teacher_classes","teacher_id={$teacher['teacher_id']}", 0);
                if(is_array($classes)){
                    foreach($classes as $class){
                        if(!empty($class["program_id"])){
                            $total_students += intval(fetchData1("COUNT(indexNumber) as total","students_table", "program_id={$class['program_id']}")["total"]);
                        }else{
                            $total_students += 0;
                        }
                    }
                }
                echo $total_students;
            ?>
        </span>
    </div>
    <div class="card v-card gap-lg orange p-med sm-rnd flex-wrap">
        <span class="">Class Count</span>
        <span class="txt-fl3 txt-bold"><?= fetchData1("COUNT(DISTINCT program_id) as total","teacher_classes","teacher_id={$teacher['teacher_id']}")["total"] ?></span>
    </div>
</section>

<section class="d-section flex flex-wrap gap-md p-sm sm-xlg-tp">
    <div id="my_classes" class="lt-shade white window gap-xsm">
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
                $sql = "SELECT c.course_id, c.course_name, p.program_id, p.program_name, c.short_form as short_c, p.short_form as short_p
                    FROM teacher_classes t JOIN courses c ON t.course_id=c.course_id
                    JOIN program p ON t.program_id=p.program_id
                    WHERE t.teacher_id={$teacher['teacher_id']}";
                $query = $connect2->query($sql);
                if($query->num_rows > 0) : 
                    while($class = $query->fetch_assoc()) :

            ?>
            <div class="sp-med w-full lt-shade">
                <div class="top txt-fl sm-med-b">
                    <span><?= $class["short_p"] ?? $class["program_name"] ?> - <?= $class["short_c"] ?? $class["course_name"] ?></span>
                </div>
                <div class="middle flex flex-column">
                    <span>Student Count: <?= fetchData1("COUNT(indexNumber) as total","students_table","program_id=".$class["program_id"])["total"] ?></span>
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

    <div id="best_students" class="lt-shade white window gap-xsm">
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
                    FROM results r JOIN students_table s ON s.indexNumber=r.indexNumber
                    JOIN program p ON p.program_id=s.program_id
                    JOIN teacher_classes t ON t.program_id=p.program_id
                    WHERE r.teacher_id={$teacher['teacher_id']} AND p.program_id=t.program_id
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
                    <span>Grade <?= giveGrade(round($student["averageMark"],2), $result_type) ?> [<?= round($student["averageMark"],2) ?>%]</span>
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

<section class="d-section lt-shade white sm-xlg-t">
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
            <div class="middle sm-med-tp"><?= html_entity_decode($message["body"]) ?></div>
            <div class="foot">
                <span class="txt-fs color-dark"><?= date("F d, Y H:i:s", strtotime($message["date"])) ?></span>
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
<?php close_connections() ?>