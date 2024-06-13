<?php
    include_once("auth.php");
    //set nav_point session
    $_SESSION["nav_point"] = "students";

    $stud_sum = [
        "total" => fetchData("COUNT(indexNumber) AS ttl", "cssps", "current_data=TRUE")["ttl"],
        "enroled" => fetchData("COUNT(indexNumber) AS ttl", "cssps", ["current_data=TRUE", "enroled=TRUE"], 1, "AND")["ttl"],
        "not_enroled" => fetchData("COUNT(indexNumber) AS ttl", "cssps", ["current_data=TRUE", "enroled=FALSE"], 1, "AND")["ttl"]
    ];

?>

<section class="section_container">
    <div class="content primary">
        <div class="head">
            <h2><?= number_format(floatval($stud_sum["total"])) ?></h2>
        </div>
        <div class="body">
            <span>Total Admitted Student [Current]</span>
        </div>
    </div>
    <div class="content secondary">
        <div class="head">
            <h2><?= number_format(floatval($stud_sum["enroled"])) ?></h2>
        </div>
        <div class="body">
            <span>Enroled Students [Current]</span>
        </div>
    </div>
    <div class="content orange">
        <div class="head">
            <h2><?= number_format(floatval($stud_sum["not_enroled"])) ?></h2>
        </div>
        <div class="body">
            <span>Not Enroled Students [Current]</span>
        </div>
    </div>
</section>

<section class="txt-al-c">
    <p>Use this section to make searches and edits to information of an individual student</p>
</section>

<?php require "$rootPath/admin/student-search.php" ?>

<script src="<?= "$url/admin/assets/scripts/student-search.min.js?v=".time() ?>"></script>
