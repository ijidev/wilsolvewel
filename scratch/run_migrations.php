<?php
$c = new mysqli('localhost', 'root', '', 'wilsolvewel_db');
if ($c->connect_error) die("Connection failed: " . $c->connect_error);

$file = $argv[1] ?? 'c:/xampp/htdocs/wilsovewel.com/scratch/migrations_v3.sql';
if (!file_exists($file)) die("File not found: $file\n");

$q = file_get_contents($file);
if ($c->multi_query($q)) {
    do {
        if ($res = $c->store_result()) $res->free();
    } while ($c->more_results() && $c->next_result());
    echo "SUCCESS: $file applied\n";
} else {
    echo "ERR: " . $c->error . "\n";
}
$c->close();
?>
