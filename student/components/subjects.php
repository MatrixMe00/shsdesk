<?php require_once "compSession.php"; $_SESSION["active-page"] = "subject" ?>
<section class="flex d-section flex-wrap gap-sm p-lg card-section">
    <div class="card v-card gap-lg primary sm-rnd flex-wrap">
        <span class="self-align-start">Total Subjects</span>
        <span class="txt-fl3 txt-bold self-align-end">10</span>
    </div>
    <div class="card v-card gap-lg purple sm-rnd flex-wrap">
        <span class="self-align-start">Total Core Subjects</span>
        <span class="txt-fl3 txt-bold self-align-end">4</span>
    </div>
    <div class="card v-card gap-lg orange sm-rnd flex-wrap">
        <span class="self-align-start">Total Program subjects</span>
        <span class="txt-fl3 txt-bold self-align-end">6</span>
    </div>
</section>

<input type="hidden" name="student_index" id="student_index" value="<?= $student["indexNumber"] ?>">

<section class="d-section">
    <h1 class="sm-lg-b">Registered Courses</h1>
    <table class="full" id="subject_table">
        <thead>
            <td>ID</td>
            <td>Subject</td>
            <td>Teacher</td>
            <td>Credit Hours</td>
        </thead>
        <tbody>
            <?php 
                $course_ids = fetchData1("program_id,course_ids","program","LOWER(program_name)='".strtolower($student["programme"])."'");
                if($course_ids == "empty") :
            ?>
            <tr class="empty">
                <td colspan="4" class="txt-al-c sp-xxlg-tp">Your program has not been uploaded yet. Please contact your school administrator for aid.</td>
            </tr>
            <?php else :
                $course_ids = explode(" ", $course_ids);
            ?>
            <tr data-course-id="1" data-school-id="<?= $student["school_id"] ?>">
                <td>1</td>
                <td>Mathematics</td>
                <td>Teacher 1</td>
                <td>3</td>
            </tr>
            <?php endif; ?>
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
    var chartElement = null;
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

            $.ajax({
                url: "submit.php",
                dataType: "json",
                data: {
                    cid: course_id, sid: school_id, stud_index: student_index, 
                    submit: "getCourseData"
                },
                timeout: 15000,
                beforeSend: function(){
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

                    if(typeof response["error"]){
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
        }
    })
</script>