<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

const ROOT_DIR = __DIR__ . "/../";
const IGNORE_DIRS = ["extract_files", ".git", "images", "return_forms", "vendor"];
const IGNORE_FILES = ["bootstrap.", ".ttf", "angular", "VistaDB", ".ps1", ".json", ".lock" ];

echo "<br/>ROOT_DIR = " . ROOT_DIR;

$line_count = 0;
$file_count = 0;

$file_type_counts = [

    'php' => [
        'line_count' => 0,
        'file_count' => 0
    ],
    'html' => [
        'line_count' => 0,
        'file_count' => 0
    ],
    'js' => [
        'line_count' => 0,
        'file_count' => 0
    ],
    'css' => [
        'line_count' => 0,
        'file_count' => 0
    ],
    'sql' => [
        'line_count' => 0,
        'file_count' => 0
    ]
];

if (is_dir(ROOT_DIR)) {

    Read_Directory(ROOT_DIR, $line_count, $file_count, $file_type_counts);

} else {
    echo "<br/>ROOT_DIR does not exist";
}

echo "<br/><br/>Summary:";
echo "<table border='1' cellpadding='5' cellspacing='0'>";
echo "<tr>" . 
        "<th>File<br/>Type</th>" .
        "<th>File<br/>Count</th>" . 
        "<th>Line<br/>Count</th>" . 
    "</tr>";

foreach ($file_type_counts as $type => $count) {
    echo "<tr>" . 
            "<td>" . strtoupper($type) . "</td>" .
            "<td align='right'>" . $count['file_count'] . "</td>" .
            "<td align='right'>" . $count['line_count'] . "</td>" .
        "</tr>";
}

echo "<tr>" . 
        "<td><b>Total</b></td>" .
        "<td align='right'><b>" . $file_count . "</b></td>" .
        "<td align='right'><b>" . $line_count . "</b></td>" .
    "</tr>";

echo "</table>";


function Read_Directory($dir, &$line_count, &$file_count, &$file_type_counts){

    $items = scandir($dir);

    foreach ($items as $item) {

        if ($item != "." && $item != "..") {

            if ($item[0] === '.') {
                echo "<br/> Ignoring " . $item;
                continue;
            }

            if (is_dir($dir . $item)) {

                echo "<br/><br/>" . $item . " is a directory";
                
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
                    $file_type_counts[pathinfo($item, PATHINFO_EXTENSION)]['line_count'] += $num_lines;
                    $file_type_counts[pathinfo($item, PATHINFO_EXTENSION)]['file_count'] += 1;
                    $line_count += $num_lines;
                    echo "<br/>  - " . $item . " has " . $num_lines . " lines of code";
/*
                    switch (pathinfo($item, PATHINFO_EXTENSION)) {
                        case 'php':
                            ++$file_type_counts['php']['file_count'];
                            break;
                        case 'html':
                            ++$file_type_counts['html']['file_count'];
                            break;
                        case 'js':
                            ++$file_type_counts['js']['file_count'];
                            break;
                        case 'css':
                            ++$file_type_counts['css']['file_count'];
                            break;
                        default:
                            ++$file_type_counts['other']['file_count'];
                    }
*/
                }   // if (!$ignore_file)

            }

        }   // if ()

    }   // foreach()

}   // function Read_Directory()

?>