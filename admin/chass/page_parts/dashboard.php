<?php include_once("auth.php");
    //set nav_point session
    $_SESSION["nav_point"] = "dashboard";
    $school_type = decimalIndexArray(fetchData("id", "school_category", "head = '$user_role'", 0));
    if($school_type){
        $school_type = implode(",",array_column($school_type, "id"));
    }
    $school_type_name = $user_role == "chass_t" ? "Technical" : "SHS/SHT";
    $current_admission_year = getAcademicYear(now(), false);
    $admission_table_join = ["join" => "schools cssps", "on" => "id schoolID", "alias" => "s c"];

    // count the number of registered schools
    $school_count = fetchData("COUNT(id) as total", "schools", "category IN ($school_type)")["total"];
    $student_count_c = fetchData(
        "COUNT(indexNumber) as total", $admission_table_join,
        ["s.category IN ($school_type)","c.current_data = TRUE", "c.academic_year='$current_admission_year'"], where_binds: "AND"
    )["total"];
    $student_r_count_c = fetchData(
        "COUNT(indexNumber) as total", $admission_table_join,
        ["s.category IN ($school_type)", "c.current_data = TRUE", "c.academic_year='$current_admission_year'", "c.enroled = TRUE"], where_binds: "AND"
    )["total"];
    $student_count = fetchData(
        "COUNT(indexNumber) as total", $admission_table_join,
        ["s.category IN ($school_type)", "c.enroled = TRUE"], where_binds: "AND"
    )["total"];
?>

<!-- current admission details -->
<section class="section_container">
    <div class="content light">
        <div class="head">
            <h2><?= $student_count_c ?></h2>
        </div>
        <div class="body">
            <span><?= formatAcademicYear($current_admission_year, false) ?> CSSPS Students [uploaded]</span>
        </div>
    </div>
    <div class="content light">
        <div class="head">
            <h2><?= $student_r_count_c ?></h2>
        </div>
        <div class="body">
            <span><?= formatAcademicYear($current_admission_year, false) ?> Students [enroled]</span>
        </div>
    </div>
    <div class="content light">
        <div class="head">
            <h2>GH¢ <?= number_format($role_price * $system_usage_price, 2) ?></h2>
        </div>
        <div class="body">
            <span>Price per enroled student</span>
        </div>
    </div>
</section>

<!-- all time -->
<section class="section_container">
    <div class="content light">
        <div class="head">
            <h2><?= $school_count ?></h2>
        </div>
        <div class="body">
            <span><?= $school_type_name ?> School<?= $school_count != 1 ? "s" : "" ?></span>
        </div>
    </div>
    <div class="content light">
        <div class="head">
            <h2><?= number_format($student_count) ?></h2>
        </div>
        <div class="body">
            <span>Student<?= $student_count != 1 ? "s" : "" ?> [All time]</span>
        </div>
    </div>
</section>
<?php close_connections() ?>