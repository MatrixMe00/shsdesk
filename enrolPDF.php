<?php
    require_once "includes/session.php";

    if(!isset($_SESSION["ad_stud_index"])){
        exit("No student information found");
    }

    $data = fetchData("*", "enrol_table", "indexNumber='{$_SESSION['ad_stud_index']}'");
    extract($data, EXTR_PREFIX_SAME, "var");

    // if user has been inserted into students table, then get program name
    // $program_name = fetchData1("")

    $school_name = $_SESSION["ad_school_name"];

    if(!is_array($data)){
        exit("Student '{$_SESSION['ad_stud_index']}' enrolment information was not found");
    }

    $printed_at = date('M j, Y \a\t g:i A');
    $enrolDate = date('M j, Y \a\t g:i A', strtotime($enrolDate));
    $enrolCode = strtoupper($enrolCode);

    $data_string = <<<HTML
    <div class="view_box">
        <div class="head">
            <h2>Enrolment Details</h2>
        </div>
        
        <div class="body">
            <fieldset>
                <legend>CSSPS Details</legend>
                <div class="joint">
                    <div class="label">
                        <div class="name">
                            <span>SHS Name</span>
                        </div>
                        <div class="value">
                            <span id="res_shs_placed">$school_name</span>
                        </div>
                    </div>
                    <div class="label">
                        <div class="name">
                            <span>Enrol Code</span>
                        </div>
                        <div class="value">
                            <span>$enrolCode</span>
                        </div>
                    </div>
                    <div class="label">
                        <div class="name">
                            <span>JHS Index Number</span>
                        </div>
                        <div class="value">
                            <span id="res_ad_index">$indexNumber</span>
                        </div>
                    </div>
                    <div class="label">
                        <div class="name">
                            <span>Six Aggregate</span>
                        </div>
                        <div class="value">
                            <span id="res_ad_aggregate">$aggregateScore</span>
                        </div>
                    </div>
                    <div class="label">
                        <div class="name">
                            <span>Course Chosen</span>
                        </div>
                        <div class="value">
                            <span id="res_ad_course">$program</span>
                        </div>
                    </div>
                </div>
            </fieldset>

            <fieldset>
                <legend>Personal Details</legend>
                <div class="joint">
                    <div class="label">
                        <div class="name">
                            <span>Lastname</span>
                        </div>
                        <div class="value">
                            <span id="res_ad_lname">$lastname</span>
                        </div>
                    </div>
                    <div class="label">
                        <div class="name">
                            <span>Other name(s)</span>
                        </div>
                        <div class="value">
                            <span id="res_ad_oname">$othername</span>
                        </div>
                    </div>
                    <div class="label">
                        <div class="name">
                            <span>Gender</span>
                        </div>
                        <div class="value">
                            <span id="res_ad_gender">$gender</span>
                        </div>
                    </div>
                    <div class="label">
                        <div class="name">
                            <span>JHS Attended</span>
                        </div>
                        <div class="value">
                            <span id="res_ad_jhs">$jhsName</span>
                        </div>
                    </div>
                    <div class="label">
                        <div class="name">
                            <span>JHS Location
                        </div>
                        <div class="value">
                            <span>$jhsTown</span>
                        </div>
                    </div>
                    <div class="label">
                        <div class="name">
                            <span>JHS District</span>
                        </div>
                        <div class="value">
                            <span>$jhsDistrict</span>
                        </div>
                    </div>
                    <div class="label">
                        <div class="name">
                            <span>Birthdate</span>
                        </div>
                        <div class="value">
                            <span id="res_ad_birthdate">$birthdate</span>
                        </div>
                    </div>
                    <div class="label">
                        <div class="name">
                            <span>Birth Place
                        </div>
                        <div class="value">
                            <span>$birthPlace</span>
                        </div>
                    </div>
                </div>
            </fieldset>

            <fieldset>
                <legend>Particulars of Parents / Guardian</legend>
                <div class="joint">
                    <div class="label" ng-show="ad_father_name">
                        <div class="name">
                            <span>Father's Name</span>
                        </div>
                        <div class="value">
                            <span>$fatherName</span>
                        </div>
                    </div>
                    <div class="label" ng-show="ad_father_name">
                        <div class="name">
                            <span>Occupation of Father</span>
                        </div>
                        <div class="value">
                            <span>$fatherOccupation</span>
                        </div>
                    </div>
                    <div class="label" ng-show="ad_mother_name">
                        <div class="name">
                            <span>Maiden's Name</span>
                        </div>
                        <div class="value">
                            <span>$motherName</span>
                        </div>
                    </div>
                    <div class="label" ng-show="ad_mother_name">
                        <div class="name">
                            <span>Maiden's Occupation</span>
                        </div>
                        <div class="value">
                            <span>$motherOccupation</span>
                        </div>
                    </div>
                    <div class="label" ng-show="ad_guardian_name">
                        <div class="name">
                            <span>Guardian's Name</span>
                        </div>
                        <div class="value">
                            $guardianName
                        </div>
                    </div>
                    <div class="label" ng-show="ad_postal_address">
                        <div class="name">
                            <span>Postal Address</span>
                        </div>
                        <div class="value">
                            <span>$postalAddress</span>
                        </div>
                    </div>
                    <div class="label">
                        <div class="name">
                            <span>Residential Address</span>
                        </div>
                        <div class="value">
                            <span>$residentAddress</span>
                        </div>
                    </div>
                    <div class="label">
                        <div class="name">
                            <span>Contact Number 1</span>
                        </div>
                        <div class="value">
                            <span>$primaryPhone</span>
                        </div>
                    </div>
                    <div class="label" ng-show="ad_other_phone">
                        <div class="name">
                            <span>Contact Number 2</span>
                        </div>
                        <div class="value">
                            <span>$secondaryPhone</span>
                        </div>
                    </div>
                </div>
            </fieldset>

            <fieldset>
                <legend>Interests</legend>
                <div class="joint">
                    <div class="label">
                        <div class="name">
                            <span>Interests</span>
                        </div>
                        <div class="value" id="interest_value">
                            <span id="res_ad_interest">$interest</span>
                        </div>
                    </div>
                </div>
            </fieldset>

            <fieldset>
                <legend>Other Information</legend>
                <div class="joint">
                    <div class="label">
                        <div class="name">
                            <span>Awards</span>
                        </div>
                        <div class="value">
                            <span>$award</span>
                        </div>
                    </div>
                    <div class="label">
                        <div class="name">
                            <span>Role(s) Held</span>
                        </div>
                        <div class="value">
                            <span>$position</span>
                        </div>
                    </div>
                    <div class="label">
                        <div class="name">
                            <span>Elective Class</span>
                        </div>
                        <div class="value">
                            <span id="program_display_val">N/A</span>
                        </div>
                    </div>
                </div>
            </fieldset>

            <fieldset>
                <legend>Witness Information</legend>
                <div class="joint">
                    <div class="label">
                        <div class="name">
                            <span>Witness' Name</span>
                        </div>
                        <div class="value">
                            <span>$witnessName</span>
                        </div>
                    </div>
                    <div class="label">
                        <div class="name">
                            <span>Witness' Contact</span>
                        </div>
                        <div class="value">
                            <span>$witnessPhone</span>
                        </div>
                    </div>
                </div>
            </fieldset>
        </div>
        <label for="agree" class="checkbox" style="margin-top: 10mm">
            <input type="checkbox" name="agree" id="agree" value="agree" checked>
            <span>I, <strong id="fullCandidateName">$lastname $othername</strong> do accept that my admission to this school opens a new chapter in my life. I therefore pledge to abide by all the rules and regulations of the school</span>
        </label>
    </div>

    <p class='cur_time'>Enroled on $enrolDate. This document is generated at $printed_at</p>
    HTML;

    $css = <<<CSS
    .only-print{display: none}
    #print_mode{cursor: pointer; color: blue}
    @media print{
        .only-print{display: initial}
        .hide-print{display: none}
        fieldset{display: block;margin-bottom: 1cm;border: 1px solid black;} 
        .joint{margin: 5px; display: flex; flex-wrap: wrap; gap: 5mm}
        .joint .label{flex: 1 1 auto; min-width: 40mm; border: 1px solid lightgrey;padding: 2mm 3mm;min-height: 1.25em;} 
        .label .value{color: #222;font-variant: small-caps;}
        .cur_time{margin-top: 5mm; text-align: center;}
        .checkbox{margin-top: 3mm; padding: 2mm 2mm 1mm}
    }
    CSS;

    close_connections();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enrolment Details | <?= $indexNumber ?></title>
    <style>
        <?= $css ?>
    </style>
    <script type="text/javascript">
        // This function will automatically trigger the print dialog
        window.onload = function() {
            window.print(); // Print the current page
        };
    </script>
</head>
<body>
    <div class="only-print">
        <?= $data_string ?>
    </div>
    <div class="hide-print">
        <p>Document assessible only in <u id="print_mode" onclick="window.print()">print</u> mode (ctrl + P)</p>
    </div>
</body>
</html>

