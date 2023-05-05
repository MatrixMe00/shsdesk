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

<p id="student_index"><?= $student["indexNumber"] ?></p>

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
            <tr data-course-id="" data-school-id="">
                <td>1</td>
                <td>Mathematics</td>
                <td>Teacher 1</td>
                <td>3</td>
            </tr>
            <tr data-course-id="" data-school-id="">
                <td>2</td>
                <td>Social Studies</td>
                <td>Teacher 2</td>
                <td>2</td>
            </tr>
            <tr data-course-id="" data-school-id="">
                <td>3</td>
                <td>English</td>
                <td>Teacher 3</td>
                <td>2</td>
            </tr>
            <tr data-course-id="" data-school-id="">
                <td>4</td>
                <td>French</td>
                <td>Teacher 4</td>
                <td>2</td>
            </tr>
            <tr data-course-id="" data-school-id="">
                <td>5</td>
                <td>Science</td>
                <td>Teacher 5</td>
                <td>3</td>
            </tr>
            <tr data-course-id="" data-school-id="">
                <td>6</td>
                <td>RME</td>
                <td>Teacher 6</td>
                <td>2</td>
            </tr>
            <tr data-course-id="" data-school-id="">
                <td>7</td>
                <td>Citizenship Education</td>
                <td>Teacher 7</td>
                <td>2</td>
            </tr>
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

    $("#subject_table tbody tr").click(function(){
        if(!$(this).hasClass("yellow")){
            let subject = $(this).children("td:nth-child(2)").html()
            
            $("tr.yellow").removeClass("yellow")
            $(this).addClass("yellow")

            let loading = null
            
            //data
            const course_id = $(this).attr("data-course-id")
            const school_id = $(this).attr("data-school-id")
            const student_index = $("#student_index").html()

            $.ajax({
                url: "submit.php",
                dataType: "json",
                data: {
                    cid: course_id, sid: school_id, stud_index: student_index, 
                    submit: "getCourseData"
                },
                timeout: 10,
                beforeSend: function(jqXHR, settings){
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

                    console.log(jqXHR, settings)

                    $("#stat_message").addClass("no_disp")
                },
                success: function(response){
                    clearInterval(loading)

                    response = JSON.parse(JSON.stringify(response))

                    if(typeof response["error"]){
                        if(response["error"] === true){
                            $("#stat_message").removeClass("no_disp").html(response["message"])
                        }else{

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
            return
            //change subject
            $("#subject_name").html(" for " + subject)

            //hide stat message and show chart
            $("#stat_message").addClass("no_disp"); return
            $("canvas#stats").removeClass("no_disp")
            
            const chart_type = "line";
            var graph_colors = selectChartColors(chart_type, 5)
            var min = generateRandomInteger(50); var max = generateRandomInteger(100, (min+1))
            
            var randData = randomData(5, min, max)
            
            var config = {
                type: chart_type,
                data: {
                    labels: ['Year1 T1', 'Year1 T2', 'Year2 T1', 'Year2 T2', 'Year3 T1'],
                    datasets: [{
                        label: 'Subjects',
                        backgroundColor: graph_colors,
                        borderColor: graph_colors,
                        data: randData
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