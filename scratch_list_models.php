<?php

require __DIR__ . '/vendor/autoload.php';

use Gemini\Laravel\Facades\Gemini;
use Illuminate\Support\Facades\Http;

// We'll use a direct HTTP call to be safe from facade issues
$apiKey = 'AIzaSyArKLchl67Do9cph16PE6Zf7anXOFVPQHQ';
$url = "https://generativelanguage.googleapis.com/v1beta/models?key=" . $apiKey;

$response = file_get_contents($url);
$data = json_decode($response, true);

echo "Available Models:\n";
foreach ($data['models'] as $model) {
    echo "- " . $model['name'] . " (" . $model['displayName'] . ")\n";
}
