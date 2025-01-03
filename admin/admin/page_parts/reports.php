<?php
    include_once($_SERVER["DOCUMENT_ROOT"]."/includes/session.php");

    //set nav_point session
    $_SESSION["nav_point"] = "reports";

    // academic years
    $academic_years = decimalIndexArray(fetchData1("DISTINCT academic_year", "results", ["school_id = $user_school_id", "accept_status = TRUE"], 0, "AND"));

    // programs
    $programs = decimalIndexArray(fetchData1(
        ["program_id", "program_name", "short_form", "course_ids"], "program", "school_id = $user_school_id", 0
    ));

    // display an error message where the need be or get the subjects if all is well
    if(!$programs){
        $error_message = "No classes have been created in the system. No results can be displayed";
    }elseif(!$academic_years){
        $error_message = "No results have been uploaded or approved yet.";
    }else{
        $subjects = decimalIndexArray(fetchData1(["course_id", "course_name", "short_form"], "courses", "school_id = $user_school_id", 0));
    }
?>

<section class="sp-xlg-tp">
    <?php if(isset($error_message)): ?>
        <p class="sp-med txt-al-c"><?= $error_message ?></p>
    <?php else: ?>
        <p class="sp-med txt-al-c">Use this page to retrieve results of a class for a specified academic year</p>

        <div class="btn w-fit sm-auto p-med wmax-xs gap-med txt-al-c">
            <button class="primary search_menu_btn" data-searchable="0" data-submit-value="class">Class Results</button>
            <button class="plain-r primary search_menu_btn" data-searchable="1" data-submit-value="subject">Subject Results</button>
        </div>

        <form class="mx-auto sm-xlg-t" name="search_result_form" class="search_form" id="class_search_form" action="admin/submit.php" method="GET">
            <div class="body">
                <div class="joint">
                    <label for="search_academic_year">
                        <select name="academic_year" id="search_academic_year">
                            <option value="">Select an Academic year</option>
                            <?php foreach($academic_years as $academic_year): ?>
                                <option value="<?= $academic_year["academic_year"] ?>"><?= $academic_year["academic_year"] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                    <label for="search_program_id">
                        <select name="program_id" id="search_program_id" data-searchable="1">
                            <option value="">Select a Class</option>
                            <?php foreach($programs as $program): ?>
                                <option value="<?= $program["program_id"] ?>" data-subjects = "<?= $program["course_ids"] ?>"><?= $program["program_name"] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                    <label for="search_course_id" class="no_disp">
                        <select name="course_id" id="search_course_id">
                            <option value="">Select a Subject</option>
                            <?php foreach($subjects as $subject): ?>
                                <option value="<?= $subject["course_id"] ?>" class="searchable no_disp" data-subject-id="<?= $subject["course_id"] ?>"><?= $subject["short_form"] ?? $subject["course_name"] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                    <label for="search_year_level">
                        <select name="year_level" id="search_year_level">
                            <option value="">Select Form Level</option>
                            <?php foreach(range(1,3) as $year): ?>
                                <option value="<?= $year ?>"><?= "Form $year" ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                    <label for="search_semester">
                        <select name="semester" id="search_semester">
                            <option value="">Select a Semester</option>
                            <?php foreach(range(1,2) as $semester): ?>
                                <option value="<?= $semester ?>"><?= $semester ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                </div>
                <div class="btn p-lg sm-auto wmax-xs w-full flex flex-wrap flex-eq gap-sm">
                    <button type="submit" name="submit" value="search_class_result" id="submit_form_button" class="primary">Search</button>
                    <button type="reset" class="secondary" id="reset_form">Clear</button>
                </div>
            </div>
        </form>
    <?php endif; ?>
</section>

<?php if(!isset($error_message)): ?>
<section class="sp-xlg-tp no_disp" id="content-area">
    <div class="txt-al-c status-area">
        <p id="status-tag">Loading...</p>
    </div>

    <div class="content-space no_disp">
        <table class="full">
            <thead class="txt-al-c transform-v p-med"></thead>
            <tbody class="txt-al-c"></tbody>
            <tfoot class="no_disp"></tfoot>
        </table>
        <div class="btn p-med rounded">
            <button class="violet" id="print_btn">Print</button>
        </div>
    </div>
</section>

