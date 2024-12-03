<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pending Card</title>
  <!-- Font Awesome Link -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <style>
    body {
      font-family: Arial, sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
      background-color: #f5f5f5;
    }

    .card {
      background-color: #fff;
      width: 300px;
      height: 200px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      border-radius: 8px;
      display: flex;
      justify-content: center;
      align-items: center;
      text-align: center;
    }

    .card .status {
      font-size: 1.5rem;
      color: #555;
    }

    .card .status i {
      color: #f39c12;
      margin-right: 8px;
    }
  </style>
</head>
<body>
  <div class="card">
    <div class="status">
      <i class="fas fa-spinner fa-spin"></i> Pending
    </div>
  </div>
</body>
</html>
