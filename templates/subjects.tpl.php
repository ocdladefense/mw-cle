<?php


    /** template subjects.tpl.php 
     * 
     * Rendering the subject and secondary_subject for all cases in a day's worth of reviews. 
    */
?>

<div class="car-subjects">

    <?php foreach($cars as $car): ?>
        <p class="car-subject">
            <?php print strtoupper($car["subject"]); ?> - <?php print $car["secondary_subject"]; ?>
        </p>
    <?php endforeach; ?>

</div>