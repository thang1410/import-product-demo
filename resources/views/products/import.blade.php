<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import File</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background: linear-gradient(to right, #6a11cb, #2575fc);
            font-family: 'Arial', sans-serif;
        }
        .form-container {
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 450px;
        }
        .form-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .form-header h2 {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }
        .btn-primary {
            background-color: #2575fc;
            border-color: #2575fc;
        }
        .btn-primary:hover {
            background-color: #1b63cc;
            border-color: #1b63cc;
        }
    </style>
</head>
<body>
<div class="form-container">
    <div class="form-header">
        <h2>Import File</h2>
    </div>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('products.import') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label for="file" class="form-label">Chọn file (*định dạng .xlsx, .xls)</label>
            <input type="file" class="form-control" name="file" id="file" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Import</button>
    </form>
</div>
</body>
</html>
