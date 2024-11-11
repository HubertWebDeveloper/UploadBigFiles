<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Large File Upload with Progress</title>
    <style>
        #progressContainer {
            width: 100%;
            background-color: #f3f3f3;
            display: none;
            margin-top: 10px;
        }
        
        #progressBar {
            width: 0%;
            height: 25px;
            background-color: #4CAF50;
            text-align: center;
            color: white;
        }
    </style>
</head>
<body>
    <h2>Upload Large File</h2>
    <input type="file" id="fileInput" />
    <button onclick="uploadFile()">Upload</button>
    
    <div id="progressContainer">
        <div id="progressBar">0%</div>
    </div>
    
    <script>
        const CHUNK_SIZE = 5 * 1024 * 1024; // 5 MB per chunk

        async function uploadFile() {
            const fileInput = document.getElementById("fileInput");
            const progressContainer = document.getElementById("progressContainer");
            const progressBar = document.getElementById("progressBar");

            if (fileInput.files.length === 0) {
                alert("Please select a file.");
                return;
            }

            const file = fileInput.files[0];
            const totalChunks = Math.ceil(file.size / CHUNK_SIZE);
            progressContainer.style.display = "block";

            for (let chunkIndex = 0; chunkIndex < totalChunks; chunkIndex++) {
                const start = chunkIndex * CHUNK_SIZE;
                const end = Math.min(start + CHUNK_SIZE, file.size);
                const chunk = file.slice(start, end);

                const formData = new FormData();
                formData.append("file", chunk);
                formData.append("chunkIndex", chunkIndex);
                formData.append("totalChunks", totalChunks);
                formData.append("fileName", file.name);

                await fetch("upload.php", {
                    method: "POST",
                    body: formData,
                });

                // Update progress
                const progress = Math.floor(((chunkIndex + 1) / totalChunks) * 100);
                progressBar.style.width = progress + "%";
                progressBar.textContent = progress + "%";
            }

            alert("File upload complete!");
            // Hide progress bar after upload is complete
            progressContainer.style.display = "none";
            progressBar.style.width = "0%";
            progressBar.textContent = "0%";
        }
    </script>
</body>
</html>
