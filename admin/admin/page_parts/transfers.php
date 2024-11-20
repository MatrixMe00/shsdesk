<?php
    require "auth.php";

    $_SESSION["nav_point"] = "transfers";

    $transfers = get_transfers();
    $academic_year = getAcademicYear(now());
    $pending = array_merge($transfers["pending"]["data"], $transfers["request"]["data"]);
    $responded = array_merge($transfers["rejected"]["data"], $transfers["accepted"]["data"]);
    $schools = decimalIndexArray(fetchData("id, schoolName", "schools", ["Active=TRUE", "id != $user_school_id"], 0, "AND"));
    $this_school = $transfers["this_school"];
?>

<section class="section_container">
    <div class="content primary">
        <div class="head">
            <h2><?= $transfers["outgoing"] ?></h2>
        </div>
        <div class="body">
            <span>Outgoing Transfer Request</span>
        </div>
    </div>

    <div class="content pink">
        <div class="head">
            <h2><?= $transfers["incoming"] ?></h2>
        </div>
        <div class="body">
            <span>Incoming Transfer Request</span>
        </div>
    </div>

    <div class="content green">
        <div class="head">
            <h2><?= $transfers["pending"]["count"] ?></h2>
        </div>
        <div class="body">
            <span>Pending Transfers</span>
        </div>
    </div>

    <div class="content secondary">
        <div class="head">
            <h2><?= $transfers["accepted"]["count"] ?></h2>
        </div>
        <div class="body">
            <span>Transfers Completed [<?= formatAcademicYear($academic_year, false) ?>]</span>
        </div>
    </div>
</section>

<section>
    <div class="display light">
        <p style="text-align: center; padding: 1em">
            Use this section to make or accept student transfers across the system. Students not enroled and in the current admission year can be transfered to other schools registered with us. 
            You can also use this platform to make a transfer request for a specified student from another school. Approved student transfers will automatically be added to your uploaded cssps list
        </p>
    </div>
</section>

<section>
    <div id="action">
        <div class="body btn flex flex-wrap wrap-half w-full-child w-full wmax-med sm-auto gap-sm p-med flex-eq">
            <button class="light plain-r section-btn" data-section-id="transfer">New Transfer</button>
            <button class="light plain-r section-btn" data-section-id="my-request">My Requests</button>
            <button class="light plain-r section-btn" data-section-id="pending">Pending</button>
            <button class="light plain-r section-btn" data-section-id="reviewed">Reviewed Transfers</button>
        </div>
    </div>
</section>

<section id="transfer" class="no_disp btn_section">
    <form action="<?php echo $url?>/admin/admin/submit.php" method="post" class="" name="adminAddStudent">
        <div class="head">
            <h2>Transfer a student</h2>
        </div>
        <div class="body">
            <div class="message_box success no_disp">
                <span class="message"></span>
                <div class="close"><span>&cross;</span></div>
            </div>
            <div class="joint">
                <input type="hidden" class="reset-element" name="school_self" data-init="<?= $user_school_id ?>" value="<?= $user_school_id?>">
                <label for="mode" class="flex-column">
                    <select name="mode" id="mode" title="Select type of transfer" required>
                        <option value="">Select type of transfer</option>
                        <option value="request">Request Transfer</option>
                        <option value="process">Process Transfer</option>
                    </select>
                    <span class="item-event info">Select 'request transfer' if the student is to be transfered to your school, otherwise use 'process transfer' if student is to be transfered from your school</span>
                </label>
                <label for="student_index" class="self-align-start flex-column">
                    <input type="text" name="student_index" id="student_index"
                    autocomplete="off" placeholder="Index Number" list="student-search-list" title="Index Number of transfer student">
                    <span class="item-event info">Provide the JHS index number of the transfer student. <span id="extra_info"></span></span>
                    <datalist id="student-search-list" style="z-index: 99;">
                    </datalist>
                </label>

                <?php if($schools): ?>
                <label for="school_transfer" class="flex-column">
                    <select name="school_transfer" id="school_transfer" title="Select type of transfer" required>
                        <option value="">Select transfer school</option>
                        <?php foreach($schools as $school): ?>
                        <option value="<?= $school["id"] ?>"><?= $school["schoolName"] ?></option>
                        <?php endforeach; ?>
                    </select>
                    <span class="item-event info" id="transfer_info">Select the transfer school</span>
                </label>
                <?php endif; ?>
            </div>
        </div>
        <div class="foot">
            <div class="flex flex-wrap gap-sm flex-eq wmax-xs sm-auto">
                <label for="submit" class="btn w-full sm-unset sp-unset">
                    <button type="submit" name="submit" class="primary w-fluid sp-med xs-rnd" value="transfer_student">Save</button>
                </label>
                <label for="cancel" class="btn w-full sm-unset sp-unset">
                    <button type="reset" name="cancel" class="red w-fluid sp-med xs-rnd" id="reset_btn">Reset</button>
                </label>
            </div>
        </div>
    </form>
