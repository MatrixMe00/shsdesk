<?php require_once "compSession.php"; $_SESSION["active-page"] = "report" ?>
<section class="flex d-section flex-wrap gap-sm p-lg card-section">
    <div class="card v-card gap-lg indigo sm-rnd flex-wrap">
        <span class="self-align-start">Number of Subjects</span>
        <span class="txt-fl3 txt-bold self-align-end" id="subject_number"><?php 
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
        <span class="self-align-start">Average Score (Overall)</span>
        <span class="txt-fl3 txt-bold self-align-end"><span id="avg_score"><?php 
            $average = fetchData1("AVG(mark) as Mark","results","indexNumber=".$student["indexNumber"]);
            if($average == "empty"){
                $average = 0;
            }else{
                $average = $average["Mark"] ?? 0;
            }
            echo round($average,1);
        ?></span>%</span>
    </div>
    <div class="card v-card gap-lg secondary sm-rnd flex-wrap">
        <span class="self-align-start">Average Grade (Overall)</span>
        <span class="txt-fl3 txt-bold self-align-end">Grade <span id="avg_grade"><?= giveGrade($average, fetchData("school_result","admissiondetails","schoolID=".$student["school_id"])["school_result"]) ?></span></span>
    </div>
</section>
<section class="d-section lt-shade">
    <div class="form flex-all-center flex-eq wmax-md sm-auto flex-wrap gap-sm">
        <label class="p-med" for="report_year">
            <select class="w-full" name="report_year" id="report_year">
                <?php for($i=intval($student["studentYear"]); $i > 0; $i--) : ?>
                <option value="<?= $i ?>">Year <?= $i ?></option>
                <?php endfor; ?>
            </select>
        </label>
        <label class="p-med" for="report_term">
            <select class="w-full" name="report_term" id="report_term">
                <option value="1">Semester 1</option>
                <option value="2">Semester 2</option>
            </select>
        </label>
        <input type="hidden" name="indexNumber" id="indexNumber" value="<?= $student["indexNumber"] ?>">
        <div class="flex btn w-fluid-child flex-eq gap-sm">
            <label for="report_search" class="p-med">
                <button id="report_search" class="xs-rnd primary b-primary w-full" name="submit" value="report_search">Generate</button>
            </label>
            <label for="report_save" class="p-med no_disp">
                <button id="report_save" class="xs-rnd green b-green w-full" name="report_save" type="button">Save</button>
            </label>
            <label for="report_reset" class="p-med no_disp">
                <button id="report_reset" class="xs-rnd red b-red w-full" name="report_reset" type="button">Reset</button>
            </label>
        </div>
        
    </div>
</section>
<section class="d-section lt-shade no_disp" id="empty_result">
    <table class="full">
        <tbody>
            <tr class="empty">
                <td>Results for this period has not been uploaded yet</td>
            </tr>
        </tbody>
    </table>
</section>

<section class="d-section lt-shade" id="default_result">
    <table class="full">
        <tbody>
            <tr class="empty">
                <td>Click on the <b>"Generate"</b> button to generate your results</td>
            </tr>
        </tbody>
    </table>
</section>

<section class="d-section lt-shade no_disp" id="non_empty_result">
    <table class="full">
        <thead>
            <td>ID</td>
            <td>Exam Type</td>
            <td>Subject</td>
            <td>Class Score</td>
            <td>Exam Score</td>
            <td>Grade</td>
            <td>Total Score</td>
        </thead>
        <tbody>
        </tbody>
    </table>
</section>

<section class="d-section lt-shade txt-al-c no_disp" id="save_status">
    <p class="sp-lg txt-fl"></p>
</section>

<section>
    <canvas id="stats" class="wmax-md" style="max-height: 40vh"></canvas>
</section>

<script src="assets/scripts/functions.js?v=<?php echo time()?>"></script>
<script src="assets/scripts/chartJS/chart.min.js"></script>
<script>
    $(document).ready(function(){
        var chartElement = null;

        $("#report_search").click(function(){
            $("#empty_result, #default_result").addClass("no_disp");

            const report_year = $("select#report_year").val()
            const report_term = $("select#report_term").val()
            const indexNumber = $("input#indexNumber").val()

            $.ajax({
                url: "./submit.php",
                data: {
                    report_term: report_term, report_year: report_year, submit: "report_search",
                    index_number: indexNumber
                },
                timeout: 8000,
                beforeSend: function(){
                    $("#empty_result").removeClass("no_disp").find(".empty td").html("Fetching data...")
                },
                success: function(response){
                    if(response["error"] === true){
                        $("#empty_result").removeClass("no_disp").find(".empty td").html(response["message"])
                    }else{
                        $("#empty_result, label[for=report_search]").addClass("no_disp")

                        fillTable({
                            table_id: "non_empty_result", result_data: response["message"],
                            has_mark: true, mark_index: 6
                        })

                        chartElement = generateChart(chartElement, {
                            arrayData: response["message"], isMarks: false, chartID: "stats", chartType: "pie"
                        })

                        $("label[for=report_save], label[for=report_reset], #non_empty_result").removeClass("no_disp")
                    }
                },
                error: function(xhr, textStatus){
                    if(textStatus === "timeout"){
                        $("#empty_result").removeClass("no_disp").find(".empty td").html("Connection was timed out. Please check your internet connection and try again later")
                    }
                }
            })
            
        })

        $("#report_reset").click(function(){
            $("label[for=report_search], #default_result").removeClass("no_disp")
            $("label[for=report_save], label[for=report_reset], #empty_result, #non_empty_result").addClass("no_disp")
            $("select").prop("selectedIndex", 0)
        })

        $("button#report_save").click(function(){
            const report_year = $("select#report_year").val()
            const report_term = $("select#report_term").val()
            const indexNumber = $("input#indexNumber").val()
            const canvasImage = $("canvas#stats")[0].toDataURL()
            
            if(canvasImage.length <= 22){
                alert_box("Chart could not be drawn")
                return
            }

            $.ajax({
                url: "./components/generateReport.php",
                data: {
                    canvas: canvasImage, submit:"generateReport", year: report_year, semester: report_term
                },
                type: "POST",
                timeout: 8000,
                cache: false,
                beforeSend: function (){
                    $("#save_status p").html("Getting your document ready, please wait...")
                    $("#save_status").removeClass("no_disp")
                },
                success: function (data){
                    $("#save_status p").html("Documents are ready")
        
                    if(typeof data === "object"){
                        isError = data["error"] == true

                        if(isError){
                            alert_box("An error occurred while processing the document", "danger")
                        }else{
                            alert_box("Response: " + data["message"])
                        }
                    }else{
                        alert_box(data, "primary", 10);
                    }
                },
                error: function (jqXHR, textStatus, errorThrown){
                    let message = ""
                    $("#save_status p").html("")
                    $("#save_status").addClass("no_disp")

                    if(textStatus == "timeout"){
                        message = "Connection was timed out. Please check your connection and try again"
                    }else{
                        message = JSON.stringify(jqXHR)
                    }

                    alert_box(message, "danger")
                }
            })
        })

        $("select[name=report_year], select[name=report_term]").change(function(){
            $("label[for=report_search]").removeClass("no_disp")
            $("label[for=report_save]").addClass("no_disp")
        })
    })
</script>