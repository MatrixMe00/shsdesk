<?php
    include_once("compSession.php");
    require("$mainRoot/mpdf/vendor/autoload.php");
    require("$mainRoot/mpdf/qr/vendor/autoload.php");

    if(isset($_REQUEST["submit"])){
        $submit = $_REQUEST["submit"];

        if($submit == "generateReport"){
            if(!empty($student)){
                //instance of the pdf and qrcode class
                $pdf = new \Mpdf\Mpdf();

                //make settings for footer
                $footer = array (
                    'odd' => array (
                        'L' => array (
                            'content' => 'Page {PAGENO} of {nbpg}',
                            'font-size' => 10,
                            'font-style' => 'R',
                            'font-family' => 'Times',
                            'color'=>'#000000'
                        ),
                        'C' => array (
                            'content' => getSchoolDetail(intval($student["school_id"]))["schoolName"],
                            'font-size' => 10,
                            'font-style' => 'R',
                            'font-family' => 'Times',
                            'color'=>'#000000'
                        ),
                        'line' => 1
                    ),
                    'even' => array (
                        'L' => array (
                            'content' => 'Page {PAGENO} of {nbpg}',
                            'font-size' => 10,
                            'font-style' => 'R',
                            'font-family' => 'Times',
                            'color'=>'#000000'
                        ),
                        'C' => array (
                            'content' => getSchoolDetail(intval($student["school_id"]))["schoolName"],
                            'font-size' => 10,
                            'font-style' => 'R',
                            'font-family' => 'Times',
                            'color'=>'#000000'
                        ),
                        'line' => 1
                    )
                );

                //apply footer settings
                $pdf->setFooter($footer);

                //provide document information
                $pdf->SetCreator("MatrixMe");
                $pdf->SetAuthor("SHSDesk");
                $pdf->SetTitle("Student Report | ".$student["Lastname"]);
                $pdf->SetSubject("Report Summary");
                $pdf->SetKeywords("Report, SHSDesk, summary, document, ".getSchoolDetail($student["school_id"])['schoolName']);

                $date_now = date("M j, Y")." at ".date("h:i:sa");

                //semester results table filters
                $semester = $_REQUEST["semester"];
                $exam_year = $_REQUEST["year"];
                $program_id = $_REQUEST["program_id"];

                //school details
                $school = getSchoolDetail($student["school_id"], true);
                $admission_details = fetchData(["school_result", "headName"], "admissiondetails", "schoolID={$school['id']}");

                $school_result_type = $admission_details["school_result"];
                $schoolLogo = "<img src=\"$mainRoot".$school["logoPath"]."\" alt=\"logo\" width=\"30mm\" height=\"30mm\">";
                $head_master = $admission_details["headName"];
                
                $schoolName = $school["schoolName"];
                $postal_address = $school["postalAddress"];
                //end of school details

                //all student details
                $student_name = $student["Lastname"]." ".$student["Othernames"];
                
                $class_name = fetchData1("program_name, short_form","program", "program_id=$program_id");
                // $chartImg = "<img src=\"".$_REQUEST['canvas']."\" height=\"80mm\ width=\"80mm\">";
                if(is_array($class_name)){
                    $class_name = empty($class_name["short_form"]) ? $class_name["program_name"] : $class_name["short_form"];
                }else{
                    $class_name = "Not Set";
                }

                $student_program = $student["programme"];
                $class_result_data = fetchData1("COUNT(DISTINCT indexNumber) as total, academic_year", "results","program_id=$program_id AND exam_year=$exam_year AND semester=$semester", group_by:["indexNumber","academic_year"]);
                
                $academicYear = $class_result_data["academic_year"];

                //calculating class position
                $class_position = getStudentPosition($student["indexNumber"], $exam_year, $semester, $program_id);
                $total_position = fetchData1("COUNT(DISTINCT indexNumber) as total", "results","program_id=$program_id AND exam_year=$exam_year AND semester=$semester")["total"];

                $attendance = fetchData1("student_attendance, attendance_total", "attendance", "indexNumber={$student['indexNumber']} AND student_year=$exam_year AND semester=$semester");
                if(is_array($attendance)){
                    $attendance = "Class Attendance: {$attendance['student_attendance']} of {$attendance['attendance_total']}";
                }else{
                    $attendance = "Class Attendance: Not Set";
                }
                
                //end of student details

                //results table
                $sql = "SELECT DISTINCT c.course_name, r.course_id, r.class_mark, r.exam_mark, r.mark, r.position
                    FROM results r JOIN courses c ON r.course_id=c.course_id
                    WHERE r.indexNumber='{$student['indexNumber']}' AND r.program_id=$program_id AND r.exam_year=$exam_year AND r.semester=$semester";
                $results = decimalIndexArray($connect2->query($sql)->fetch_all(MYSQLI_ASSOC));

                //average of student marks
                $average = 0;

                $table = "
                <table id=\"results_table\">\n
                    <thead>\n
                        <tr>
                            <td>Subject</td>\n
                            <td>Class Score</td>\n
                            <td>Exam Score</td>\n
                            <td>Total</td>\n
                            <td>Grade</td>\n
                            <td>Position</td>\n
                        </tr>\n
                    </thead>\n
                    <tbody>\n";

                if($results){
                    $total_class = 0;
                    $total_exam = 0;

                    $average = array_sum(array_column($results, "mark")) / count($results);

                    foreach($results as $row){
                        $table .= "
                        <tr>\n
                            <td>{$row['course_name']}</td>\n
                            <td>".number_format($row['class_mark'], 1)."</td>\n
                            <td>".number_format($row['exam_mark'], 1)."</td>\n
                            <td>".number_format($row['mark'], 1)."</td>\n
                            <td>".giveGrade($row['mark'], $school_result_type)."</td>\n
                            <td>".positionFormat($row["position"])."</td>\n
                        </tr>
                        ";
                        $total_class += $row["class_mark"];
                        $total_exam += $row["exam_mark"];
                    }
                    $total_class = number_format($total_class, 1);
                    $total_exam = number_format($total_exam, 1);
                    $table .= "
                    <tr class=\"total\">\n
                        <td>Total</td>\n
                        <td>$total_class</td>\n
                        <td>$total_exam</td>\n
                        <td>".number_format($total_class + $total_exam, 1)."</td>\n
                        <td colspan=\"2\"></td>\n
                    </tr>\n
                    <tr><td colspan=\"6\" style=\"border: unset\"></rd></tr>\n
                    <tr><td colspan=\"6\" style=\"text-align: center\">$attendance</rd></tr>\n
                    ";
                }else{
                    $table .= "<tr colspan=\"6\">No results were found</tr>\n";
                }

                $average = number_format($average, 1);
                $avg_grade = giveGrade($average, $school_result_type);


                $table .= "</tbody>
                </table>
                ";

                //qrcode
                $newqr = '<barcode code="Document digitally signed by '.$head_master.'" type="QR" class="barcode" size="1" error="M" disableborder="0"
                style="background-color: yellow;" />';

                $school_result_type = strtoupper($school_result_type);

                $content = <<<HTML
                <head>
                    <style>
                        .border{border: 1px solid #888; padding: 0.5em; margin: 2.5mm auto}
                        .border.no-b{border-bottom: unset;}
                        .f-left{float: left}
                        .f-right{float: right}
                        .logo{width: 50mm;}
                        .header{text-align: center}
                        .fl{font-size: large}
                        .fs{font-size: small}
                        .school_name{margin-bottom: 10mm; margin-top: 10mm;}
                        .half{width: 80.8mm}
                        .quarter{width: 40.4mm}
                        .third{width: 53.9mm}
                        .full{width: 161.6mm}
                        .inline{display: inline}
                        .top-space{margin-top: 5mm}
                        #results_table td{padding: 0.5em}
                        #results_table{width: 100%}
                        #results_table thead{font-weight: bold}
                        #results_table td{border: 1px solid lightgrey}
                        #results_table .total td{border: unset; border-top: 3px double grey; border-bottom: 3px double grey; font-weight: bold}
                        #table td.no-border, .no-border{border: unset}
                        .digital{margin-top: 3mm;}
                    </style>
                </head>
                <div class="border header">
                    <div class="f-left logo">
                        $schoolLogo
                    </div>
                    <div class="f-right">
                        <br><b class="fl school_name">$schoolName</b><br>
                        <span>$academicYear Academic Report - Semester $semester </span><br>
                        <span>$postal_address</span>
                    </div>
                </div>
                <div class="border">
                    <p style="text-align: center; margin-bottom: 3mm; margin-top:-1mm">$student_program</p>
                    <div class="half inline f-left"><b>Name:</b> $student_name</div>
                    <div class="quarter f-left inline"><b>Class:</b>$class_name</div>
                    <div class="quarter f-left inline"><b>Year: </b>$exam_year</div>
                    <div class="third f-left inline top-space"><b>Average Score: </b> $average</div>
                    <div class="third f-left inline"><b>Average Grade: </b>$avg_grade</div>
                    <div class="third f-left inline"><b>Class Position: </b>$class_position of $total_position</div>
                </div>
                <div class="border">
                    <br><p><b>School Grading Type: </b>$school_result_type</p>
                    $table
                </div>
                <br><br><br>
                <div class="foot" class="digital" style="border-left: 2px solid lightgrey; text-align: right">
                    <div style="text-align:left; padding-left: 4mm">$newqr</div>
                    <div style="padding-top: -17mm">
                        <span>
                            <i>--Digitally Signed in QR Code--</i>
                        </span><br>
                        <span class="headMaster">$head_master</span><br>
                        <span class="it-admin">[School Head]</span>
                    </div>
                </div>
                <div style="margin-top: 10mm; text-align: center">
                    <span>Document Generated on $date_now</span>
                </div>
                HTML;

                $response = [
                    "error" => false,
                    "message" => "success"
                ];

                //write some html
                $pdf->writeHTML($content);

                //output html
                $pdf->Output("Semester Report | {$student['Lastname']}.pdf", "D");
            }else{
                $response = [
                    "error" => true,
                    "message" => "error"
                ];

                header("Content-Type: application/json");
                echo json_encode($response);
            }
        }else{
            echo "Submit value not found";
        }
    }else{
        header("Content-Type: application/json");
        echo json_encode([
            "status" => false,
            "message" => "No index number specified"
        ]);
    }

    close_connections();
?>