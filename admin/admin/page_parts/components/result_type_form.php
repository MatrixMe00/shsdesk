<form action="<?= "$url/admin/admin/submit.php" ?>" method="GET" class="w-full wmax-md sm-med color-dark" method="post" name="recordsForm">
    <div class="body">
        <div class="message_box no_disp">
            <span class="message"></span>
            <div class="close"><span>&cross;</span></div>
        </div>
        <?php 
            $school_result = fetchData("school_result","admissiondetails","schoolID=$user_school_id")["school_result"] ?? "";
            $result_types = [
                0 => [
                    "value"=> "",
                    "title"=> "Select a Result Type"
                ],
                1 => [
                    "value"=> "wassce",
                    "title"=> "WASSCE Only"
                ],
                2 => [
                    "value"=> "ctvet",
                    "title"=> "CTVET Only"
                ]
            ]
        ?>
        <label for="school_result" class="flex-column gap-sm">
            <span class="label_title">Provide the type of result the school uses</span>
            <select name="school_result" id="school_result">
                <?php foreach($result_types as $type): ?>
                <option value="<?= $type["value"] ?>" <?= $school_result === $type["value"] ? "selected" : "" ?>><?= $type["title"] ?></option>
                <?php endforeach; ?>
            </select>    
        </label>
        <input type="hidden" name="school_id" value="<?= $user_school_id ?>">
        <label for="submit" class="btn sp-unset sm-auto w-full w-full-child wmax-xs p-lg">
            <button type="submit" name="submit" id="submit" value="update_result_type" class="teal xs-rnd">Update</button>
        </label>
    </div>
</form>