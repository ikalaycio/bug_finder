<?php

function listDeletableFiles($dir) {
    // System files to exclude (PHP and Python scripts)
    $systemExtensions = ['php', 'py'];
    $files = scandir($dir);
    $deletableFiles = [];
    foreach ($files as $file) {
        // Skip directories and hidden files
        if ($file === '.' || $file === '..' || is_dir($dir . '/' . $file) || $file[0] === '.') {
            continue;
        }
        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        // Skip system files
        if (in_array($ext, $systemExtensions)) {
            continue;
        }
        $deletableFiles[] = $file;
    }
    return $deletableFiles;
}

echo "========================================\n";
echo "  FILE MANAGER\n";
echo "========================================\n\n";
echo "Note: System files (.php, .py) are protected and cannot be deleted.\n\n";

$currentDir = getcwd();
$files = listDeletableFiles($currentDir);

if (empty($files)) {
    echo "No deletable files found in the current directory.\n";
    exit;
}

echo "Available files (enter numbers separated by commas, 'all' for all, or 'q' to quit):\n";
echo "--------------------------------------------------------------------------------\n";
foreach ($files as $index => $file) {
    echo ($index + 1) . ". $file\n";
}
echo "--------------------------------------------------------------------------------\n";

echo "\nEnter your selection: ";
$input = trim(fgets(STDIN));

if ($input === 'q' || $input === 'quit') {
    echo "Exiting without deleting any files.\n";
    exit;
}

$filesToDelete = [];

if ($input === 'all') {
    $filesToDelete = $files;
} else {
    $selections = explode(',', $input);
    foreach ($selections as $sel) {
        $sel = trim($sel);
        if (is_numeric($sel)) {
            $index = (int)$sel - 1;
            if ($index >= 0 && $index < count($files)) {
                $filesToDelete[] = $files[$index];
            }
        }
    }
}

if (empty($filesToDelete)) {
    echo "No valid files selected.\n";
    exit;
}

echo "\n========================================\n";
echo "Files to be deleted:\n";
echo "========================================\n";
foreach ($filesToDelete as $file) {
    echo "  - $file\n";
}
echo "\nAre you sure you want to delete these files? (yes/no): ";
$confirm = trim(fgets(STDIN));

if (strtolower($confirm) === 'yes' || strtolower($confirm) === 'y') {
    $deletedCount = 0;
    foreach ($filesToDelete as $file) {
        if (unlink($file)) {
            echo "Deleted: $file\n";
            $deletedCount++;
        } else {
            echo "Failed to delete: $file\n";
        }
    }
    echo "\n========================================\n";
    echo "Deleted $deletedCount file(s) successfully.\n";
    echo "========================================\n";
} else {
    echo "\nDeletion cancelled. No files were deleted.\n";
}
