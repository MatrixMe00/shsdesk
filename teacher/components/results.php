<?php include_once("compSession.php"); $_SESSION["active-page"] = "results" ?>
<input type="hidden" name="result_type" id="result_type" value="wassce">
<section class="d-section lt-shade">
    <form action="" class="flex w-full flex-column gap-sm">
        <div class="joint gap-sm">
            <label for="class">
                <select class="w-full sp-xlg" name="class" id="class">
                    <option value="">Select Class</option>
                    <option value="">Classname 1</option>
                    <option value="">Classname 2</option>
                    <option value="">Classname 3</option>
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
                    <option value="">Select Year</option>
                    <option value="">Term 1</option>
                    <option value="">Term 2</option>
                    <option value="">Term 3</option>
                </select>
            </label>
        </div>
        <div class="btn wmax-xs w-full sm-auto">
            <button class="primary sm-rnd sp-xlg w-full" name="submit" value="search_class">Submit</button>
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
                <td>0123456789</td>
                <td>Lastname Othernames</td>
                <td contenteditable="true" class="white class_score" data-max="40">0</td>
                <td contenteditable="true" class="white exam_score" data-max="60">0</td>
                <td class="total_score">0</td>
                <td class="grade">F9</td>
            </tr>
            <tr class="p-lg">
                <td>0123456789</td>
                <td>Lastname Othernames</td>
                <td contenteditable="true" class="white class_score" data-max="40">0</td>
                <td contenteditable="true" class="white exam_score" data-max="60">0</td>
                <td class="total_score">0</td>
                <td class="grade">F9</td>
            </tr>
            <tr class="p-lg">
                <td>0123456789</td>
                <td>Lastname Othernames</td>
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
</script>