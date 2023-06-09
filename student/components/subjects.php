<?php require_once "compSession.php"; $_SESSION["active-page"] = "subject" ?>
<section class="flex d-section flex-wrap gap-sm p-lg card-section">
    <div class="card v-card gap-lg primary sm-rnd flex-wrap">
        <span class="self-align-start">Total Subjects</span>
        <span class="txt-fl3 txt-bold self-align-end"><?php 
            if(!is_null($student["program_id"])){
                $course_ids = fetchData1("course_ids","program","program_id=".intval($student["program_id"]));
                if($course_ids == "empty"){
                    echo "<span class='txt-fl2'>No courses assigned</span>";
                }else{
                    $course_ids = explode(" ", $course_ids["course_ids"]);
                    array_pop($course_ids);
                    echo count($course_ids);
                }
            }else{
                echo "<span class='txt-fl2'>No class assigned</span>";
            }
        ?></span>
    </div>
    <div class="card v-card gap-lg orange sm-rnd flex-wrap">
        <span class="self-align-start">Total Program Teachers</span>
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

<section class="d-section">
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
                $programData = fetchData1("program_id,course_ids","program","program_id=".intval($student["program_id"]));
                if($programData == "empty") :
            ?>
            <tr class="empty">
                <td colspan="5" class="txt-al-c sp-xxlg-tp">Your program has not been uploaded yet. Please contact your school administrator for aid.</td>
            </tr>
            <?php else :
                $course_ids = explode(" ", $programData["course_ids"]);
                //remove the last element which is a space
                if(end($course_ids) == "")
                    array_pop($course_ids);

                    $sql = "SELECT t.lname, t.oname, tc.course_id, c.course_name, c.short_form, c.credit_hours
                        FROM teacher_classes tc JOIN teachers t ON tc.teacher_id=t.teacher_id JOIN courses c
                        ON tc.course_id = c.course_id
                        WHERE tc.program_id={$student['program_id']}
                    ";
                    $subjects = $connect2->query($sql);
                if($subjects->num_rows > 0) :
                    $counter = 1;
                    while($subject = $subjects->fetch_assoc()) :
            ?>
            <tr data-course-id="<?= $subject["course_id"] ?>" data-school-id="<?= $student["school_id"] ?>">
                <td><?= $counter++ ?></td>
                <td><?= $subject["course_name"] ?></td>
                <td><?= $subject["short_form"] ?></td>
                <td><?= $subject["lname"]." ".@$subject["oname"] ?></td>
                <td><?= is_null($subject["credit_hours"]) ? "Not Set" : $subject["credit_hours"] ?></td>
            </tr>
            <?php 
                    endwhile;
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

<section class="d-section lt-shade flex-all-center flex-column">
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
                        timeout: 8000,
                        beforeSend: function(){
                            chartElement = null
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
                                    if(typeof response["message"][0]["exam_type"] == "string" && response["message"].length > 1){
                                        generateChart(response["message"])
                                    }else{
                                        generateChart(response["message"], "pie")
                                    }

                                    //hide stat message and show chart
                                    $("#stat_message").addClass("no_disp")
                                    $("canvas#stats").removeClass("no_disp")
                                }
                            }else{
                                chartElement = null
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

        /**
         * This function is used to bind a year and semester level
         * @param {string|int} year This is the year value
         * @param {string |int} term This is the term value
         * @returns {string} the year and term combination
         */
        function yearBind(year, term){
            return "Year" + year + " T" + term
        }

        /**
         * This function will be used to generate a graph
         * @param {array} arrayData This is the data array to be executed
         * @param {string} chartType This receives the type of chart to be created
         * 
         * @returns a new chart
         */
        function generateChart(arrayData, chartType="line"){
            const chart_type = chartType
            var graph_colors = selectChartColors(chart_type, 5)
            var chartLabels = []
            var chartData = []

            //for testing
            // var min = generateRandomInteger(50); var max = generateRandomInteger(100, (min+1))
            // chartLabels = ['Year1 T1', 'Year1 T2', 'Year2 T1', 'Year2 T2', 'Year3 T1']
            // chartData = randomData(5, min, max)
            
            for(var i = 0; i < arrayData.length; i++){
                chartLabels.push(yearBind(arrayData[i]["exam_year"], arrayData[i]["semester"]))
                chartData.push(arrayData[i]["mark"])
            }

            var config = {
                type: chart_type,
                data: {
                    labels: chartLabels,
                    datasets: [{
                        label: 'Subjects',
                        backgroundColor: graph_colors,
                        borderColor: graph_colors,
                        data: chartData
                    }]
                },
                options: {}
            }

            if(chartElement != null){
                chartElement.destroy()
            }
            chartElement = new Chart(document.getElementById('stats'),config);
        }
    })
</script>