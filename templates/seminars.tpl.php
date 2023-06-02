<?php
    // Represents a single day/court summary for listing on LOD homepage.
    $showMeta = false;
?>


<ul>
    <?php foreach($seminars as $seminar): ?>
        <li><a href="/CLE/<?= $seminar["Id"]; ?>"><?= $seminar["Name"] ?> - <?= $seminar["date"] ?></a></li>
    <?php endforeach; ?>
</ul>