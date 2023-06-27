<?php include_once($_SERVER["DOCUMENT_ROOT"]."/includes/session.php");

    //add nav point session
    $_SESSION["nav_point"] = "ussd";
?>

<section class="section_container">
    <div class="content green">
        <div class="head">
            <h2>
                <?= fetchData1("COUNT(school_id) as total","school_ussds", "status='approve'")["total"] ?>
            </h2>
        </div>
        <div class="body">
            <span>Approved SMS USSDs</span>
        </div>
    </div>
    <div class="content red">
        <div class="head">
            <h2>
                <?= fetchData1("COUNT(school_id) as total","school_ussds", "status='reject'")["total"] ?>
            </h2>
        </div>
        <div class="body">
            <span>Rejected SMS USSDs</span>
        </div>
    </div>
    <div class="content secondary">
        <div class="head">
            <h2>
                <?= fetchData1("COUNT(school_id) as total","school_ussds", "status='pending'")["total"] ?>
            </h2>
        </div>
        <div class="body">
            <span>Pending SMS USSDs</span>
        </div>
    </div>
</section>

<section class="sp-lg-tp">
    <h3 class="txt-al-c sm-lg-b">View Controls</h3>
    <div class="btn w-full p-med gap-sm flex-all-center flex-wrap">
        <button data-section-id="pending" class="plain-r primary control_btn">Pending</button>
        <button data-section-id="reject" class="plain-r red control_btn">Rejected</button>
        <button data-section-id="approve" class="plain-r green control_btn">Approved</button>
    </div>
</section>

<section id="pending" class="control_section sp-xlg-tp no_disp">
    <h3 class="txt-al-c">Pending SMS USSDs</h3>
    <?php
        $ussds = fetchData1("school_id, sms_id", "school_ussds","status='pending'", 0);
        if(is_array($ussds)) :
    ?>
    <div class="btn m-sm p-med">
        <button class="primary btn_all" disabled data-value="approve_all">Approve All</button>
        <button class="red btn_all" disabled data-value="reject_all">Reject All</button>
    </div>
    <div class="body flex flex-wrap sp-med gap-sm">
        <?php
            if(!array_key_exists(0, $ussds)){
                $ussds = [$ussds];
            }

            foreach($ussds as $ussd) :
        ?>
        <div class="flex w-fit flex-column sm-rnd gap-sm lt-shade sp-med">
            <h4 class=""><?= getSchoolDetail($ussd["school_id"])["schoolName"] ?></h4>
            <span>USSD: <?= $ussd["sms_id"] ?></span>
            <?php if(intval($user_details["role"]) == 1): ?>
            <div class="foot">
                <span class="item-event approve" data-value="approve" data-id="<?= $ussd["school_id"] ?>">Approve</span>
                <span class="item-event reject" data-value="reject" data-id="<?= $ussd["school_id"] ?>">Reject</span>
            </div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
    <?php else : ?>
    <div class="empty lt-shade sm-lg-t txt-al-c p-xlg">
        <p>There are no pending USSDs</p>
    </div>
    <?php endif; ?>
</section>

<section id="approve" class="control_section sp-xlg-tp no_disp">
    <h3 class="txt-al-c">Approved SMS USSDs</h3>
    <?php
        $ussds = fetchData1("school_id, sms_id", "school_ussds","status='approve'", 0);
        if(is_array($ussds)) :
    
        if(!array_key_exists(0, $ussds)){
            $ussds = [$ussds];
        }

        foreach($ussds as $ussd) :
    ?>
        <div class="flex w-fit flex-column sm-rnd gap-sm lt-shade sp-med">
            <h4 class=""><?= getSchoolDetail($ussd["school_id"])["schoolName"] ?></h4>
            <span>USSD: <?= $ussd["sms_id"] ?></span>
            <?php if(intval($user_details["role"]) == 1): ?>
            <div class="foot">
                <span class="item-event reject" data-value="reject" data-id="<?= $ussd["school_id"] ?>">Reject</span>
            </div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
    <?php else : ?>
    <div class="empty lt-shade sm-lg-t txt-al-c p-xlg">
        <p>There are no approved USSDs</p>
    </div>
    <?php endif; ?>
</section>

<section id="reject" class="control_section sp-xlg-tp no_disp">
    <h3 class="txt-al-c">Rejected SMS USSDs</h3>
    <?php
        $ussds = fetchData1("school_id, sms_id", "school_ussds","status='reject'", 0);
        if(is_array($ussds)) :
    
        if(!array_key_exists(0, $ussds)){
            $ussds = [$ussds];
        }

        foreach($ussds as $ussd) :
    ?>
        <div class="flex w-fit flex-column sm-rnd gap-sm lt-shade sp-med">
            <h4 class=""><?= getSchoolDetail($ussd["school_id"])["schoolName"] ?></h4>
            <span>USSD: <?= $ussd["sms_id"] ?></span>
            <?php if(intval($user_details["role"]) == 1): ?>
            <div class="foot">
                <span class="item-event approve" data-value="approve" data-id="<?= $ussd["school_id"] ?>">Approve</span>
            </div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
    <?php else : ?>
    <div class="empty lt-shade sm-lg-t txt-al-c p-xlg">
        <p>There are no rejected USSDs</p>
    </div>
    <?php endif; ?>
</section>

<script>
    $(document).ready(function(){
        //page variables and constants
        const current_tab = $("#lhs .item.active").attr("data-current-tab")

        //page functions
        $(".control_btn").click(function(){
            $(".control_btn:not(.plain-r)").addClass("plain-r")
            $(this).removeClass("plain-r")

            $("#lhs .item.active").attr("data-current-tab",$(this).attr("data-section-id"))
            
            $(".control_section").addClass("no_disp")
            $("#" + $(this).attr("data-section-id")).removeClass("no_disp")
        })

        $(".item-event").click(function(){
            const val = $(this).attr("data-value")
            const sid = $(this).attr("data-id")

            $.ajax({
                url: "./superadmin/submit.php",
                data: {submit: "update_ussd_status", value: val, school_id: sid},
                timeout: 30000,
                success: function(response){
                    if(response == "update-success"){
                        alert_box("Status was changed successfully", "success")
                        $("#lhs .item.active").click()
                    }else{
                        alert_box(response, "danger", 7)
                    }
                },
                error: function(xhr){
                    let message = ""

                    if(xhr.statusText == "timeout"){
                        message = "Connection was timed out due to a slow network detected. Please check and try again"
                    }else[
                        message = xhr.responseText
                    ]

                    alert_box(message, "danger", 7)
                }
            })
        })

        $(".btn_all").click(function(){
            alert_box("Disabled", "primary", 3)
        })

        //onready executions
        $(".control_btn[data-section-id=" + current_tab + "]").click()
    })
</script>