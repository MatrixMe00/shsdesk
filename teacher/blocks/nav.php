<nav id="lhs">
    <div class="head">
        <div class="txt-al-c white sp-med">
            <img src="assets/images/icons/person-outline.svg" class="rect-xxsm" alt="">
        </div>
        <h3 class="flex-all-center txt-al-c flex-column gap-sm">
            <span><?= $teacher["lname"]." ".$teacher["oname"] ?></span>
            <span id="nonactive_access" class="txt-fs">[<?= $teacher["user_username"] ?>]</span>
        </h3>
    </div>
    <div class="nav_links">
        <div id="ham">
            <span></span>
            <span></span>
            <span></span>
        </div>
        <div class="buttons">
            <div data-href="<?= "$url/components/dashboard.php" ?>" class="tab primary active" data-document-title="<?= $teacher["lname"] ?> Dashboard"
                data-active="dashboard">
                <span>Dashboard</span>
            </div>
            <div data-href="" class="tab logout">
                <span>Logout</span>
            </div>
            <div data-href="<?= "$url/components/classes.php" ?>" class="tab" data-document-title="<?= $teacher["lname"] ?> Classes"
                data-active="classes">
                <span>My Classes</span>
            </div>
            <div id="my_report" data-href="<?= "$url/components/results.php" ?>" class="tab" data-document-title="<?= $teacher["lname"] ?> Results"
                data-active="results">
                <span>Student Results</span>
            </div>
            <div data-href="<?= "$url/components/profile.php" ?>" class="tab" data-document-title="<?= $teacher["lname"] ?> Profile" data-active="profile">
                <span>My Profile</span>
            </div>
        </div>
    </div>
    <input type="hidden" id="active-page" value="<?= !isset($_SESSION["active-page"]) ? "dashboard" : $_SESSION["active-page"] ?>">
</nav>