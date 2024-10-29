<section id="contacts" class="secondary">
    <div class="head">
        <h3>For Assistance</h3>
    </div>
    <div class="body">
        <p>Please select your choice school to get the contact of your admin</p>
        <label for="getContact">
            <select name="getContact" id="getContact">
                <option value="">Select A School</option>
                <?php 
                    if($schools > 0){
                        foreach($schools as $row){
                            echo "
                            <option value=\"".$row['id']."\">".$row["schoolName"]."</option>
                            ";
                        }
                    }
                ?>
            </select>
        </label>                    
        <span id="contResult"></span>
    </div>
</section>