<?php 
    $settings = [
        "program_swap" => [
            "value" => $school_settings["program_swap"] ?? false,
            "description" => "Enabling this means that your classes have been named differently. Eg. Bus 1 A, Bus 2 A, Bus 3 A. Disabling it means the system default naming is used Eg. Bus A, Bus B [The system will automatically assign the classes to the students so we can have Bus A Form 1, Bus A Form 2, Bus A Form 3].
            <br />Disabling this feature means you have named your classes in the system default approach. This is to help the system better decide the classes of the students in case you use the promote feature",
            "name" => "Class Naming Method",
            "options" => "boolean",
            "need_refresh" => 1
        ]
    ]
?>
<table class="full" style="color: initial !important">
    <tr>
        <td>Setting</td>
        <td>Description</td>
        <td>Status/Value</td>
    </tr>
    <?php foreach ($settings as $name => $setting): ?>
        <tr>
            <td><?php echo htmlspecialchars($setting["name"]); ?></td>
            <td><?= $setting["description"] ?></td>
            <td>
                <label for="<?= $name ?>">
                    <select name="<?= $name ?>" id="<?= $name ?>" class="system_setting" data-need-refresh="<?= $setting["need_refresh"] ?>">
                        <?php if($setting["options"] == "boolean"): ?>
                            <option value="0" <?= $setting["value"] == false ? "selected" : "" ?>>Disabled</option>
                            <option value="1" <?= $setting["value"] == true ? "selected" : "" ?>>Enabled</option>
                        <?php elseif(is_array($setting["options"])): foreach($setting["options"] as $value => $name_): ?>
                            <option value="" <?= $setting["value"] == $value ? "selected" : "" ?>><?= $name_ ?></option>
                        <?php endforeach; endif; ?>
                    </select>
                </label>
            </td>
        </tr>
    <?php endforeach; ?>
</table>