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

echo "Welcome to Music Generator!\n";
echo "Generate custom music based on the insect's danger level.\n\n";

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
$musicFile = $baseName . "_music.wav";

// Ask for duration
echo "How many seconds of music do you want to generate? ";
$duration = -1;
while ($duration < 1) {
    $durationInput = trim(fgets(STDIN));
    if (is_numeric($durationInput) && (int)$durationInput > 0) {
        $duration = (int)$durationInput;
    } else {
        echo "Please enter a valid number of seconds: ";
    }
}

echo "\nGenerating music based on $selectedTxt...\n";
echo "Duration: $duration seconds\n";

// Run Python script to generate music
$command = "python3 generate_music.py " . escapeshellarg($selectedTxt) . " " . $duration . " " . escapeshellarg($musicFile);
$output = [];
$returnVar = 0;
exec($command, $output, $returnVar);

if ($returnVar === 0) {
    echo "\nMusic file generated: $musicFile\n";
} else {
    echo "\nError generating music file.\n";
    print_r($output);
}
