<?php 
    $classes = decimalIndexArray(fetchData1("program_id, program_name", "program", "school_id=$user_school_id", limit: 0));
    if($classes == "empty"){
        echo "<p class='txt-al-c'>You have no classes created</p>";
        return;
    }

    $promotion_classes = promotion_classes();

    function select_field($name, $program_id = ""){
        global $classes;
        $select = array("<option value=\"\">Select a Class</option>");

        foreach($classes as $class){
            $select[] = "<option value=\"{$class['program_id']}\" ".($class["program_id"] == $program_id ? "selected" : "").">{$class['program_name']}</option>";
        }

        return "<label for=''>\n\t<select name=\"$name\">\n". implode("\t\n", $select). "\t</select>\n</label>";
    }
?>

<div class="btn sm-lg-b">
    <button id="add_program_promote" class="">Add Row</button>
    <button class="<?= $promotion_classes ? "" : "no_disp" ?>" id="fix_anomalies">Fix Anomalies</button>
</div>

<table class="full" style="color: black;">
    <thead>
        <td>Year 1</td>
        <td>Year 2</td>
        <td>Year 3</td>
    </thead>
    <tbody id="promotion_classes">
        <?php if($promotion_classes): foreach($promotion_classes as $promotion_class): ?>
            <tr data-id="<?= $promotion_class["id"] ?>">
                <td class="year1"><?= select_field("year1", $promotion_class["year1"]) ?></td>
                <td class="year2"><?= select_field("year2", $promotion_class["year2"]) ?></td>
                <td class="year3"><?= select_field("year3", $promotion_class["year3"]) ?></td>
                <td>
                    <span class="item-event promotion-save">Save</span>
                    <span class="item-event promotion-remove">Remove</span>
                </td>
            </tr>
        <?php endforeach; endif; ?>
    </tbody>
</table>

<template id="program_promote_template">
    <tr data-id="0">
        <td class="year1"><?= select_field("year1") ?></td>
        <td class="year2"><?= select_field("year2") ?></td>
        <td class="year3"><?= select_field("year3") ?></td>
        <td>
            <span class="item-event promotion-save">Save</span>
            <span class="item-event promotion-remove">Remove</span>
        </td>
    </tr>
</template>