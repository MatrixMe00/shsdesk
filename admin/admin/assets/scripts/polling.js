$(document).ready(function() {
    const poll_elements = [
        "notification", "displaced", "transfers"
    ];
    
    polling(poll_elements); // Start polling for all elements
});

async function polling(elements) {
    try {
        const response = await ajaxCall({
            url: "admin/polling.php",
            method: "POST",
            returnType: "json",
        });

        // Loop through each key-value pair in the response
        $.each(response, (key, value) => {
            const response_element = $("#" + key + "_element");
            response_element.html(parseInt(value) === 0 ? "" : value); // Update the element
        });

        // Re-trigger polling after receiving a response
        setTimeout(() => polling(elements), 5000); // Poll every 5 seconds
    } catch (error) {
        if (error.statusText !== "timeout") {
            console.error("Polling error:", error);
        }

        // Retry polling even if there was a timeout
        setTimeout(() => polling(elements), 5000); // Retry after 5 seconds
    }
}
