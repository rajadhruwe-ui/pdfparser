<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload PDF</title>
</head>
<body>
    <h2>Upload a PDF File</h2>
    <form action="upload.php" method="POST" enctype="multipart/form-data">
        <input type="file" name="pdf_file" accept="application/pdf" required>
        <button type="submit">Upload</button>
    </form>
</body>
</html>
