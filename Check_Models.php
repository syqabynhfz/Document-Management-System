<?php

require 'vendor/autoload.php';

use Gemini\Client;
use Gemini\Discovery\Api;
use Gemini\Gemini; // <-- INI BARIS YANG HILANG TADI

try {
    // Ganti 'YOUR_API_KEY' dengan API Key Anda
    $apiKey = 'AIzaSyA6lUWrjQ7MVYtAqecrNzEyl22xdssA1x4'; 
    
    // Buat instance client
    // Baris 13 sekarang akan berfungsi karena 'use Gemini\Gemini;' di atas
    $client = Gemini::client($apiKey); 

    echo "Mencoba mengambil daftar model...\n";

    // Panggil listModels()
    $response = $client->models()->listModels();

    echo "Berhasil! Model yang tersedia untuk API Key Anda:\n\n";

    // Loop melalui model yang ditemukan
    foreach ($response->models as $model) {
        // Kita gunakan ->name karena itu format yang diminta API
        echo "Nama Model: " . $model->name . "\n";
        echo "Versi: " . $model->version . "\n";
        echo "--- \n";
    }

} catch (Exception $e) {
    echo "Gagal total saat mengambil daftar model:\n";
    echo $e->getMessage();
}

?>