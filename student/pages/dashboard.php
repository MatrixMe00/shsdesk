<?php include_once "compSession.php"; $_SESSION["active-page"] = "dashboard" ?>
    <h1 class="txt-fl1 d-greeting sp-xlg-lr"><?= showGreeting()." ".$student["Lastname"].", " ?></h1>
        <section class="flex d-section flex-wrap gap-sm p-lg card-section">
            <div class="card v-card gap-lg purple sm-rnd flex-wrap">
                <span class="self-align-start">Number of Subjects</span>
                <span class="txt-fl3 txt-bold self-align-end">
                    <?php 
                        if(!is_null($student["program_id"])){
                            $course_total = countProgramSubjects($student["program_id"]);

                            if($course_total === false){
                                echo "<span class='txt-fl2'>No subjects assigned</span>";
                            }else{
                                echo $course_total;
                            }
                        }else{
                            echo "<span class='txt-fl2'>No class assigned</span>";
                        }
                    ?>
                </span>
            </div>
            <div class="card v-card gap-lg dark sm-rnd flex-wrap">
                <span class="self-align-start">Current Year</span>
                <span class="txt-fl3 txt-bold self-align-end">Year <?= $student["studentYear"] ?></span>
            </div>
        </section>
        
        <div class="flex gap-md flex-wrap">
            <section class="d-section white lt-shade hmin" style="flex: 2 1 360px">
                <div class="head txt-al-c">
                    <h2>Announcements</h2>
                </div>
                <div class="body sm-med-t m-lg-tp wmax-md sm-auto">
                    <?php 
                        $sql = "SELECT heading, body, date 
                            FROM announcement WHERE school_id = {$student['school_id']} AND audience IN ('students', 'all')";
                        $query = $connect2->query($sql);

                        if($query->num_rows > 0) :
                            while($message = $query->fetch_assoc()) :
                    ?>
                    <div class="sp-med w-full lt-shade-h light">
                        <h3 class="top"><?= $message["heading"] ?></h3>
                        <div class="middle sm-med-tp"><?= html_entity_decode($message["body"]) ?></div>
                        <div class="foot">
                            <span class="txt-fs color-dark"><?= date("F d, Y H:i:s", strtotime($message["date"])) ?></span>
                        </div>
                    </div>
                    <?php endwhile; else: ?>
                    <div class="sp-med w-full light p-lg txt-al-c lt-shade white">
                        <div class="middle">No Announcements have been made</div>
                    </div>
                    <?php endif; ?>
                </div>
            </section>

            <?php if($code !== "empty") : ?>
            <section class="d-section white lt-shade flex-all-center flex-column" style="flex: 3 1 auto">
                <h2 class="sm-xlg-b">Statistics</h2>
                <?php if($course_total): ?>
                <div class="form flex-all-center flex-eq wmax-md sm-auto flex-wrap gap-sm" id="searchStats">
                    <label for="stat_year" class="p-med">
                        <select name="stat_year" id="stat_year">
                            <?php
                                $years = decimalIndexArray(fetchData1("DISTINCT exam_year, program_id", "results", "indexNumber='{$student['indexNumber']}'", limit: 0)); 

                                if(!$years){
                                    $years = decimalIndexArray(["exam_year" => $student["studentYear"], "program_id" => $student["program_id"]]);
                                }

                                foreach($years as $year) : 
                            ?>
                            <option value="<?= $year["exam_year"] ?>" data-program-id="<?= $year["program_id"] ?>">Year <?= $year["exam_year"] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                    <label for="stat_term" class="p-med">
                        <select name="stat_term" id="stat_term">
                            <option value="1">Semester 1</option>
                            <option value="2">Semester 2</option>
                        </select>
                    </label>
                    <input type="hidden" name="student_id" id="search_student_id" value="<?= $student["indexNumber"] ?>">
                    <div for="submit" class="flex btn w-fluid-child flex-eq gap-sm">
                        <button id="stat_search" class="xs-rnd primary b-primary w-full m-border" name="submit" value="stat_search">Preview</button>
                    </div>
                </div>
                <div id="chart_type_div" class="form no_disp">
                    <label for="chart_type">
                        <span class="label_title">Chart type</span>
                        <select name="chart_type" id="chart_type">
                            <option value="line">Line Chart</option>
                            <option value="bar">Bar Chart</option>
                            <option value="pie">Pie Chart</option>
                            <option value="doughnut">Doughnut Chart</option>
                            <option value="polarArea">Polar Area Chart</option>
                        </select>
                    </label>
                </div>
                <canvas id="stats" class="wmax-md sm-auto w-full no_disp" style="max-height: 40vh"></canvas>
                <?php endif; ?>
                <p id="canvas_status" class="txt-al-c sp-lg sm-xlg-tp txt-fl1"><?= $course_total ? "Make a results preview search in the stats box above" : "No subjects have been assigned yet." ?></p>
            </section>
        </div>
        
        <section class="d-section white no_disp" id="exam_data">
            <table class="full">
                <thead>
                    <td>ID</td>
                    <td>Exam Type</td>
                    <td>Subject</td>
                    <td>Grade</td>
                    <td>Percent</td>
                </thead>
                <tbody></tbody>
            </table>
        </section>

        <!-- chart script -->
    <script src="assets/scripts/functions.min.js?v=<?php echo time()?>"></script>
    <script src="assets/scripts/chartJS/chart.min.js"></script>
    <script>
        $(document).ready(function(){
            var chartElement = null
            var new_search_data = true
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

            $("#searchStats select").change(function(){
                new_search_data = true
            })

            $("button#stat_search").click(function(){
                if(new_search_data){
                    const year = $("select[name=stat_year]").val();
                    const term = $("select[name=stat_term]").val();
                    const indexNumber = $("#search_student_id").val();
                    const program_id = $("select[name=stat_year] option:selected").attr("data-program-id");

                    $.ajax({
                        url: "submit.php",
                        data: {
                            report_year: year, report_term: term, submit: $(this).attr("value"),
                            index_number: indexNumber, result_distinct: true, program_id: program_id
                        },
                        dataType: "json",
                        type: "get",
                        timeout: 30000,
                        beforeSend: function(){
                            $("canvas, #exam_data, #chart_type_div").addClass("no_disp")
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
                                $("canvas, #exam_data, #chart_type_div").removeClass("no_disp")

                                new_search_data = false
                                console.log(response["message"])
                                $("select#chart_type").change()
                                
                                fillTable({
                                    table_id: "exam_data",result_data: response["message"],
                                    has_mark: true, mark_index: 4
                                })
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
        })
    </script>
    <?php else: ?>
        <section class="d-section white lt-shade flex-all-center flex-column" style="flex: 3 1 auto">
            <p>Please purchase an access code from the 'Get Access Code' menu to view statistics</p>
        </section>
    </div>
    <?php endif; close_connections() ?>