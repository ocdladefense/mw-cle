<?php
    // Represents a single day/court summary for listing on LOD homepage.
    $showMeta = false;
?>


<div class="car-entry">

    <h2>
        <a href='<?php print "$appDomain/car/list?year=$year&month=$month&day=$day&court=$court"; ?>'>
            <?php print $title; ?>
        </a>
    </h2>
    <div class="car-summary-header">
        <p>
            <?php if($author): ?>
                by: <?php print $author; ?>
            <?php endif; ?>
        </p>
    </div>

    <?php print $subjectsHTML; ?>
    
    <p>
        <a href='<?php print "$appDomain/car/list?year=$year&month=$month&day=$day&court=$court"; ?>'>
            &rarr; read the full summaries...
        </a>
    </p>

</div>