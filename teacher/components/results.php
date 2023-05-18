<?php 
    include_once("compSession.php"); 
    $_SESSION["active-page"] = "results";

    //useful variables in this page
    $course_ids = explode(" ", $teacher["course_id"]);
    $program_ids = explode(" ", $teacher["program_ids"]);
    $result_type = fetchData("school_result","admissiondetails","schoolID=".$teacher["school_id"])["school_result"];
?>
<input type="hidden" name="result_type" id="result_type" value="<?= $result_type ?>">
<section class="d-section lt-shade">
    <div class="head txt-al-c sm-med-b m-sm-b">
        <h2>Draw out Class List</h2>
        <p>Please select a class list to draw out</p>
    </div>
    <form action="" class="flex w-full flex-column gap-sm">
        <div class="joint gap-sm">
            <label for="class">
                <select class="w-full sp-xlg" name="class" id="class">
                    <option value="">Select Class</option>
                    <?php 
                        $classes = implode(" ", $program_ids);
                        $sql = "SELECT program_id, program_name FROM program WHERE '$classes' LIKE CONCAT('%', program_id, ' ', '%')";
                        $query = $connect2->query($sql);
                        while($class=$query->fetch_assoc()) :
                    ?>
                    <option value="<?= $class["program_id"] ?>"><?= $class["program_name"] ?></option>
                    <?php endwhile; ?>
                </select>
            </label>
            <label for="year">
                <select class="w-full sp-xlg" name="year" id="year">
                    <option value="">Select year</option>
                    <option value="">2020</option>
                    <option value="">2021</option>
                    <option value="">2022</option>
                </select>
            </label>
            <label for="semester">
                <select class="w-full sp-xlg" name="semester" id="semester">
                    <option value="">Select Semester</option>
                    <option value="">Semester 1</option>
                    <option value="">Semester 2</option>
                </select>
            </label>
        </div>
        <div class="btn wmax-xs w-full sm-auto">
            <button class="primary sm-rnd sp-xlg w-full" name="submit" value="search_class">Draw Out</button>
        </div>
    </form>
</section>

<section class="lt-shade d-section sm-xlg-t m-xlg-tp">
    <div class="head">
        <h2>Result Slip For Classname</h2>
    </div>
    <div class="form sm-xlg-b">
        <label for="search" class="flex flex-column">
            <span class="label_title">Search for a specific student</span>
            <input type="search" name="search" id="search" placeholder="Search..." autocomplete="off"
                class="w-full sp-lg">
        </label>
    </div>

    <table id="result_slip" class="full light">
        <thead>
            <td>Index Number</td>
            <td>Full Name</td>
            <td>Class Mark (40)</td>
            <td>Exam Score (60)</td>
            <td>Total Score</td>
            <td>Grade</td>
        </thead>
        <tbody>
            <tr class="p-lg">
                <td>0167423589</td>
                <td>Thierry Henry</td>
                <td contenteditable="true" class="white class_score" data-max="40">0</td>
                <td contenteditable="true" class="white exam_score" data-max="60">0</td>
                <td class="total_score">0</td>
                <td class="grade">F9</td>
            </tr>
            <tr class="p-lg">
                <td>0169713236</td>
                <td>Adaklu Mamaga</td>
                <td contenteditable="true" class="white class_score" data-max="40">0</td>
                <td contenteditable="true" class="white exam_score" data-max="60">0</td>
                <td class="total_score">0</td>
                <td class="grade">F9</td>
            </tr>
            <tr class="p-lg">
                <td>0314758965</td>
                <td>Martial Anthony</td>
                <td contenteditable="true" class="white class_score" data-max="40">0</td>
                <td contenteditable="true" class="white exam_score" data-max="60">0</td>
                <td class="total_score">0</td>
                <td class="grade">F9</td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="6" class="btn p-lg">
                    <button class="green">Submit Results</button>
                    <button class="teal">Download Student List</button>
                    <button class="reset_table red">Reset Table</button>
                </td>
            </tr>
        </tfoot>
    </table>
</section>

<script src="<?= "$url/assets/scripts/functions.min.js" ?>"></script>
<script>
    $(".class_score, .exam_score").blur(function(){
        const tr = $(this).parents("tr")
        const class_score = $(tr).find(".class_score")
        const exam_mark = $(tr).find(".exam_score")
        const total_mark = $(tr).find(".total_score")
        const grade = $(tr).find(".grade")
        const result_type = $("input#result_type").val()
        
        if($(this).html() === ""){
            $(this).html("0")
        }else if(parseInt($(this).html()) < 0){
            alert_box("Lower than 0 value rejected", "red")
            $(this).html("0")
        }else if(parseInt($(this).html()) > parseInt($(this).attr("data-max"))){
            alert_box("Value greater than " + $(this).attr("data-max") + " rejected", "red")
            $(this).html("0")
        }

        const total_score = parseFloat($(class_score).html()) + parseFloat($(exam_mark).html())
        //grab total mark
        $(total_mark).html(total_score)
        $(grade).html(giveGrade(total_score), result_type)
    })
    $(".class_score, .exam_score").keydown(function(event){
        if(event.key === "Enter"){
            event.preventDefault()
            $(this).blur()
        }
    })

    $(".reset_table").click(function(){
        $("#result_slip").find(".class_score, .exam_score").html("0")
        $("#result_slip").find(".class_score").blur()
    })

    $("#search").keyup(function(){
        const searchText = $(this).val().toLowerCase()
        $("table#result_slip tbody tr").each(function(){
            const rowData = $(this).text().toLowerCase()
            if(rowData.indexOf(searchText) === -1){
                $(this).hide()
            }else{
                $(this).show()
            }
        })
    })
</script>