<?php require_once "compSession.php"; $_SESSION["active-page"] = "subject" ?>
<section class="flex d-section flex-wrap gap-sm p-lg card-section">
    <div class="card v-card gap-lg primary sm-rnd flex-wrap">
        <span class="self-align-start">Total Subjects</span>
        <span class="txt-fl3 txt-bold self-align-end"><?php 
            if(!is_null($student["program_id"])){
                $course_total = countProgramSubjects($student["program_id"]);

                if($course_total === false){
                    echo "<span class='txt-fl2'>No courses assigned</span>";
                }else{
                    echo $course_total;
                }
            }else{
                echo "<span class='txt-fl2'>No class assigned</span>";
            }
        ?></span>
    </div>
    <div class="card v-card gap-lg orange sm-rnd flex-wrap">
        <span class="self-align-start">Total Program Teachers (Registered)</span>
        <span class="txt-fl3 txt-bold self-align-end"><?php 
            if(!is_null($student["program_id"])){
                echo fetchData1("COUNT(DISTINCT teacher_id) as total","teacher_classes","program_id={$student['program_id']}")["total"];
            }else{
                echo "<span class='txt-fl2'>No class assigned</span>";
            }
            
        ?></span>
    </div>
</section>

<input type="hidden" name="student_index" id="student_index" value="<?= $student["indexNumber"] ?>">

<section class="d-section white">
    <h1 class="sm-lg-b">Registered Courses</h1>
    <table class="full" id="subject_table">
        <thead>
            <td>ID</td>
            <td>Subject</td>
            <td>Subject Alias</td>
            <td>Teacher</td>
            <td>Credit Hours</td>
        </thead>
        <tbody>
            <?php 
                if(is_null($student["program_id"])) :
            ?>
            <tr class="empty">
                <td colspan="5" class="txt-al-c sp-xxlg-tp">Your program has not been uploaded yet. Please contact your school administrator for aid.</td>
            </tr>
            <?php else :
                // $subjects = getProgramSubjectNTeachers($student["program_id"]);
                $table_columns = ["course_id","course_name","short_form", "credit_hours"];
                $subjects = getProgramSubjects($student["program_id"], $table_columns);
                $teachers = getProgramTeachers($student["program_id"]);
                if(is_array($subjects) && count($subjects) > 0) :
                    foreach($subjects as $counter=>$subject) :
            ?>
            <tr data-course-id="<?= $subject["course_id"] ?>" data-school-id="<?= $student["school_id"] ?>">
                <td><?= formatItemId($subject["course_id"], "SID") ?></td>
                <td><?= ucwords(strtolower($subject["course_name"])) ?></td>
                <td><?= ucwords(strtolower($subject["short_form"])) ?></td>
                <td><?= $teachers[$counter] != "none" ? ucwords(strtolower($teachers[$counter])) : "Teacher Not Set" ?></td>
                <td><?= is_null($subject["credit_hours"]) ? "Not Set" : $subject["credit_hours"] ?></td>
            </tr>
            <?php 
                    endforeach;
                else :
            ?>
                <tr class="empty">
                    <td colspan="5" class="txt-al-c sp-xxlg">No subjects have been added to this program yet</td>
                </tr>
            <?php
                endif;
                endif; 
            ?>
        </tbody>
    </table>
</section>

<section class="d-section white lt-shade flex-all-center flex-column">
    <h2 class="sm-xlg-b">Statistics <span id="subject_name"></span></h2>
    <canvas id="stats" class="wmax-md no_disp" style="max-height: 40vh"></canvas>
    <p id="stat_message">Click on a subject to see your progress in it</p>
</section>

<script src="assets/scripts/functions.min.js?v=<?php echo time()?>"></script>
<script src="assets/scripts/chartJS/chart.min.js"></script>
<script>
    $(document).ready(function(){
        var chartElement = null;

        $("#subject_table tbody tr:not(.empty)").click(function(){
            if(!$(this).hasClass("yellow")){
                let subject = $(this).children("td:nth-child(2)").html()
                
                $("tr.yellow").removeClass("yellow")
                $(this).addClass("yellow")

                let loading = null
                
                //data
                const course_id = $(this).attr("data-course-id")
                const school_id = $(this).attr("data-school-id")
                const student_index = $("input#student_index").val()

                if(parseInt(course_id) > 0){
                    $.ajax({
                        url: "submit.php",
                        dataType: "json",
                        data: {
                            cid: course_id, sid: school_id, stud_index: student_index, 
                            submit: "getCourseData"
                        },
                        timeout: 30000,
                        beforeSend: function(){
                            $("canvas#stats").addClass("no_disp")

                            const appends = [".","..","..."]
                            let count = 0

                            loading = setInterval(()=>{
                                if(count < appends.length){
                                    $("#subject_name").html("Processing" + appends[count])
                                    ++count
                                }else{
                                    count = 0
                                }
                            }, 500)

                            $("#stat_message").addClass("no_disp")
                        },
                        success: function(response){
                            clearInterval(loading)
                            $("#subject_name").html("")

                            response = JSON.parse(JSON.stringify(response))

                            if(typeof response["error"] == "boolean"){
                                if(response["error"] === true){
                                    $("#subject_name").html(": No Data")
                                    $("#stat_message").removeClass("no_disp").html(response["message"])
                                }else{
                                    $("#subject_name").html(" for " + subject)

                                    //generate the graph
                                    if(typeof response["message"][0]["exam_type"] == "string" && response["message"].length > 2){
                                        chartElement = generateChart(chartElement, {arrayData: response["message"], chartID: "stats"})
                                    }else{
                                        chartElement = generateChart(chartElement, {arrayData: response["message"], chartType:"pie", chartID: "stats"})
                                    }

                                    //hide stat message and show chart
                                    $("#stat_message").addClass("no_disp")
                                    $("canvas#stats").removeClass("no_disp")
                                }
                            }else{
                                $("canvas#stats").addClass("no_disp")
                                $("#stat_message").removeClass("no_disp").html("An invalid reponse was received.")
                            }
                        },
                        error: function(xhr, textStatus, errorThrown){
                            clearInterval(loading)
                            $("#subject_name").html("")
                            let message = ''

                            if(textStatus == "timeout"){
                                message = "Connection was timed out due to a slow network. Please try again later"
                            }else{
                                message = JSON.stringify(xhr)
                            }
                            $("#stat_message").removeClass("no_disp").html(message,"danger",8)
                        }
                    })
                }else{
                    //abort processing
                    $(this).removeClass("yellow")
                }
            }
        })

        /**
         * demo function to generate single random integer
         * @param {number} limit the maximum number random number
         * @returns {number} an integer
         */
        function generateRandomInteger(limit = 100, minimum = 0){
            const returnValue = (parseInt((Math.random() * limit)) % limit) + minimum
            return returnValue > limit ? limit : returnValue
        }
        /**
         * Demo function for a random data
         * @param {number} dataCount number of numbers to generate
         * @param {number} minimum minimum score value
         * @param {number} maximum maximum score value
         * @returns random data
         */
        function randomData(dataCount, minimum =  0, maximum = 100) {
            var numbers = []

            while(dataCount-- > 0){
                var number = -1
                do{
                    number = generateRandomInteger(maximum)
                }while(number < minimum)
                
                numbers.push(number)
            }

            return numbers
        }
    })
</script>
<?php close_connections() ?>