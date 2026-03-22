<?php

function listTxtFiles($dir) {
    $files = scandir($dir);
    $txtFiles = [];
    foreach ($files as $file) {
        if (pathinfo($file, PATHINFO_EXTENSION) === 'txt') {
            $txtFiles[] = $file;
        }
    }
    return $txtFiles;
}

echo "Welcome to SRT Generator!\n";
$currentDir = getcwd();
$txtFiles = listTxtFiles($currentDir);

if (empty($txtFiles)) {
    echo "No txt files found in the current directory.\n";
    exit;
}

echo "Please select a txt file from the list:\n";
foreach ($txtFiles as $index => $file) {
    echo ($index + 1) . ". $file\n";
}

$selection = -1;
while ($selection < 1 || $selection > count($txtFiles)) {
    echo "Enter your selection (1-" . count($txtFiles) . "): ";
    $input = trim(fgets(STDIN));
    if (is_numeric($input)) {
        $selection = (int)$input;
    }
}

$selectedTxt = $txtFiles[$selection - 1];
$baseName = pathinfo($selectedTxt, PATHINFO_FILENAME);
$audioFile = $baseName . ".wav";
$srtFile = $baseName . ".srt";

if (!file_exists($audioFile)) {
    echo "Error: Corresponding audio file ($audioFile) not found.\n";
    exit;
}

echo "Generating SRT for $selectedTxt...\n";

// Run Python script to generate SRT
$command = "python3 generate_srt.py " . escapeshellarg($selectedTxt) . " " . escapeshellarg($audioFile) . " " . escapeshellarg($srtFile);
$output = [];
$returnVar = 0;
exec($command, $output, $returnVar);

if ($returnVar === 0) {
    echo "SRT file generated: $srtFile\n";
} else {
    echo "Error generating SRT file.\n";
    print_r($output);
}
