<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

const ROOT_DIR = __DIR__ . "/../";
const IGNORE_DIRS = ["extract_files", ".git", "images", "return_forms", "vendor"];
const IGNORE_FILES = ["bootstrap.", ".ttf", "angular", "VistaDB", ".ps1" ];

echo "<br/>ROOT_DIR = " . ROOT_DIR;

$line_count = 0;
$file_count = 0;

$file_type_counts = [
    'php' => 0,
    'html' => 0,
    'js' => 0,
    'css' => 0,
    'other' => 0
];

if (is_dir(ROOT_DIR)) {

    echo "<br/>ROOT_DIR exists";
    Read_Directory(ROOT_DIR, $line_count, $file_count, $file_type_counts);

    echo "<br/><br/>Total number of files = " . $file_count;
    echo "<br/><br/>Total lines of code = " . $line_count;

} else {
    echo "<br/>ROOT_DIR does not exist";
}

echo "<br/><br/>File counts by type:";
foreach ($file_type_counts as $type => $count) {
    echo "<br/>  - " . strtoupper($type) . ": " . $count;
}



function Read_Directory($dir, &$line_count, &$file_count, &$file_type_counts){

    $items = scandir($dir);


    foreach ($items as $item) {

        if ($item != "." && $item != "..") {

            if ($item[0] === '.') {
                echo "<br/> Ignoring " . $item;
                continue;
            }

            if (is_dir($dir . $item)) {

                echo "<br/>" . $item . " is a directory";
                
                if (in_array($item, IGNORE_DIRS)) {

                    echo "<br/>  - Ignoring " . $item;
                    continue;

                } else {

                    Read_Directory($dir . $item . "/", $line_count, $file_count, $file_type_counts);
                }

            } else {
                
                $ignore_file = false;

                foreach (IGNORE_FILES as $file) {

                    if (str_contains($item, $file)) {
                        echo "<br/>  - Ignoring " . $item;
                        $ignore_file = true;
                        break;
                    }
                }

                if ($ignore_file) {

                    continue;       // ignore this file

                } else {

                    ++$file_count;
                    $file_path = $dir . $item;
                    $num_lines = count(file($file_path, FILE_SKIP_EMPTY_LINES | FILE_IGNORE_NEW_LINES));
                    $line_count += $num_lines;
                    echo "<br/>  - " . $item . " has " . $num_lines . " lines of code";

                    switch (pathinfo($item, PATHINFO_EXTENSION)) {
                        case 'php':
                            ++$file_type_counts['php'];
                            break;
                        case 'html':
                            ++$file_type_counts['html'];
                            break;
                        case 'js':
                            ++$file_type_counts['js'];
                            break;
                        case 'css':
                            ++$file_type_counts['css'];
                            break;
                        default:
                            ++$file_type_counts['other'];
                    }

                }   // if (!$ignore_file)

            }

        }   // if ()

    }   // foreach()

}   // function Read_Directory()

?>