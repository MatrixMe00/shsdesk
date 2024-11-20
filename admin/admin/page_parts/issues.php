<?php
    require "auth.php";

    $_SESSION["nav_point"] = "issues";

    $n_enroled = decimalIndexArray(fetchData("c.indexNumber, c.Lastname, c.Othernames, e.enrolDate", 
        ["join" => "enrol_table cssps", "on" => "indexNumber indexNumber", "alias" => "e c"],
        ["c.schoolID=$user_school_id", "c.current_data=TRUE", "c.Enroled=FALSE"], 
        0, "AND", "left"));
    $enroled = fetchData("COUNT(indexNumber) as total", "enrol_table", "shsID=$user_school_id AND current_data = TRUE")["total"];
?>

<section class="section_container">
    <div class="content primary">
        <div class="head">
            <h2><?= $enroled ?></h2>
        </div>
        <div class="body">
            <span>Student Enrolment Recorded</span>
        </div>
    </div>

    <div class="content <?= $n_enroled ? "red":"green" ?>">
        <div class="head">
            <h2><?= $n_enroled ? count($n_enroled) : 0 ?></h2>
        </div>
        <div class="body">
            <span>Issues</span>
        </div>
    </div>
</section>

<section>
    <?php if(!$n_enroled) : ?>
    <div class="body empty">
        <p>You have no issue</p>
    </div>
    <?php else: ?>
    <div class="body">
        <div class="btn p-lg w-full txt-al-c">
            <button class="wmax-4xs w-full secondary" id="resolve" data-school-id="<?= $user_school_id ?>">Resolve All Issues</button>
        </div>

        <table>
            <thead>
                <tr>
                    <td>Index Number</td>
                    <td>Lastname</td>
                    <td>Othernames</td>
                    <td>Time recorded</td>
                </tr>
            </thead>
            <tbody>
                <?php foreach($n_enroled as $student): ?>
                <tr>
                    <td><?= $student["indexNumber"] ?></td>
                    <td><?= $student["Lastname"] ?></td>
                    <td><?= $student["Othernames"] ?></td>
                    <td><?= date("d M Y, H:i:sa" ,strtotime($student["enrolDate"])) ?></td>
                </tr>
                <?php endforeach ?>
            </tbody>
            <tfoot></tfoot>
        </table>
    </div>
    <?php endif ?>
</section>

<script>
    $(document).ready(function(){
        $("#resolve").click(function(){
            const school_id = $(this).attr("data-school-id");
            const button = $(this);

            $.ajax({
                url: "./admin/submit.php",
                data: {submit: "resolve_issues", school_id: school_id},
                method: "POST",
                beforeSend: function(){
                    button.html("Resolving...");
                },
                success: function(data){
                    button.html("Resolve All Issues");

                    if(data == "success"){
                        alert_box("All issues resolved", "success");
                        location.reload();
                    }else{
                        alert_box(data, "danger", 8);
                    }
                },
                error: function(xhr, textStatus, errorThrown){
                    alert_box(errorThrown);
                    console.log(xhr);
                }
            })
        })
    })
</script>
<?php close_connections() ?>