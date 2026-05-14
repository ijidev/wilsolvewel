<?php
$c = file_get_contents('old_projects.php');
// UTF-16LE to UTF-8 if necessary
if (substr($c, 0, 2) === "\xff\xfe") {
    $c = mb_convert_encoding($c, 'UTF-8', 'UTF-16LE');
}
if (preg_match('/(<!-- Milestone Modal -->.*)<\/body>/is', $c, $m)) {
    file_put_contents('modals.html', $m[1]);
    echo "Extracted modals";
} else {
    echo "Could not find modals";
}
