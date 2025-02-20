<?php
    include_once("auth.php");

    if(isset($_REQUEST["school_id"]) && !empty($_REQUEST["school_id"])){
        $user_school_id = $_REQUEST["school_id"];
        $user_details = getUserDetails($_REQUEST["user_id"]);
    }else{
        //set nav_point session
        $_SESSION["nav_point"] = "admission";
    }
?>
    <?php if($_SESSION["admin_mode"] == "admission") :
        require_once("components/school_details.php");
    else: 
        $school_settings = get_school_settings();
?>
    <section class="btn_section sp-xlg-tp">
        <div class="btn flex flex-wrap gap-sm sm-auto p-lg">
            <button class="plain-r primary btn-item" data-section="record_type">Result Type</button>
            <button class="plain-r primary btn-item" data-section="record_date">Result Entry Period</button>
            <button class="plain-r primary btn-item" data-section="system_settings">System Settings</button>
            <?php if(isset($school_settings["program_swap"]) && $school_settings["program_swap"]): ?>
            <button class="plain-r primary btn-item" data-section="program_swap_section">Class Promotions</button>
            <?php endif; ?>
        </div>
    </section>
    
    <section class="section_container sp-lg-tp btn_connect" id="record_date">
        <?php require_once("components/records_date_form.php") ?>
    </section>

    <section class="section_container sp-lg-tp btn_connect" id="system_settings">
        <div class="body">
            <?php require_once("components/system_settings.php") ?>
        </div>
    </section>

    <?php if(isset($school_settings["program_swap"]) && $school_settings["program_swap"]): ?>
    <section class="section_container sp-lg-tp btn_connect" id="program_swap_section">
        <div class="body">
            <?php require_once("components/program-swap.php") ?>
        </div>
    </section>
    <?php endif; ?>
    
    <div class="btn_connect" id="record_type">
        <section class="section_container">
            <?php require_once("components/result_type_form.php") ?>
        </section>
        
        <section class="section_container grade_table" id="empty">
            <p class="color-dark sp-xxlg-tp sp-lg-lr">You have not specified the type of results for your school yet</p>
        </section>

        <?php require_once("components/result_type_sections.php") ?>
    </div>

    <script src="<?= "$url/admin/admin/assets/scripts/admission_records.js?v=".time() ?>"></script>
    <?php endif; close_connections() ?>

    <script src="<?php echo $url?>/assets/scripts/form/general.min.js?v=<?php echo time()?>"></script>
    <script src="<?php echo $url?>/admin/assets/scripts/tinymce/jquery.tinymce.min.js"></script>
    <script src="<?php echo $url?>/admin/assets/scripts/tinymce/tinymce.min.js"></script>
    <script src="<?php echo $url?>/admin/assets/scripts/tinymce.min.js?v=<?php echo time()?>"></script>
    <script src="<?php echo $url?>/admin/admin/assets/scripts/admission.min.js?v=<?php echo time()?>"></script>
    <script src="<?php echo $url?>/admin/admin/assets/scripts/school_settings.js?v=<?php echo time()?>"></script>