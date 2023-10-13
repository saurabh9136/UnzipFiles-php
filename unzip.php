<?php

function unzipSubfolders($folderPath) {
    // Get all zip files in the folder
    $zipFiles = glob($folderPath . '/*.zip');

    if (empty($zipFiles)) {
        echo "<p>No zip files found in $folderPath</p>";
    } else {
        foreach ($zipFiles as $zipFile) {
            // Extract the base name of the zip file (excluding extension)
            $folderName = pathinfo($zipFile, PATHINFO_FILENAME);

            // Create a temporary directory to extract the zip contents
            $tempDir = sys_get_temp_dir() . '/' . uniqid('temp_zip_extract_');
            mkdir($tempDir);

            // Extract the contents of the zip file to the temporary directory
            $zip = new ZipArchive;
            if ($zip->open($zipFile) === true) {
                $zip->extractTo($tempDir);
                $zip->close();

                echo "<p>Extracted contents of '$zipFile' to '$tempDir'</p>";

                // Move the contents back to the original folder with the zip file name
                moveContentsToFolder($tempDir, $folderPath, $folderName);

                // Delete the zip file
                unlink($zipFile);

                // Remove the temporary directory
                rmdir($tempDir);
            } else {
                echo "<p>Failed to open zip file: $zipFile</p>";
            }
        }
    }
}

function moveContentsToFolder($source, $destination, $folderName) {
    $files = scandir($source);
    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..') {
            $sourceFile = $source . '/' . $file;
            $destinationFile = $destination . '/' . $folderName . '/' . $file;

            // Ensure the destination folder exists before moving the file
            if (!file_exists($destination . '/' . $folderName)) {
                mkdir($destination . '/' . $folderName, 0777, true);
            }

            // Move or copy the file (change this to move or copy as needed)
            rename($sourceFile, $destinationFile);
        }
    }
}


// Example usage:
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $folderPath = $_POST['folderPath'];
    unzipSubfolders($folderPath);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unzip Subfolders</title>
</head>
<body>
    <h2>Unzip Subfolders</h2>

    <form method="post" action="">
        <label for="folderPath">Enter Folder Path:</label>
        <input type="text" id="folderPath" name="folderPath" required>
        <button type="submit">Unzip Subfolders</button>
    </form>
</body>
</html>
