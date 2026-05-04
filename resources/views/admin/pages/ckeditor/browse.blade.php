<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Browser</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .file-item {
            border: 1px solid #ddd;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
        }
        .file-preview {
            width: 100px;
            height: 100px;
            object-fit: cover;
            margin-right: 15px;
        }
        .file-icon {
            font-size: 50px;
            color: #6c757d;
            margin-right: 15px;
            text-align: center;
            width: 100px;
        }
        .file-info {
            flex: 1;
        }
        .browse-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container mt-4 mb-5">
        <div class="browse-header">
            <h4>File Browser</h4>
            <form action="{{ route('ckeditor.upload') }}" method="post" enctype="multipart/form-data" id="uploadForm">
                @csrf
                <div class="input-group">
                    <input type="file" class="form-control" name="upload" id="fileUpload">
                    <button class="btn btn-primary" type="submit">Upload</button>
                </div>
            </form>
        </div>

        @if(count($files) > 0)
            <div class="row">
                @foreach($files as $file)
                    <div class="col-md-6 mb-3">
                        <div class="file-item d-flex align-items-center">
                            @php
                                $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                                $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                            @endphp
                            
                            @if($isImage)
                                <img src="{{ $file['url'] }}" alt="{{ $file['name'] }}" class="file-preview">
                            @else
                                <div class="file-icon">
                                    <i class="bi bi-file-earmark"></i>
                                </div>
                            @endif
                            
                            <div class="file-info">
                                <h6 class="mb-1">{{ $file['name'] }}</h6>
                                <p class="mb-1 small">Size: {{ $file['size'] }}</p>
                                <p class="mb-1 small">Modified: {{ $file['modified'] }}</p>
                                <button class="btn btn-sm btn-primary" onclick="selectFile('{{ $file['url'] }}')">Select</button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="alert alert-info">
                No files uploaded yet. Use the upload button above to add files.
            </div>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function selectFile(url) {
            window.opener.CKEDITOR.tools.callFunction({{ request('CKEditorFuncNum', 1) }}, url);
            window.close();
        }

        // Handle AJAX file upload
        document.getElementById('uploadForm').addEventListener('submit', function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            
            fetch('{{ route('ckeditor.upload') }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.uploaded) {
                    window.location.reload();
                } else {
                    alert('Upload failed: ' + data.error.message);
                }
            })
            .catch(error => {
                alert('Error: ' + error);
            });
        });
    </script>
</body>
</html> 