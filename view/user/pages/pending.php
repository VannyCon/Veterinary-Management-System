<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pending Card</title>
  <!-- Font Awesome Link -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../../../assets/css/bootstrap.css">
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f0f4f7;
      margin: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .card {
      background-color: #fff;
      width: 350px;
      padding: 30px;
      box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
      border-radius: 12px;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      text-align: center;
      transition: transform 0.3s ease-in-out;
    }

    .card:hover {
      transform: scale(1.05);
    }

    .status {
      font-size: 1.5rem;
      color: #2c3e50;
      margin-bottom: 20px;
    }

    .status i {
      color: #f39c12;
      margin-right: 10px;
      font-size: 2rem;
    }

    .logout-btn {
      width: 100%;
      padding: 12px;
      background-color: #e74c3c;
      border: none;
      border-radius: 8px;
      color: white;
      font-size: 1rem;
      text-align: center;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    .logout-btn:hover {
      background-color: #c0392b;
    }

    .logout-btn i {
      margin-right: 8px;
    }

    .logout-btn:active {
      transform: scale(0.98);
    }
  </style>
</head>
<body>
  <div class="card">
    <div class="status">
      <i class="fas fa-spinner fa-spin"></i> Pending
    </div>
    <div class="logout-btn">
        <a href="../logout.php" class="d-flex align-items-center justify-content-center" style="color: white; text-decoration: none;">
            <i class="fa fa-arrow-right alt mr-2" aria-hidden="true"></i> Back
        </a>
    </div>
  </div>
</body>
</html>
