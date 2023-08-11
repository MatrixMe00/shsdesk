<nav class="<?= intval(date("H")) >= 18 ? "dark" : "primary" ?>">
    <div class="head">
        <div class="logo">
            <img src="" alt="">
        </div>
        <h2 class="name txt-al-c">
            <?= getSchoolDetail($student["school_id"])["schoolName"] ?>
        </h2>
    </div>
    <h3 class="flex-all-center flex-column gap-sm">
        <span><?= $student["Lastname"]." ".$student["Othernames"] ?></span>
        <span class="txt-fs"><?php $code = fetchData1("accessToken","accesstable","indexNumber='{$student['indexNumber']}' AND status=1 ORDER BY expiryDate DESC"); 
            echo $code == "empty" ? "[No access Code]" : "[{$code['accessToken']} Active]" ?></span>
    </h3>
    <div class="nav_links">
        <div id="ham">
            <span></span>
            <span></span>
            <span></span>
        </div>
        <div class="buttons">
            <div data-href="" class="tab logout">
                <span>Logout</span>
            </div>
            <div id="my_report" data-href="<?= "$url/pages/report.php" ?>" class="tab" data-document-title="<?= $student["Lastname"] ?> Reports"
                data-active="report">
                <span>My Reports</span>
            </div>
            <div data-href="<?= "$url/pages/accessCode.php" ?>" class="tab" data-document-title="Get Access Code" data-active="code">
                <span>Get Access Code</span>
            </div>
        </div>
    </div>
    <input type="hidden" id="active-page" value="<?= !isset($_SESSION["active-page"]) ? "report" : $_SESSION["active-page"] ?>">
</nav>