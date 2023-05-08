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
                <span class="self-align-start">Average Score</span>
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
                <span class="self-align-start">Average Grade</span>
                <span class="txt-fl3 txt-bold self-align-end">Grade <span id="avg_grade">B</span></span>
            </div>
        </section>
        <section class="d-section lt-shade">
            <div class="form flex-all-center flex-eq wmax-md sm-auto flex-wrap gap-sm">
                <label class="p-med" for="report_year">
                    <select class="w-full" name="report_year" id="report_year">
                        <option value="3">Year 3</option>
                        <option value="2">Year 2</option>
                        <option value="1">Year 1</option>
                    </select>
                </label>
                <label class="p-med" for="report_term">
                    <select class="w-full" name="report_term" id="report_term">
                        <option value="1">Term 1</option>
                        <option value="2">Term 2</option>
                        <option value="3">Term 3</option>
                    </select>
                </label>
                <div class="flex flex-wrap gap-sm">
                    <label for="report_search" class="btn p-med">
                        <button id="report_search" class="xs-rnd primary b-primary" name="submit" value="report_search">Generate</button>
                    </label>
                    <label for="report_save" class="btn p-med no_disp">
                        <button id="report_save" class="xs-rnd green b-green" name="report_save" type="button">Save</button>
                    </label>
                    <label for="report_reset" class="btn p-med no_disp">
                        <button id="report_reset" class="xs-rnd red b-red" name="report_reset" type="button">Reset</button>
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

        <script src="assets/scripts/functions.min.js?v=<?php echo time()?>"></script>
        <script>
            $("#report_search").click(function(){
                $("label[for=report_search], #empty_result, #default_result").addClass("no_disp");
                $("label[for=report_save], label[for=report_reset], #non_empty_result").removeClass("no_disp")
            })

            $("#report_reset").click(function(){
                $("label[for=report_search], #default_result").removeClass("no_disp")
                $("label[for=report_save], label[for=report_reset], #empty_result, #non_empty_result").addClass("no_disp")
            })
        </script>