<script>
    $(document).ready(function(){
        $("#search_program_id").change(function(){
            if($(this).attr("data-searchable") == "1"){
                const value = $(this).val();
                const subjects = value != "" ? $(this).find("option:selected").attr("data-subjects").split(" ") : "";

                if(subjects != ""){
                    // remove the trailing empty string
                    subjects.pop()

                    // display necessary subjects
                    $("#search_course_id option.searchable").each((index, element) => {
                        element = $(element);
                        const subject_id = element.attr("data-subject-id");
                        
                        // hide or show element if it exists
                        subjects.includes(subject_id) ? element.removeClass("no_disp") : element.addClass("no_disp");
                    })
                }else{
                    $("#search_course_id option.searchable").addClass("no_disp");
                }
            }
        });

        $("#reset_form").click(function(){
            $("#search_course_id option.searchable").addClass("no_disp");

            const content_area = $("#content-area");
            const status_area = content_area.find(".status_area");

            content_area.addClass("no_disp");
            status_area.addClass("no_disp").find("p").html("");
        })

        $(".search_menu_btn").click(function(){
            const searchable = $(this).attr("data-searchable");
            $(".search_menu_btn:not(.plain-r)").addClass("plain-r");
            $(this).removeClass("plain-r");

            $("#search_program_id").attr("data-searchable", searchable);
            $("form button[name=submit]").val("search_" + $(this).attr("data-submit-value") + "_result")

            if(searchable == "1"){
                $("label[for=search_course_id]").removeClass("no_disp");
                $("#search_course_id").prop("disabled", false);
            }else{
                $("label[for=search_course_id]").addClass("no_disp");
                $("#search_course_id").prop("disabled", true);
            }

            // reset form
            $("#reset_form").click();
        })

        function create_thead(thead_element, thead_data){
            thead_element.html("<th>INDEX NUMBER</th>\n");
            thead_data.forEach(element => {
                let data = "<th>" + (element.replace("_", " ").toUpperCase()) + "</th>\n";
                $(thead_element).append(data);
            });
            thead_element.append("<th>POSITION</th>\n");
        }

        $("form").submit(function(e){
            e.preventDefault();
            const submit_btn = $(this).find("button[name=submit]");
            const class_form = submit_btn.val() == "search_class_result";
            const content_area = $("#content-area");
            const status_area = content_area.find(".status-area");
            const result_area = content_area.find(".content-space");
            const tfoot = content_area.find("tfoot");

            // show content area
            status_area.html("Fetching results...");
            content_area.removeClass("no_disp");
            tfoot.addClass("no_disp").html("");

            const response = jsonFormSubmit($(this), submit_btn, false);

            response.then((resp) => {
                if(resp.status){
                    status_area.addClass("no_disp");

                    const thead = resp.data.thead;
                    const data = Object.entries(resp.data.data).map(([index_number, report]) => ({
                        index_number,
                        ...report
                        }));     

                    // Sort the array by 'total' in descending order
                    data.sort((a, b) => b.total - a.total);

                    create_thead(content_area.find("thead"), thead);

                    // insert data into body
                    const tbody = content_area.find("tbody");
                    tbody.html("");
                    let last_mark = -1;
                    let last_position = 0;
                    let count = 1;

                    $.each(data, (index, record) => {                            
                        let tr = "<tr>\n<td>" + record.index_number + "</td>\n";

                        thead.forEach((element) => {
                            let value = record[element] != undefined || record[element] != null ? record[element] : "N/A";
                            value = value != "N/A" ? parseFloat(value).toFixed(1) : value;
                            tr += "<td>" + value + "</td>\n";
                        })

                        // insert the position
                        last_position = last_mark == record.total ? last_position : count;
                        last_mark = record.total;

                        tr += "<td>" + positionFormat(last_position) + "</td>\n";

                        // keep counting the number of students to determine the next position
                        ++count;

                        // append changes
                        tbody.append(tr);
                    })

                    const cols = thead.length + 1;
                    const status_section = "<tr>\n" +
                                           "    <td>Total Records</td>\n" +
                                           "    <td colspan=\"" + cols + "\">" + Object.keys(data).length + "\n" +
                                           "</tr>\n";
                    tfoot.html(status_section);

                    content_area.find("thead").toggleClass("transform-v", class_form);

                    if(!class_form){
                        const new_tr =  "<tr>\n" +
                                        "    <td>Entry By</td>\n" +
                                        "    <td colspan=\"" + cols + "\">" + resp.data.teacher_name + "\n" +
                                        "</tr>\n";
                        tfoot.append(new_tr);
                    }

                    // show the footer
                    tfoot.removeClass("no_disp");

                    result_area.removeClass("no_disp");
                }else{
                    status_area.html(resp.data);
                }
            })
        })

        $("#print_btn").click(function(){
            var printContents = $('#content-area .content-space').html();
            var originalContents = $('body').html();

            const academic_year = $("select#search_academic_year").val();
            const program_name = $("select#search_program_id option:selected").text();
            const subject_name = $("select#search_course_id option:selected").text();
            const year = $("select#search_year_level").val();
            const semester = $("select#search_semester").val();

            let page_title = "";
            let course_style = "";

            if(subject_name.toLocaleLowerCase() == "select a subject"){
                page_title = academic_year;
                course_style = "th{font-size: 10pt}";
            }else{
                page_title = academic_year + " " + subject_name;
            }

            page_title += " Records for " + program_name + ", Year " + year + " Semester " + semester;

            let page_style = "<style>" + 
                               "    table td{border: thin solid lightgrey}" +
                               "    " + course_style + ""
                               "</style>";

            // Create a new window or tab
            var printWindow = window.open('', '', 'height=600,width=800');
            printWindow.document.write('<html><head><title>SHSDesk Records</title>');
            printWindow.document.write("<style>table{border-collapse: collapse; font-size: 12pt} button{display: none;} th,td{border: thin solid lightgrey; padding: 0.25em}" + course_style + "tbody td:not(:first-child){text-align: center}</style>");
            printWindow.document.write('</head><body >');
            printWindow.document.write('<h4 style="text-align: center">' + page_title + '</h4>');
            printWindow.document.write(printContents);
            printWindow.document.write('</body></html>');

            // Wait for the new document to be fully loaded before printing
            printWindow.document.close();
            printWindow.focus();
            printWindow.print();
            printWindow.close();
        })
    })
</script>
<?php endif; close_connections() ?>