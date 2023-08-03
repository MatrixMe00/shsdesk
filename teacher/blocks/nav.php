<nav id="lhs" class="<?= intval(date("H")) >= 18 ? "dark" : "primary" ?>">
    <div class="head">
        <div class="txt-al-c white sp-med">
            <img src="assets/images/icons/person-outline.svg" class="rect-xxsm" alt="">
        </div>
        <h3 class="flex-all-center txt-al-c flex-column gap-sm">
            <span><?= $teacher["lname"]." ".$teacher["oname"] ?></span>
            <span class="txt-fs">[<?= $teacher["user_username"] ?>]</span>
        </h3>
    </div>
    <div class="nav_links">
        <div id="ham">
            <span></span>
            <span></span>
            <span></span>
        </div>
        <div class="buttons">
            <div data-href="<?= "$url/pages/dashboard.php" ?>" class="tab" data-document-title="<?= $teacher["lname"] ?> Dashboard"
                data-active="dashboard">
                <span>Dashboard</span>
            </div>
            <div data-href="" class="tab logout">
                <span>Logout</span>
            </div>
            <div data-href="<?= "$url/pages/classes.php" ?>" class="tab" data-document-title="<?= $teacher["lname"] ?> Classes"
                data-active="classes">
                <span>My Classes</span>
            </div>
            <div id="my_report" data-href="<?= "$url/pages/results.php" ?>" class="tab" data-document-title="<?= $teacher["lname"] ?> Result Entry"
                data-active="results">
                <span>Result Entry</span>
            </div>
            <div id="" data-href="<?= "$url/pages/uploaded.php" ?>" class="tab" data-document-title="<?= $teacher["lname"] ?> Result Summary"
                data-active="res_sum" data-current-tab="approve">
                <span>My Results</span>
            </div>
            <div id="" data-href="<?= "$url/pages/documents.php" ?>" class="tab" data-document-title="<?= $teacher["lname"] ?> Documents"
                data-active="documents" data-current-tab="">
                <span>My Documents</span>
            </div>
            <div data-href="<?= "$url/pages/profile.php" ?>" class="tab" data-document-title="<?= $teacher["lname"] ?> Profile" data-active="profile">
                <span>My Profile</span>
            </div>
        </div>
    </div>
    <input type="hidden" id="active-page" value="<?= !isset($_SESSION["active-page"]) ? "dashboard" : $_SESSION["active-page"] ?>">
</nav>