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
                    $sql = "SELECT id, schoolName FROM schools WHERE Active=1";
                    $res = $connect->query($sql);
                    if($res->num_rows > 0){
                        while($row = $res->fetch_assoc()){
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