<?php
function read_data() {
    $filename = "calendar.txt";
    $f = fopen($filename, "r") or die("Unable to open file!");
    $s = fread($f, 10000000);
    if ($s == "") {
        $s = "[]";
    }
    fclose($f);
    return json_decode($s, true);
}

function read_data_str() {
    $filename = "calendar.txt";
    $f = fopen($filename, "r") or die("Unable to open file!");
    $s = fread($f, 10000000);
    if ($s == "") {
        $s = "[]";
    }
    fclose($f);
    return $s;
}

function write_data($data) {
    $d = read_data();
    $d[] = $data;
    $filename = "calendar.txt";
    $f = fopen($filename, "w") or die("Unable to open file!");
    fwrite($f, json_encode($d));
    fclose($f);
}

function clear() {
    $filename = "calendar.txt";
    $f = fopen($filename, "w") or die("Unable to open file!");
    fwrite($f, "");
    fclose($f);
}
?>
