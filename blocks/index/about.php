<section id="about">
    <?php $course = fetchData("COUNT(DISTINCT programme) as total","cssps")["total"];
        if($course > 5) :
    ?>
    <div class="control">
        <div class="head">
            <img src="<?= $url?>/assets/images/icons/course.svg" alt="courses">
        </div>
        <div class="desc">
            <div class="figure">
            <span><?= $course ?></span>
            </div>
            <div class="text">
                <span>Courses</span>
            </div>
        </div>
    </div>
    <?php endif;
        $cssps = fetchData("COUNT(indexNumber) as total","cssps","enroled=TRUE")["total"];
        
        if($cssps >= 30):
    ?>
    <div class="control">
        <div class="head">
            <img src="<?= $url?>/assets/images/icons/student.svg" alt="student">
        </div>
        <div class="desc">
            <div class="figure">
                <span><?= numberShortner($cssps)."<b>+</b>" ?></span>
            </div>
            <div class="text">
                <span>Students Admitted</span>
            </div>
        </div>
    </div>
    <?php endif;
        $system = fetchData("COUNT(indexNumber) as total","cssps")["total"];
        
        if($system >= 50){
    ?>
    <div class="control">
        <div class="head">
            <img src="<?= $url?>/assets/images/icons/teacher.svg" alt="teacher">
        </div>
        <div class="desc">
            <div class="figure">
            <span><?= numberShortner($system)."<b>+</b>" ?></span>
            </div>
            <div class="text">
                <span>Students Placed</span>
            </div>
        </div>
    </div>
    <?php } ?>
    <div class="control">
        <div class="head">
            <img src="<?= $url?>/assets/images/icons/region.svg" alt="region">
        </div>
        <div class="desc">
            <div class="figure">
            <span><?= fetchData("COUNT(id) as total","schools")["total"] ?></span>
            </div>
            <div class="text">
                <span>Registered Schools</span>
            </div>
        </div>
    </div>
    <div class="shadow"></div>
</section>