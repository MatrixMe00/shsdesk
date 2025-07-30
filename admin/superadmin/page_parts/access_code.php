<?php include_once("session.php");

//set nav_point
$_SESSION["nav_point"] = "create_access";

//fetch active schools
$schools = decimalIndexArray(fetchData(...[
    "columns" => ["id", "schoolName"],
    "table" => "schools",
    "where" => "Active=1", "limit" => 0
]));

?>

<section class="p-section">
    <div style="background-color: #f0f8ff; border-left: 4px solid #1e90ff; padding: 10px; margin-bottom: 20px;">
        This page allows you to create access codes for specific students in selected schools. 
        Start by selecting a school. Only schools with active status and enrolled students will be eligible.
        Valid students who have active tokens will not show in the search list.
    </div>

    <div class="joint flex-column gap-sm sm-auto wmax-sm">
        <label for="school_select" class="flex-column gap-sm">
            <span class="label_title">Select School</span>
            <select id="school_select" class="text_input">
                <option value="">-- Choose a School --</option>
                <?php foreach ($schools as $school): ?>
                    <option value="<?= $school["id"] ?>"><?= $school["schoolName"] ?></option>
                <?php endforeach; ?>
            </select>
        </label>
    </div>
</section>

<!-- Hidden by default until a school is selected -->
<section id="school_section" class="p-section no_disp">
    <div id="no_students_alert" class="empty p-med txt-al-c no_disp">
        <p>No students found for this school. Please import student records first.</p>
    </div>

    <div id="student_search_section" class="no_disp">
        <label for="student_search" class="flex-column gap-sm wmax-sm sm-auto">
            <span class="label_title">Search Student by Name</span>
            <input type="text" id="student_search" placeholder="Start typing student name..." class="text_input">
        </label>
        <div id="student_match" class="p-sm flex flex-wrap gap-md sm-auto wmax-md" style="max-height: 25vh; overflow: auto"></div>
    </div>

    <div id="selected_students_section" class="p-med-tp no_disp">
        <table class="sm-full">
            <thead>
                <tr>
                    <td>Index Number</td>
                    <td>Full Name</td>
                    <td>Class Name</td>
                    <td>Form Level</td>
                    <td>Action</td>
                </tr>
            </thead>
            <tbody id="student_table_body">
                <!-- Dynamically filled -->
            </tbody>
        </table>
        <div class="btn txt-al-c sp-med-tp">
            <button class="primary" id="proceed_btn">Process Access Code</button>
        </div>
    </div>
</section>

<script>
    let selectedStudents = [];
    let programs = undefined;

    // Delegated: School Select
    $(document).on('change', '#school_select', function () {
        const schoolID = $(this).val();

        // Reset UI sections
        $('#school_section').addClass('no_disp');
        $('#student_search_section').addClass('no_disp');
        $('#no_students_alert').addClass('no_disp');
        $('#selected_students_section').addClass('no_disp');
        $('#student_match').empty();
        $('#student_table_body').empty();
        selectedStudents = [];

        if (schoolID !== "") {
            $.ajax({
                url: "superadmin/submit.php",
                method: "POST",
                dataType: "json",
                data: { school_id: schoolID, submit: "check_school_students" },
                success: function (res) {
                    if (res.has_students === true) {
                        $('#student_search_section').removeClass('no_disp');
                        $('#selected_students_section').removeClass('no_disp');
                        programs = res.programs;
                    } else {
                        $('#no_students_alert').removeClass('no_disp');
                    }
                    $('#school_section').removeClass('no_disp');
                },
                error: function () {
                    alert_box("An error occurred. Try again", "danger");
                }
            });
        }
    });

    // Delegated: Typing in student search input
    $(document).on('input', '#student_search', function () {
        const searchTerm = $(this).val();
        const schoolID = $('#school_select').val();

        if (searchTerm.length < 2 || !schoolID){
            $('#student_match').empty().addClass("no_disp"); 
            return;
        }

        $.ajax({
            url: "superadmin/submit.php",
            method: "POST",
            dataType: "json",
            data: {
                term: searchTerm,
                school_id: schoolID,
                submit: "search_student_by_name"
            },
            success: function (response) {
                if (!response.status) {
                    $('#student_match').html(`${response.message}`);
                    return;
                }

                const students = response.students;
                $('#student_match').empty().removeClass('no_disp');

                if (students.length > 0) {
                    let anyVisible = false;

                    students.forEach(student => {
                        const index = student.indexNumber;
                        const isSelected = selectedStudents.includes(index);
                        const program = programs[parseInt(student.program_id)]['program_name'] || "N/A";
                        const fullname = `${student.Lastname} ${student.Othernames}`;

                        const studentCard = `
                            <button class="btn card light w-fit cursor-p xs-rnd m-sm p-sm student_option student_${index}" data-index="${index}">
                                <h4>${fullname}</h4>
                                <p>${program}, Year ${student.studentYear}</p>
                            </button>`;

                        $('#student_match').append(studentCard);

                        if (isSelected) {
                            $(`.student_${index}`).addClass('no_disp');
                        } else {
                            anyVisible = true;
                        }
                    });

                    if (!anyVisible) {
                        $('#student_match').html('<p class="info">All matching students have already been added.</p>');
                    }
                } else {
                    $('#student_match').html('<p class="info">No students match your search.</p>');
                }
            },
            error: function () {
                alert_box("Error loading students. Try again.", "danger");
            }
        });
    });

    // Delegated: Selecting a student from matches
    $(document).on('click', '.student_option', function () {
        const indexNumber = $(this).data('index');

        if (selectedStudents.includes(indexNumber)) return;

        const studentName = $(this).find('h4').text();
        const details = $(this).find('p').text();
        const [program, year] = details.split(', Year ');

        selectedStudents.push(indexNumber);
        $(`.student_${indexNumber}`).addClass('no_disp');

        $('#student_table_body').append(`
            <tr data-index="${indexNumber}">
                <td>${indexNumber}</td>
                <td>${studentName}</td>
                <td>${program.trim()}</td>
                <td>${year.trim()}</td>
                <td class="btn"><button class="red remove_student">Remove</button></td>
            </tr>
        `);
    });

    // Delegated: Remove a student
    $(document).on('click', '.remove_student', function () {
        const row = $(this).closest('tr');
        const indexNumber = row.data('index');

        row.remove();
        selectedStudents = selectedStudents.filter(i => i !== indexNumber);
        $(`.student_${indexNumber}`).removeClass('no_disp');
    });

    // Delegated: Proceed button
    $(document).on('click', '#proceed_btn', function () {
        if (selectedStudents.length === 0) {
            alert_box("Please select at least one student before proceeding.", "warning");
            return;
        }

        const schoolID = $('#school_select').val();

        $.ajax({
            url: "superadmin/submit.php",
            method: "POST",
            dataType: "json",
            data: {
                submit: "create_access_code",
                students: selectedStudents,
                school_id: schoolID
            },
            success: function (response) {
                if (response.status === true) {
                    alert_box("Access code successfully created for selected students.", "success");

                    // Reset UI
                    selectedStudents = [];
                    $('#student_table_body').empty();
                    $('#student_match').empty().addClass('no_disp');
                    $('#student_search').val('');
                } else {
                    alert_box(response.message || "An unknown error occurred.", "danger");
                }
            },
            error: function () {
                alert_box("An error occurred during submission. Please try again.", "danger");
            }
        });
    });

</script>

<?php close_connections(); ?>