</section>

<section id="pending" class="no_disp btn_section">
    <table class="full">
        <thead>
            <td>Index Number</td>
            <td>Fullname</td>
            <td>Programme</td>
            <td>Transfer From</td>
            <td>Transfer To</td>
            <td>Request Date</td>
        </thead>
        <tbody>
            <?php if($transfers["pending"]["count"]): ?>
                <?php foreach($transfers["pending"]["data"] as $request): ?>
                <tr data-item-id="<?= $request["id"] ?>">
                    <td><?= $request["index_number"] ?></td>
                    <td><?= $request["fullname"] ?></td>
                    <td><?= $request["programme"] ?></td>
                    <td title="<?= $request["school_from"] ?>"><?= $request["school_from"] == $this_school ? "Me" : $request["from_abbr"] ?></td>
                    <td title="<?= $request["school_to"] ?>"><?= $request["school_to"] == $this_school ? "Me" : $request["to_abbr"] ?></td>
                    <td><?= date("M j, Y h:iA", strtotime($request["created_at"])) ?></td>
                    <td>
                        <span class="item-event" data-type = "accept" data-is-request="<?= intval($request["is_request"]) ?>">Accept</span>
                        <span class="item-event" data-type = "reject" data-is-request="<?= intval($request["is_request"]) ?>">Deny</span>
                    </td>
                </tr>
            <?php endforeach; else: ?>
                <tr class="empty">
                    <td colspan="6">No pending transfers found for <?= $academic_year ?></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</section>

<section id="my-request" class="no_disp btn_section">
    <table class="full">
        <thead>
            <td>Index Number</td>
            <td>Fullname</td>
            <td>Programme</td>
            <td>Transfer From</td>
            <td>Transfer To</td>
            <td>Request Date</td>
        </thead>
        <tbody>
            <?php if($transfers["request"]["count"]): ?>
                <?php foreach($transfers["request"]["data"] as $request): ?>
                <tr data-item-id="<?= $request["id"] ?>">
                    <td><?= $request["index_number"] ?></td>
                    <td><?= $request["fullname"] ?></td>
                    <td><?= $request["programme"] ?></td>
                    <td title="<?= $request["school_from"] ?>"><?= $request["school_from"] == $this_school ? "Me" : $request["from_abbr"] ?></td>
                    <td title="<?= $request["school_to"] ?>"><?= $request["school_to"] == $this_school ? "Me" : $request["to_abbr"] ?></td>
                    <td><?= date("M j, Y h:iA", strtotime($request["created_at"])) ?></td>
                    <td>
                        <?php if($request["is_request"] && $request["school_to"] == $this_school): ?>
                        <span class="item-event info">Pending</span>
                        <?php else: ?>
                        <span class="item-event" data-type = "accept" data-is-request="<?= intval($request["is_request"]) ?>">Accept</span>
                        <span class="item-event" data-type = "reject" data-is-request="<?= intval($request["is_request"]) ?>">Deny</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; else: ?>
                <tr class="empty">
                    <td colspan="6">No pending transfer requests for <?= $academic_year ?></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</section>

<section id="reviewed" class="no_disp btn_section">
    <table class="full">
        <thead>
            <td>Index Number</td>
            <td>Fullname</td>
            <td>Programme</td>
            <td>Transfer From</td>
            <td>Transfer To</td>
            <td>Response Date</td>
        </thead>
        <tbody>
            <?php if($responded): ?>
                <?php foreach($responded as $request): ?>
                <tr data-item-id="<?= $request["id"] ?>">
                    <td><?= $request["index_number"] ?></td>
                    <td><?= $request["fullname"] ?></td>
                    <td><?= $request["programme"] ?></td>
                    <td title="<?= $request["school_from"] ?>"><?= $request["school_from"] == $this_school ? "Me" : $request["from_abbr"] ?></td>
                    <td title="<?= $request["school_to"] ?>"><?= $request["school_to"] == $this_school ? "Me" : $request["to_abbr"] ?></td>
                    <td><?= date("M j, Y h:iA", strtotime($request["updated_at"])) ?></td>
                    <td>
                        <?php $action = $request["status"] == "rejected" ? ["text" => "Re-Submit", "type" => "resubmit"] : ["text" => "Transfered", "class" => "info"] ?>
                        <span class="item-event <?= $action["class"] ?? "" ?>" data-type="<?= $action["type"] ?? "" ?>" data-is-request="<?= intval($request["is_request"]) ?>"><?= $action["text"] ?></span>
                    </td>
                </tr>
            <?php endforeach; else: ?>
                <tr class="empty">
                    <td colspan="6">No reviewed transfers found for <?= $academic_year ?></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</section>

