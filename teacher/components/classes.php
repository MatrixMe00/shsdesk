<?php include_once("compSession.php"); $_SESSION["active-page"] = "classes" ?>
<h1 class="txt-al-c sm-lg-t no_d" id="page_title">My Classes</h1>
<section id="classes" class="d-section flex flex-center-content flex-wrap gap-md">
    <div class="card v-card sm-rnd m-med-tp sp-lg">
        <div class="self-align-start flex gap-sm flex-column">
            <h2>Class Name</h2>
            <h4>Subject Name</h4>
            <p class="txt-fs">[Academic Year | Semester Number]</p>
        </div>
        <div class="foot btn self-align-end">
            <button class="light plain-r sp-xlg" onclick="pageChange()">View Data</button>
        </div>
    </div>
    <div class="card v-card sm-rnd m-med-tp sp-lg">
        <div class="self-align-start flex gap-sm flex-column">
            <h2>Class Name</h2>
            <h4>Subject Name</h4>
            <p class="txt-fs">[Academic Year | Semester Number]</p>
        </div>
        <div class="foot btn self-align-end">
            <button class="light plain-r sp-xlg" onclick="pageChange()">View Data</button>
        </div>
    </div>
    <div class="card v-card sm-rnd m-med-tp sp-lg">
        <div class="self-align-start flex gap-sm flex-column">
            <h2>Class Name</h2>
            <h4>Subject Name</h4>
            <p class="txt-fs">[Academic Year | Semester Number]</p>
        </div>
        <div class="foot btn self-align-end">
            <button class="light plain-r sp-xlg" onclick="pageChange()">View Data</button>
        </div>
    </div>
    <div class="card v-card sm-rnd m-med-tp sp-lg">
        <div class="self-align-start flex gap-sm flex-column">
            <h2>Class Name</h2>
            <h4>Subject Name</h4>
            <p class="txt-fs">[Academic Year | Semester Number]</p>
        </div>
        <div class="foot btn self-align-end">
            <button class="light plain-r sp-xlg" onclick="pageChange()">View Data</button>
        </div>
    </div>
    <div class="card v-card sm-rnd m-med-tp sp-lg">
        <div class="self-align-start flex gap-sm flex-column">
            <h2>Class Name</h2>
            <h4>Subject Name</h4>
            <p class="txt-fs">[Academic Year | Semester Number]</p>
        </div>
        <div class="foot btn self-align-end">
            <button class="light plain-r sp-xlg" onclick="pageChange()">View Data</button>
        </div>
    </div>
</section>

<section id="single_class" class="d-section lt-shade no_disp">
    <div class="head flex flex-space-content sp-med-lr">
        <div class="back" onclick="pageChange()">Back</div>
        <div class="title">Class Name Records</div>
    </div>
    <div class="form">
        <div class="btn sp-unset">
            <button name="class_list" class="teal sp-xlg">Download Class List</button>
        </div>
    </div>
    <div class="body sm-xlg-t">
        <table class="full">
            <thead>
                <td>Index Number</td>
                <td>Student Name</td>
                <td>Marks</td>
                <td>Grade</td>
            </thead>
            <tbody>
                <tr>
                    <td>123456783</td>
                    <td>Frimpong Akosua</td>
                    <td>78</td>
                    <td>B</td>
                    <td>
                        <span class="item-event view" onclick="viewData($(this))">View</span>
                    </td>
                </tr>
                <tr>
                    <td>123456719</td>
                    <td>Sam David</td>
                    <td>80</td>
                    <td>A</td>
                    <td>
                        <span class="item-event view" onclick="viewData($(this))">View</span>
                    </td>
                </tr>
                <tr>
                    <td>123456729</td>
                    <td>Alornyo Jesse</td>
                    <td>68</td>
                    <td>C</td>
                    <td>
                        <span class="item-event view" onclick="viewData($(this))">View</span>
                    </td>
                </tr>
                <tr class="empty">
                    <td colspan="4">No student data to be seen here. Please have a record approved to continue</td>
                </tr>
            </tbody>
        </table>
    </div>
</section>

<section id="single_student" class="fixed flex flex-center-content flex-center-align form_modal_box no_disp">
    <div class="wmax-md sp-lg light w-full window txt-fl">
        <div class="head flex flex-space-content light sp-med">
            <div class="title">
                <span class="txt-bold">Student Data</span>
            </div>
            <div class="controls border sp-sm-lr" onclick="$('#single_student').addClass('no_disp')">
                <div class="mini-o" title="Close" >
                    <span></span>
                </div>
            </div>
        </div>
        <div class="body white sp-lg flex flex-column gap-md">
            <div class="flex flex-space-content">
                <span class="txt-bold">Name</span>
                <span class="name"></span>
            </div>
            <div class="flex flex-space-content">
                <span class="txt-bold">Index Number</span>
                <span class="index"></span>
            </div>
            <div class="flex flex-space-content">
                <span class="txt-bold">Mark</span>
                <span class="mark"></span>
            </div>
            <div class="flex flex-space-content">
                <span class="txt-bold">Grade</span>
                <span class="grade"></span>
            </div>
        </div>
    </div>
</section>

<script>
    function viewData(element){
        const tr = $(element).parents("tr")
        const index = $(tr).children("td:nth-child(1)").html()
        const name = $(tr).children("td:nth-child(2)").html()
        const mark = $(tr).children("td:nth-child(3)").html()
        const grade = $(tr).children("td:nth-child(4)").html()

        //fill single student
        $("#single_student .name").html(name)
        $("#single_student .index").html(index)
        $("#single_student .mark").html(mark)
        $("#single_student .grade").html(grade)

        $('#single_student').removeClass('no_disp')
    }

    function pageChange(){
        $("#page_title, #classes, #single_class").toggleClass("no_disp")
        $("#classes").toggleClass("flex")
    }
</script>