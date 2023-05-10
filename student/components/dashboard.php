<?php include_once "compSession.php"; $_SESSION["active-page"] = "dashboard" ?>
    <h1 class="txt-fl1 d-greeting sp-xlg-lr"><?= showGreeting()." ".$student["Lastname"].", ".generateIndexNumber(5) ?></h1>
        <section class="flex d-section flex-wrap gap-sm p-lg card-section">
            <div class="card v-card gap-lg purple sm-rnd flex-wrap">
                <span class="self-align-start">Number of Subjects</span>
                <span class="txt-fl3 txt-bold self-align-end">
                    <?php 
                        $course_ids = fetchData1(
                            "course_ids","program","LOWER(program_name)='".
                            strtolower($student["programme"])."' AND school_id=".$student["school_id"]
                        );
                        if($course_ids === "empty"){
                            echo 0;
                        }else{
                            echo count(explode(" ",$course_ids["course_ids"])) - 1;
                        }
                    ?>
                </span>
            </div>
            <div class="card v-card gap-lg dark sm-rnd flex-wrap">
                <span class="self-align-start">Current Year</span>
                <span class="txt-fl3 txt-bold self-align-end">Year <?= $student["studentYear"] ?></span>
            </div>
        </section>
        <section class="d-section lt-shade flex-all-center flex-column">
            <h2 class="sm-xlg-b">Statistics</h2>
            <div class="form" id="searchStats">
                <label for="stat_year">
                    <select name="stat_year" id="stat_year">
                        <option value="3">Year 3</option>
                        <option value="2">Year 2</option>
                        <option value="1">Year 1</option>
                    </select>
                </label>
                <label for="stat_term">
                    <select name="stat_term" id="stat_term">
                        <option value="1">Term 1</option>
                        <option value="2">Term 2</option>
                        <option value="3">Term 3</option>
                    </select>
                </label>
                <input type="hidden" name="student_id" id="search_student_id" value="<?= $student["indexNumber"] ?>">
                <label for="submit" class="btn">
                    <button id="stat_search" class="primary m-border b-primary" name="submit" value="stat_search">Preview</button>
                </label>
            </div>
            <div class="form">
                <label for="chart_type">
                    <span class="label_title">Chart type</span>
                    <select name="chart_type" id="chart_type">
                        <option value="line">Line Chart</option>
                        <option value="bar">Bar Chart</option>
                        <option value="pie">Pie Chart</option>
                        <option value="scatter">Scatter Chart</option>
                        <option value="doughnut">Doughnut Chart</option>
                        <option value="polarArea">Polar Area Chart</option>
                    </select>
                </label>
            </div>
            <canvas id="stats" class="wmax-md" style="max-height: 40vh"></canvas>
            <p id="canvas_status" class="no_disp txt-al-c sp-lg sm-xlg-tp txt-fl1"></p>
        </section>
        <section class="d-section" id="exam_data">
            <table class="full">
                <thead>
                    <td>ID</td>
                    <td>Exam Type</td>
                    <td>Subject</td>
                    <td>Grade</td>
                    <td>Percent</td>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>Exams</td>
                        <td>Mathematics</td>
                        <td>B</td>
                        <td>73</td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>Exams</td>
                        <td>Social Studies</td>
                        <td>C</td>
                        <td>66</td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>Exams</td>
                        <td>English</td>
                        <td>A</td>
                        <td>83</td>
                    </tr>
                    <tr>
                        <td>4</td>
                        <td>Exams</td>
                        <td>French</td>
                        <td>B</td>
                        <td>77</td>
                    </tr>
                    <tr>
                        <td>5</td>
                        <td>Exams</td>
                        <td>Science</td>
                        <td>C</td>
                        <td>69</td>
                    </tr>
                    <tr>
                        <td>6</td>
                        <td>Exams</td>
                        <td>RME</td>
                        <td>A</td>
                        <td>84</td>
                    </tr>
                    <tr>
                        <td>7</td>
                        <td>Exams</td>
                        <td>Citizenship Education</td>
                        <td>B</td>
                        <td>74</td>
                    </tr>
                </tbody>
            </table>
        </section>

        <!-- chart script -->
    <script src="assets/scripts/functions.min.js?v=<?php echo time()?>"></script>
    <script src="assets/scripts/chartJS/chart.min.js"></script>
    <script>
        var chartElement = null
        var new_search_data = false
        var dataLabels = ['Mathematics', 'Gen. Science', 'Social Studies', 'Graphics', 'ICT']
        var dataData = [87, 71, 81, 69, 76]

        function fillData(arrayData){
            dataLabels = []
            dataData = []
            for(var i = 0; i < arrayData.length; i++){
                dataLabels[i] = arrayData[i]["course_name"]
                dataData[i] = arrayData[i]["mark"]
            }
        }

        function giveGrade(mark, exam_type="wassce"){
            let grade = ""

            switch(exam_type){
                case "wassce":
                    if(mark >= 80){
                        grade = "A1"
                    }else if(mark >= 70){
                        grade = "B2"
                    }else if(mark >= 65){
                        grade = "B3"
                    }else if(mark >= 60){
                        grade = "C4"
                    }else if(mark >= 55){
                        grade = "C5"
                    }else if(mark >= 50){
                        grade = "C6"
                    }else if(mark >= 45){
                        grade = "D7"
                    }else if(mark >= 40){
                        grade = "E8"
                    }else{
                        grade = "F9"
                    }
                    break
                case "ctvet":
                    if(mark >= 80){
                        grade = "D"
                    }else if(mark >= 60){
                        grade = "C"
                    }else if(mark >= 40){
                        grade = "P"
                    }else{
                        grade = "F"
                    }
                    break
            }

            return grade
        }

        function fillTable(table_id, result_data){
            const tbody = $("#" + table_id).find("tbody")
            $(tbody).html("")

            for(i = 0; i < result_data.length; i++){
                tr = "<tr>"
                tr += "<td>" + (i+1) + "</td>" +
                      "<td>" + result_data[i]["exam_type"] + "</td>" +
                      "<td>" + result_data[i]["course_name"] + "</td>" + 
                      "<td>" + giveGrade(result_data[i]["mark"]) + "</td>" +
                      "<td>" + result_data[i]["mark"] + "</td>"
                tr += "</tr>"

                $(tbody).append(tr)
            }
        }

        $("select#chart_type").change(function(){
            let chart_type = $(this).val();
            var graph_colors = selectChartColors(chart_type, 5)
            
            var config = {
                type: chart_type,
                data: {
                    labels: dataLabels,
                    datasets: [{
                        label: 'Subjects',
                        backgroundColor: graph_colors,
                        borderColor: graph_colors,
                        data: dataData
                    }]
                },
                options: {}
            }

            if(chartElement != null){
                chartElement.destroy()
            }
            chartElement = new Chart(document.getElementById('stats'),config);
        })

        $(document).ready(function(){
            $("select#chart_type").change()
        })

        $("#searchStats select").change(function(){
            new_search_data = true
        })

        $("button#stat_search").click(function(){
            if(new_search_data){
                const year = $("select[name=stat_year]").val()
                const term = $("select[name=stat_term]").val()
                const indexNumber = $("#search_student_id").val()

                $.ajax({
                    url: "submit.php",
                    data: {
                        stat_year: year, stat_term: term, submit: $(this).attr("value"),
                        student_id: indexNumber
                    },
                    dataType: "json",
                    type: "get",
                    timeout: 8000,
                    beforeSend: function(){
                        $("canvas, #exam_data").addClass("no_disp")
                        $("#canvas_status").removeClass("no_disp").html("Fetching results, please wait...")
                    },
                    success: function(response){
                        response = JSON.parse(JSON.stringify(response))

                        if(typeof response["error"] === "boolean" && response["error"] === false){
                            fillData(response["message"])

                            $("#canvas_status").html("Data was received")
                            setTimeout(()=>{
                                $("#canvas_status").addClass("no_disp")
                            },3000)
                            $("canvas, #exam_data").removeClass("no_disp")

                            new_search_data = false
                            console.log(response["message"])
                            $("select#chart_type").change()
                            
                            fillTable("exam_data",response["message"])
                        }else{
                            $("#canvas_status").html(response["message"])
                        }
                    },
                    error: function(xhr, textStatus, errorThrown){
                        if(textStatus == "timeout"){
                            $("#canvas_status").html("Connection was timed out. Please try again later")
                        }else{
                            $("#canvas_status").html(JSON.stringify(xhr))
                        }
                    }
                })
            }
        })
    </script>