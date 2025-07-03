<!DOCTYPE html>
<html>
<head>
    <title>File Upload</title>
</head>
<body>
    <h1>Upload File to S3</h1>
    <form action="{{ route('upload') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div>
            <label for="file">Choose file:</label>
            <input type="file" id="file" name="file">
        </div>
        <button type="submit">Upload</button>
    </form>
</body>
</html>
