<nav>
    <div class="head">
        <div class="logo">
            <img src="" alt="">
        </div>
        <h1 class="name">
            School Name
        </h1>
    </div>
    <h3 class="flex-all-center flex-column gap-sm">
        <span>Student Name</span>
        <span id="active_access" class="no_disp txt-fs">[ABCD1234 Active]</span>
        <span id="nonactive_access" class="txt-fs">[No access Code]</span>
    </h3>
    <div class="nav_links">
        <div id="ham">
            <span></span>
            <span></span>
            <span></span>
        </div>
        <div class="buttons">
            <div data-href="<?= "$url/components/dashboard.php" ?>" class="tab primary active" data-document-title="<?= $student["Lastname"] ?> Dashboard"
                data-active="dashboard">
                <span>Dashboard</span>
            </div>
            <div data-href="" class="tab logout">
                <span>Logout</span>
            </div>
            <div data-href="<?= "$url/components/subjects.php" ?>" class="tab" data-document-title="<?= $student["Lastname"] ?> Subjects"
                data-active="subject">
                <span>My Subjects</span>
            </div>
            <div id="my_report" data-href="<?= "$url/components/report.php" ?>" class="tab" data-document-title="<?= $student["Lastname"] ?> Reports"
                data-active="report">
                <span>My Reports</span>
            </div>
            <div data-href="<?= "$url/components/accessCode.php" ?>" class="tab" data-document-title="Get Access Code" data-active="code">
                <span>Get Access Code</span>
            </div>
            <div data-href="<?= "$url/components/profile.php" ?>" class="tab" data-document-title="<?= $student["Lastname"] ?> Profile" data-active="profile">
                <span>My Profile</span>
            </div>
        </div>
    </div>
    <input type="hidden" id="active-page" value="<?= !isset($_SESSION["active-page"]) ? "dashboard" : $_SESSION["active-page"] ?>">
</nav>