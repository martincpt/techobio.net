<?php
if (!isset($_GET['v'])) die('No ID');

$_dir = str_replace('.m3u8', '', $_GET['v']);

if (!is_dir($_dir)) die('Invalid ID');

require($_SERVER['DOCUMENT_ROOT'].'/wp-load.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/wp-content/themes/videopro-child/video_passwords.php');

$has_access = $video_passwords->has_valid_password();

if (!$has_access) die('Permission denied...');

$_m3u8 = null;
foreach (glob($_dir."/*.m3u8") as $filename) { // ."/*.m3u8"
    $_m3u8 = $filename;
    break;
}

if ($_m3u8 === null) die('Oops! Something went wrong...');
$m3u8file_content = file_get_contents($_m3u8);


/*
create m3u8 here
*/
//header("Content-type: application/text");
//header('Content-type:application/force-download');
//header("Content-type: application/vnd.apple.mpegurl");
header("Content-type: application/x-mpegURL");
header("Content-Disposition: inline; filename=index.m3u8");
header('Cache-Control: no-cache');


$lines = explode("\n", $m3u8file_content);
foreach($lines as $line) {
    // we need to address to the correct folder:
    if (substr($line, 0, 1) !== '#') echo $_dir.'/'.$line;
    else echo $line;
    if ($line !== "\n") echo "\n";
}

