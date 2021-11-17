<?php include_once("../../../includes/session.php") ?>
<section>
    <form action="" method="post">
        <div class="head">
            <h2>Add A Student</h2>
        </div>
        <div class="body">
            <div class="joint">
                <label for="student_name">
                    <span class="label_image">
                        <img src="<?php echo $url?>/assets/images/icons/home.png" alt="name">
                    </span>
                    <input type="text" name="student_name" id="student_name" placeholder="Name of Student*"
                    required title="The full name of the student should be delivered here" pattern="[a-zA-Z\s]">
                </label>
                <label for="student_house">
                    <span class="label_image">
                        <img src="<?php echo $url?>/assets/images/icons/home.png" alt="house">
                    </span>
                    <select name="student_house" id="student_house">
                        <option value="">Select the student's House*</option>
                    </select>
                </label>
                <label for="exeat_town">
                    <span class="label_image">
                        <img src="<?php echo $url?>/assets/images/icons/home.png" alt="town">
                    </span>
                    <input type="text" name="exeat_town" id="exeat_town" placeholder="Enter the name of the town*" 
                    title="Name of destination town by the student should be entered here" required>
                </label>
                <label for="exeat_date" class="flex-column flex-align-start flex-content-start">
                    <span class="label_title">
                        Date for exeat
                    </span>
                    <input type="date" name="exeat_date" id="exeat_date" title="Date for exeat" required>
                </label>
                <label for="return_date" class="flex-column flex-align-start flex-content-start">
                    <span class="label_title">
                        Date for Returning
                    </span>
                    <input type="date" name="return_date" id="return_date" title="Date for exeat" required>
                </label>
            </div>
        </div>
    </form>
</section>

<section>
    <div class="head">
        <h2>Students on exeat</h2>
    </div>
    <div class="body">
        <table>
            <thead>
                <tr>
                    <td>Name</td>
                    <td>House</td>
                    <td>Town</td>
                    <td>Leave Date</td>
                    <td>Return Date</td>
                    <td>Status</td>  
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>John Doe</td>
                    <td>Adaklu</td>
                    <td>Nsawam</td>
                    <td>29-10-2021</td>
                    <td>01-12-2021</td>
                    <td>Not Returned</td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="foot">
        <div class="btn">
            <button title="Generates a report for the term on exeats made">Generate Report</button>
        </div>
    </div>
</section>