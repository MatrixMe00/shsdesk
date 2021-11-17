<section class="section_container">
    <div class="content" style="background-color: #007bff;">
        <div class="head">
            <h2>44</h2>
        </div>
        <div class="body">
            <span>Students Registered</span>
        </div>
    </div>

    <div class="content" style="background-color: #20c997">
        <div class="head">
            <h2>22</h2>
        </div>
        <div class="body">
            <span>Boys Allocated Houses</span>
        </div>
    </div>

    <div class="content" style="background-color: #ffc107">
        <div class="head">
            <h2>13</h2>
        </div>
        <div class="body">
            <span>Girls Allocated Houses</span>
        </div>
    </div>

    <div class="content" style="background-color: #dc3545">
        <div class="head">
            <h2><?php echo 44-13-22?></h2>
        </div>
        <div class="body">
            <span>Day Students</span>
        </div>
    </div>
</section>

<section class="flex flex-wrap flex-center-align">
    <div class="btn">
        <button onclick="$('#modal_3').removeClass('no_disp')">Add New House</button>
    </div>
    <div class="btn">
        <button>Generate Report</button>
    </div>
</section>

<section>
    <div class="head">
        <h2>List of Houses in your school</h2>
    </div>
    <div class="body">
        <table>
            <thead>
                <tr>
                    <td>No.</td>
                    <td>House Name</td>
                    <td>Gender</td>
                    <td>Rooms</td>
                    <td>Heads Per Room</td>
                    <td>Occupancy</td>
                    <td>Status</td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>Acolatse</td>
                    <td>Females</td>
                    <td>15</td>
                    <td>30</td>
                    <td>18</td>
                    <td>Not Full</td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>Vodzi</td>
                    <td>Males</td>
                    <td>15</td>
                    <td>30</td>
                    <td>25</td>
                    <td>Not Full</td>
                </tr>
            </tbody>
        </table>
    </div>
</section>