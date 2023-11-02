<?php 
    $results = [
        "wassce" => [
            ["range"=>"80 - 100", "grade"=>"A1"],
            ["range"=>"70 - 79", "grade"=>"B2"],
            ["range"=>"65 - 69", "grade"=>"C3"],
            ["range"=>"60 - 64", "grade"=>"C4"],
            ["range"=>"55 - 59", "grade"=>"C5"],
            ["range"=>"50 - 54", "grade"=>"C6"],
            ["range"=>"45 - 49", "grade"=>"D7"],
            ["range"=>"40 - 44", "grade"=>"E8"],
            ["range"=>"0 - 39", "grade"=>"F9"]
        ],
        "ctvet" => [
            ["range"=>"80 - 100", "grade"=>"D"],
            ["range"=>"60 - 79", "grade"=>"C"],
            ["range"=>"40 - 59", "grade"=>"P"],
            ["range"=>"0 - 39", "grade"=>"F"]
        ]
    ];
?>

<?php foreach($results as $type => $result) : ?>
<section class="section_container both grade_table" id="<?= strtolower($type) ?>">
    <h3 class="txt-al-c color-dark sm-med-t"><?= strtoupper($type) ?> Grade Points</h3>
    <table class="color-dark">
        <thead>
            <td>Grade Range</td>
            <td>Grade</td>
        </thead>

        <tbody>
            <?php foreach($result as $grade): ?>
            <tr>
                <td><?= $grade["range"] ?></td>
                <td><?= $grade["grade"] ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>
<?php endforeach; ?>