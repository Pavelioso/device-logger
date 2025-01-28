async function getDetailedDeviceInfo() {
    const userAgent = navigator.userAgent;

    // Parse User Agent for models / DOESN't WORK FOR NOW
    let deviceName = "Unknown Device";
    if (/iPhone/i.test(userAgent)) {
        deviceName = "Apple iPhone";
    } else if (/iPad/i.test(userAgent)) {
        deviceName = "Apple iPad";
    } else if (/Samsung|SM-|Galaxy/i.test(userAgent)) {
        deviceName = "Samsung Device";
    } else if (/Pixel/i.test(userAgent)) {
        deviceName = "Google Pixel";
    } else if (/Lenovo/i.test(userAgent)) {
        deviceName = "Lenovo Device";
    } else if (/Huawei/i.test(userAgent)) {
        deviceName = "Huawei Device";
    } else if (/Xiaomi/i.test(userAgent)) {
        deviceName = "Xiaomi Device";
    } else if (/OnePlus/i.test(userAgent)) {
        deviceName = "OnePlus Device";
    }

    // Get GPU Info
    const canvas = document.createElement("canvas");
    const gl = canvas.getContext("webgl") || canvas.getContext("experimental-webgl");
    let gpuInfo = "Unknown GPU";
    if (gl) {
        const debugInfo = gl.getExtension("WEBGL_debug_renderer_info");
        if (debugInfo) {
            gpuInfo = `${gl.getParameter(debugInfo.UNMASKED_VENDOR_WEBGL)} - ${gl.getParameter(
                debugInfo.UNMASKED_RENDERER_WEBGL
            )}`;
        }
    }

    // Get Device Type
    let deviceType = "Unknown";
    if (/Mobile|Android|iP(hone|od)/.test(userAgent)) {
        deviceType = "Mobile";
    } else if (/iPad|Tablet/.test(userAgent) || (navigator.platform === "MacIntel" && navigator.maxTouchPoints > 1)) {
        deviceType = "Tablet";
    } else {
        deviceType = "Desktop";
    }

    // Get Memory Details
    const memoryDetails = navigator.deviceMemory ? navigator.deviceMemory + " GB" : "Unknown";

    // Get Screen Info
    const screenDetails = {
        width: screen.width,
        height: screen.height,
        pixelRatio: window.devicePixelRatio,
    };

    // Capture the current URL
    const currentUrl = window.location.href;

    // Debugging Logs
    console.log("Device Name:", deviceName);
    console.log("Device Type:", deviceType);
    console.log("GPU Info:", gpuInfo);
    console.log("Memory Details:", memoryDetails);
    console.log("Screen Details:", screenDetails);
    console.log("Current URL:", currentUrl);

    return {
        deviceName,
        deviceType,
        gpuInfo,
        screenDetails,
        memoryDetails,
        userAgent,
        currentUrl,
    };
}

// Send device info to the server
getDetailedDeviceInfo().then((details) => {
    console.log("Collected Device Info:", details);

    // Send to server
    fetch("/devicelogger/device-log.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
            deviceDetails: details,
            timestamp: new Date().toISOString(),
        }),
    })
        .then((response) => {
            if (response.ok) {
                console.log("Device info logged successfully!");
            } else {
                console.error("Failed to log device info:", response.statusText);
            }
        })
        .catch((error) => console.error("Error sending device info:", error));
});
