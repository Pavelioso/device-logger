<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // GET JSON FROM JS
    $input = json_decode(file_get_contents('php://input'), true);

    // Validate that 'deviceDetails' exists in the incoming data
    if (isset($input['deviceDetails'])) {
        $deviceDetails = $input['deviceDetails'];

        // Extract individual fields with fallbacks for safety
        $deviceName = $deviceDetails['deviceName'] ?? 'Unknown Device';
        $deviceType = $deviceDetails['deviceType'] ?? 'Unknown Type';
        $gpuInfo = $deviceDetails['gpuInfo'] ?? 'Unknown GPU';
        $memoryDetails = $deviceDetails['memoryDetails'] ?? 'Unknown Memory';
        $screenWidth = $deviceDetails['screenDetails']['width'] ?? 'Unknown';
        $screenHeight = $deviceDetails['screenDetails']['height'] ?? 'Unknown';
        $pixelRatio = $deviceDetails['screenDetails']['pixelRatio'] ?? 'Unknown';
        $userAgent = $deviceDetails['userAgent'] ?? 'Unknown User Agent';
        $currentUrl = $deviceDetails['currentUrl'] ?? 'Unknown URL';
        $timestamp = $input['timestamp'] ?? date('Y-m-d H:i:s'); // Use current time if timestamp is missing

        // Get the client's IP address
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'Unknown IP';

        // Define the log file path
        $logFile = __DIR__ . '/device.log';

        // Format the log entry
        $logEntry = "$timestamp | IP: $ipAddress | URL: $currentUrl | Device Name: $deviceName | Device Type: $deviceType | GPU: $gpuInfo | Memory: $memoryDetails | Screen: ${screenWidth}x${screenHeight} (Pixel Ratio: $pixelRatio) | User Agent: $userAgent" . PHP_EOL;

        // Append the log entry to the file
        file_put_contents($logFile, $logEntry, FILE_APPEND);

        // Respond with success
        http_response_code(200);
        echo json_encode(['status' => 'success']);
        exit();
    } else {
        // Handle cases where 'deviceDetails' is missing
        http_response_code(400); // Bad Request
        echo json_encode(['error' => 'Missing deviceDetails in payload']);
        exit();
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Display the log data as an HTML table

    // Define the log file path
    $logFile = __DIR__ . '/device.log';

    // Check if the log file exists
    if (!file_exists($logFile)) {
        echo "<h1>No logs found</h1>";
        exit();
    }

    // Read the log file contents
    $logEntries = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    // Begin HTML output
    echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Device Log</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f4f4f4; }
        tr:nth-child(even) { background-color: #f9f9f9; }
    </style>
</head>
<body>
    <h1>Device Log</h1>
    <table>
        <thead>
            <tr>
                <th>Timestamp</th>
                <th>IP Address</th>
                <th>URL</th>
                <th>Device Name</th>
                <th>Device Type</th>
                <th>GPU</th>
                <th>Memory</th>
                <th>Screen</th>
                <th>Pixel Ratio</th>
                <th>User Agent</th>
            </tr>
        </thead>
        <tbody>";

    // Process each log entry and populate the table
    foreach ($logEntries as $entry) {
        // Split the log entry into its components
        preg_match('/^(.*?) \| IP: (.*?) \| URL: (.*?) \| Device Name: (.*?) \| Device Type: (.*?) \| GPU: (.*?) \| Memory: (.*?) \| Screen: (.*?) \(Pixel Ratio: (.*?)\) \| User Agent: (.*)$/', $entry, $matches);

        // Skip invalid entries
        if (count($matches) !== 11) {
            continue;
        }

        // Extract matched components
        list(, $timestamp, $ipAddress, $currentUrl, $deviceName, $deviceType, $gpuInfo, $memoryDetails, $screen, $pixelRatio, $userAgent) = $matches;

        // Add a row to the table
        echo "<tr>
            <td>$timestamp</td>
            <td>$ipAddress</td>
            <td>$currentUrl</td>
            <td>$deviceName</td>
            <td>$deviceType</td>
            <td>$gpuInfo</td>
            <td>$memoryDetails</td>
            <td>$screen</td>
            <td>$pixelRatio</td>
            <td>$userAgent</td>
        </tr>";
    }

    echo "</tbody>
    </table>
</body>
</html>";
    exit();
} else {
    // Handle unsupported request methods
    http_response_code(405); 
    echo json_encode(['error' => 'Invalid request method']);
    exit();
}
