<?php
session_start();
$error = '';

if(isset($_POST["api_key"])){
    $apiKey = $_POST["api_key"];
    $url = "http://api.openweathermap.org/data/2.5/weather?q=London&appid={$apiKey}&units=metric";
    
    $context = stream_context_create(['http' => ['timeout' => 5]]);
    $response = @file_get_contents($url, false, $context);
    $data = json_decode($response, true);

    if ($data && $data["cod"] == 200) {
        $_SESSION["api_key"] = $apiKey;
        $_SESSION["valid_api_key"] = true;
        header("Location: openweather.php");
        exit;
    } else {
        $error = "API key tidak valid atau terjadi masalah dengan koneksi.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weather</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"><style>
        :root {
            --primary-color: #8e9aaf;
            --secondary-color: #cbc0d3;
            --accent-color: #efd3d7;
            --light-color: #feeafa;
            --dark-color: #5c6d91;
            --font-main: 'Segoe UI', Roboto, sans-serif;
        }
        
        body {
            background-color: #f8f9fa;
            height: 100vh;
            font-family: var(--font-main);
            color: #212529;
        }
        
        .card {
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            border-radius: 12px;
            border: none;
            overflow: hidden;
        }
        
        .card-header {
            background-color: white;
            color: var(--dark-color);
            border-bottom: 1px solid rgba(0,0,0,0.05);
            padding: 1.2rem 1.5rem;
        }
        
        .form-control {
            border-radius: 8px;
            padding: 12px;
            border: 1px solid rgba(0,0,0,0.05);
            box-shadow: none;
            transition: all 0.2s;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(142, 154, 175, 0.25);
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            border-radius: 8px;
            font-weight: 500;
            padding: 12px;
            transition: all 0.2s;
        }
        
        .btn-primary:hover {
            background-color: var(--dark-color);
            border-color: var(--dark-color);
        }
        
        .alert-danger {
            background-color: #fff5f5;
            color: #e53e3e;
            border: 1px solid #fed7d7;
            border-radius: 8px;
        }
        
        h3 {
            color: var(--dark-color);
            font-weight: 400;
        }
        
        a {
            color: var(--primary-color);
            text-decoration: none;
        }
        
        a:hover {
            color: var(--dark-color);
        }
    </style>
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center" style="height: 100vh;">
        <div class="card w-100" style="max-width: 500px;">            <div class="card-header text-center py-3">
                <h3 class="mb-0">Weather</h3>
            </div>
            <div class="card-body p-4 text-center">
                <div style="margin: 20px 0 30px 0;">
                    <i class="fas fa-cloud fa-4x" style="color: var(--primary-color); opacity: 0.6;"></i>
                </div>
                
                <?php if(!empty($error)): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo $error; ?>
                </div>
                <?php endif; ?>
                  <p class="mb-4" style="color: #6c757d;">Masukkan API Key untuk memulai aplikasi</p>
                
                <form action="index.php" method="POST">
                    <div class="mb-4">
                        <input type="text" name="api_key" class="form-control form-control-lg" 
                               placeholder="API Key" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Lanjutkan</button>
                </form>
                
                <div class="mt-4">
                    <small style="color: #adb5bd;">Belum punya API Key? <a href="https://openweathermap.org/api" target="_blank">Daftar di sini</a></small>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>