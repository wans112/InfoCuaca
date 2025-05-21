<?php
session_start();

if (!isset($_SESSION["valid_api_key"]) || !isset($_SESSION["api_key"])) {
    header("Location: index.php");
    exit;
}

$weather = null;
$error = '';
$city = '';

if(isset($_POST["kota"])){
    $city = $_POST["kota"];
    $apiKey = $_SESSION["api_key"];
    $url = "http://api.openweathermap.org/data/2.5/weather?q={$city}&appid={$apiKey}&units=metric";
    
    $context = stream_context_create(['http' => ['timeout' => 5]]);
    $response = @file_get_contents($url, false, $context);
    $data = json_decode($response, true);

    if ($data && $data["cod"] == 200) {
        $weather = $data;
    } else {
        $error = "Gagal mendapatkan data cuaca. Pastikan nama kota benar.";
    }
}

function getWeatherIcon($iconCode) {
    return "https://openweathermap.org/img/wn/{$iconCode}@2x.png";
}

function capitalizeWords($str) {
    return ucwords($str);
}

function logout() {
    session_destroy();
    header("Location: index.php");
    exit;
}

// Handle logout
if(isset($_GET["logout"]) && $_GET["logout"] == "true") {
    logout();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OpenWeather App</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">    <style>
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
            min-height: 100vh;
            font-family: var(--font-main);
            color: #212529;
            padding: 20px 0;
        }
        
        .card {
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            border-radius: 12px;
            border: none;
            margin-top: 2rem;
            margin-bottom: 2rem;
            overflow: hidden;
        }
        
        .card-header {
            background-color: white;
            color: var(--dark-color);
            border-bottom: 1px solid rgba(0,0,0,0.05);
            padding: 1.2rem 1.5rem;
        }
        
        .weather-icon {
            width: 100px;
            height: 100px;
            opacity: 0.9;
        }
        
        .temp-display {
            font-size: 3.5rem;
            font-weight: 300;
            color: var(--dark-color);
            margin: 0.5rem 0;
        }
        
        .weather-details {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        }
        
        .detail-item {
            padding: 12px 10px;
            border-bottom: 1px solid rgba(0,0,0,0.04);
            color: #555;
            transition: all 0.2s ease;
        }
        
        .detail-item:hover {
            background-color: rgba(238, 238, 238, 0.4);
        }
        
        .detail-item:last-child {
            border-bottom: none;
        }
        
        .detail-item i {
            color: var(--dark-color);
            width: 20px;
            text-align: center;
        }
        
        .search-bar {
            border-radius: 8px;
            padding: 10px 15px;
            border: 1px solid rgba(0,0,0,0.05);
            box-shadow: none;
            transition: all 0.2s;
        }
        
        .search-bar:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(142, 154, 175, 0.25);
        }
        
        .search-btn {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            border-radius: 8px;
            font-weight: 500;
            padding: 10px 20px;
            transition: all 0.2s;
        }
        
        .search-btn:hover {
            background-color: var(--dark-color);
            border-color: var(--dark-color);
        }
        
        h2, h3, h4 {
            color: var(--dark-color);
            font-weight: 400;
        }
        
        .alert-danger {
            background-color: #fff5f5;
            color: #e53e3e;
            border: 1px solid #fed7d7;
            border-radius: 8px;
        }
        
        .btn-light {
            background-color: #f8f9fa;
            border-color: #eaeaea;
            color: #495057;
            font-weight: 500;
            transition: all 0.2s;
        }
        
        .btn-light:hover {
            background-color: #f1f3f5;
            color: var(--dark-color);
        }
        
        .city-name {
            font-weight: 300;
            font-size: 2rem;
            margin-bottom: 0.2rem;
        }
        
        .date-display {
            color: #6c757d;
            font-size: 0.9rem;
            margin-bottom: 1.5rem;
        }
        
        .weather-description {
            text-transform: capitalize;
            color: #6c757d;
            font-weight: 400;
            margin-top: 0.5rem;
        }
        
        .feels-like {
            color: #6c757d;
            font-size: 0.9rem;
            margin-top: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">Weather</h3>
                        <a href="?logout=true" class="btn btn-sm btn-light">Keluar <i class="fas fa-sign-out-alt"></i></a>
                    </div>
                    
                    <div class="card-body p-4">                        <form action="openweather.php" method="POST" class="mb-4">
                            <div class="input-group">
                                <input type="text" name="kota" class="form-control search-bar" 
                                       placeholder="Cari kota..." required value="<?php echo htmlspecialchars($city); ?>">
                                <button type="submit" class="btn search-btn">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </form>
                        
                        <?php if(!empty($error)): ?>
                            <div class="alert alert-danger" role="alert">
                                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if($weather): ?>                            <div class="text-center mb-4">
                                <h2 class="city-name"><?php echo $weather["name"]; ?>, <?php echo $weather["sys"]["country"]; ?></h2>
                                <p class="date-display"><?php echo date("l, d F Y · H:i", time()); ?></p>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 text-center">
                                    <img src="<?php echo getWeatherIcon($weather["weather"][0]["icon"]); ?>" 
                                         alt="Weather Icon" class="weather-icon">
                                    <div class="temp-display">
                                        <?php echo round($weather["main"]["temp"]); ?>°
                                    </div>
                                    <h4 class="weather-description"><?php echo capitalizeWords($weather["weather"][0]["description"]); ?></h4>
                                    <p class="feels-like">Terasa seperti <?php echo round($weather["main"]["feels_like"]); ?>°C</p>
                                </div>
                                  <div class="col-md-6">
                                    <div class="weather-details">
                                        <div class="detail-item d-flex justify-content-between">
                                            <span><i class="fas fa-wind me-2"></i> Angin</span>
                                            <span><?php echo round($weather["wind"]["speed"] * 3.6); ?> km/h</span>
                                        </div>
                                        <div class="detail-item d-flex justify-content-between">
                                            <span><i class="fas fa-tint me-2"></i> Kelembapan</span>
                                            <span><?php echo $weather["main"]["humidity"]; ?>%</span>
                                        </div>
                                        <div class="detail-item d-flex justify-content-between">
                                            <span><i class="fas fa-eye me-2"></i> Jarak Pandang</span>
                                            <span><?php echo $weather["visibility"] / 1000; ?> km</span>
                                        </div>
                                        <div class="detail-item d-flex justify-content-between">
                                            <span><i class="fas fa-compress-arrows-alt me-2"></i> Tekanan</span>
                                            <span><?php echo $weather["main"]["pressure"]; ?> hPa</span>
                                        </div>
                                        <div class="detail-item d-flex justify-content-between">
                                            <span><i class="fas fa-sun me-2"></i> Matahari Terbit</span>
                                            <span><?php echo date("H:i", $weather["sys"]["sunrise"]); ?></span>
                                        </div>
                                        <div class="detail-item d-flex justify-content-between">
                                            <span><i class="fas fa-moon me-2"></i> Matahari Terbenam</span>
                                            <span><?php echo date("H:i", $weather["sys"]["sunset"]); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>                            <div class="text-center py-5">
                                <div style="margin-bottom: 30px;">
                                    <i class="fas fa-cloud fa-4x" style="color: var(--primary-color); opacity: 0.6;"></i>
                                </div>
                                <h4 style="font-weight: 300; margin-bottom: 15px;">Selamat datang di Weather</h4>
                                <p style="color: #6c757d; margin-bottom: 25px;">Masukkan nama kota untuk melihat informasi cuaca terkini</p>
                                <div class="text-center">
                                    <small style="color: #adb5bd; display: block; margin-top: 20px;">Data cuaca disediakan oleh OpenWeather</small>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>