<script>
    $(document).ready(function(){
        $(".section-btn").click(function(){
            const section = $(this).attr("data-section-id");
            $(".btn_section:not(.no_disp)").addClass("no_disp");
            $("#" + section).removeClass("no_disp");
        })

        $(".section-btn").first().click();

        let debouce;

        $("#student_index").on("input", function(){
            const value = $(this).val();
            const mode = $("select#mode").val();
            const datalist = $("datalist#student-search-list");
            let in_datalist = false;
            clearTimeout(debouce);

            if(datalist.html() != "" && datalist.find("option[value=" + value + "]").length > 0){
                in_datalist = true;
                if(mode == "request"){
                    // auto select school
                    const schoolID = datalist.find("option[value=" + value + "]").attr("data-school-id");

                    $("#school_transfer").val(schoolID);
                }
            }

            if(value.length > 2 && mode != "" && !in_datalist){
                debounce = setTimeout(() => {
                    ajaxCall({
                        url: "admin/submit.php",
                        formData: {submit: "search_transfer_student", search: value, mode: mode},
                        returnType: "json",
                        beforeSend: function(){
                            datalist.empty();
                            datalist.append("<option value=\"\" disabled>Loading...</option>");
                        }
                    }).then(response => {
                        datalist.empty();

                        if(response.status){
                            response.data.forEach(element => {
                                const option = "<option value=\"" + element.indexNumber + "\" data-school-id=\"" + element.schoolID + "\">" + element.fullname + "</option>";
                                datalist.append(option);
                            });
                        }else{
                            alert_box(response.data, "danger");
                        }
                    }).catch(er => {
                        alert_box("Undefined error received", "danger");
                        console.log(er);                    
                    })
                }, 700);
            }else if(value.length > 2 && mode == ""){
                alert_box("Select a mode to pull suggestions")
            }
        })

        $("select#mode").change(function(){
            const value = $(this).val();
            let message1 = "", message2 = "";
            switch (value) {
                case "request":
                    message1 = "Index Number provided will not be an index number that you have uploaded via CSSPS document.";
                    message2 = "Transfer request will be made at the selected school above";
                    break;
                case "process":
                    message1 = "Index Number provided should be an index number you uploaded via CSSPS document.";
                    message2 = "Select the school to transfer the student to";
                    break;
                default:
                    break;
            }

            $("#extra_info").text(message1);
            $("#transfer_info").text(message2);
        })

        $("#reset_btn").click(function(){
            $("form .reset-element").val(function(){
                $(this).attr("data-init");
            })
        })

        $("form").submit(async function(e){
            e.preventDefault();
            const form = $(this);

            response = await formSubmit(form, form.find("button[name=submit]"), true)
            
            if(response === true){
                alert_box("Transfer " + $("select#mode").val() + " completed");
                $("#lhs .active").click();
            }
        })

        $(".item-event:not(.info)").click(function(){
            const element = $(this);
            const original_text = element.text();
            let type = element.attr("data-type");
            const is_request = element.attr("data-is-request");
            const td = element.parents("tr").first();
            const item_id = td.attr("data-item-id");
            ajaxCall({
                url: "admin/submit.php",
                formData: {submit: "parse_transfer", id: item_id, type: type, is_request: is_request},
                beforeSend: function(){
                    element.addClass("info").text("Loading");
                },
                method: "post"
            }).then(response => {
                element.text(original_text).removeClass("info");

                if(response == "success"){
                    if(type == "accept" || type == "reject"){
                        type += "ed";
                    }else{
                        type = "Pending";
                    }

                    element.siblings(".item-event").remove();
                    element.addClass("info").text(type);
                }else{
                    alert_box(response, "danger");
                }
            }).catch(err => {
                console.log(err);
                alert_box(err.toString());
                element.text(original_text).removeClass("info");
            })
        })
    })
</script>
<?php close_connections() ?>
