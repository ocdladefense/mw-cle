<?php
    // Represents a single day/court summary for listing on LOD homepage.
    $showMeta = false;
    $subject = ucwords($subject);

?>

    <div class="car-entry">

        <br />

        <a href='<?php print "$appDomain/car/list/" . $id; ?>'>
            <?php print $titleDate . " &bull; $title" ?>
        </a>

        <span>
            <?php print " &bull; " . substr($summary, 0, 180) . "..."; ?>
        </span>
    </div>