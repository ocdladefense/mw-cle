<?php
    // Represents a single day/court summary for listing on LOD homepage.
    $showMeta = false;
?>


<?php foreach($chapters as $chapter): ?>

    <div><?php print $chapter["Name"]; ?></div>
<div><?php print $chapter["Title__c"]; ?></div>

<?php endforeach; ?>