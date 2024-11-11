<?php
$uploadDir = 'uploads/';
$fileName = $_POST['fileName'];
$chunkIndex = $_POST['chunkIndex'];
$totalChunks = $_POST['totalChunks'];

$chunkFile = $uploadDir . $fileName . '.part' . $chunkIndex;

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Save the chunk
if (move_uploaded_file($_FILES['file']['tmp_name'], $chunkFile)) {
    // When all chunks have been uploaded, merge them into a single file
    if ($chunkIndex == $totalChunks - 1) {
        $finalFile = fopen($uploadDir . $fileName, 'wb');
        
        for ($i = 0; $i < $totalChunks; $i++) {
            $chunkPath = $uploadDir . $fileName . '.part' . $i;
            $chunkData = file_get_contents($chunkPath);
            fwrite($finalFile, $chunkData);
            unlink($chunkPath); // Remove chunk after appending
        }

        fclose($finalFile);

        // Store file path and name in the database
        $pdo = new PDO('mysql:host=localhost;dbname=uploadbigfiles', 'root', '');
        $stmt = $pdo->prepare("INSERT INTO files (name, path) VALUES (?, ?)");
        $stmt->execute([$fileName, $uploadDir . $fileName]);
    }

    echo json_encode(["status" => "success", "message" => "Chunk $chunkIndex uploaded successfully."]);
} else {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Failed to upload chunk $chunkIndex."]);
}
?>

?>
