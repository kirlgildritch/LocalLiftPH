<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Too Large</title>
    <style>
        body {
            margin: 0;
            font-family: Poppins, Arial, sans-serif;
            background: #f7f8fb;
            color: #1f2937;
        }

        .upload-error-page {
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 24px;
        }

        .upload-error-card {
            width: min(680px, 100%);
            background: #fff;
            border-radius: 20px;
            padding: 28px;
            box-shadow: 0 18px 45px rgba(15, 23, 42, 0.08);
        }

        .upload-error-card h1 {
            margin: 0 0 12px;
            font-size: 28px;
        }

        .upload-error-card p {
            margin: 0 0 16px;
            line-height: 1.65;
        }

        .upload-error-card ul {
            margin: 0 0 20px;
            padding-left: 18px;
            line-height: 1.7;
        }

        .upload-error-actions a {
            display: inline-block;
            padding: 12px 18px;
            border-radius: 12px;
            background: #111827;
            color: #fff;
            text-decoration: none;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <main class="upload-error-page">
        <section class="upload-error-card">
            <h1>Upload Too Large</h1>
            <p>{{ $uploadErrorMessage }}</p>
            <ul>
                <li>Current PHP upload limit: {{ $serverUploadMax }}</li>
                <li>Current PHP total request limit: {{ $serverPostMax }}</li>
                <li>Current review validation limit: {{ $reviewFileMax }} per file</li>
            </ul>
            <p>To accept a 33 MB video, increase both <code>upload_max_filesize</code> and <code>post_max_size</code> above 33 MB, then restart PHP or your local server.</p>
            <div class="upload-error-actions">
                <a href="javascript:history.back()">Go Back</a>
            </div>
        </section>
    </main>
</body>
</html>
