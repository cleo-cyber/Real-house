// Function to handle URL change events
function handleUrlChange() {
    const restrictedUrls = ["Dashboard.html"]; // List of restricted URLs
    const currentUrl = window.location.pathname;


    if (restrictedUrls.includes(currentUrl)) {
        const roleToken = sessionStorage.getItem('token');
        if (!roleToken) {
            // User is not logged in, redirect to login page
            window.location.href = "Login.html";
        } else {
            const decodedToken = atob(roleToken);
            const role = decodedToken.split('|')[2];
            const allowedRoles = [1, 4]; // Define roles allowed to access Dashboard.html

            if (!allowedRoles.includes(parseInt(role))) {
                // User is not authorized, redirect to access denied page
                window.location.href = "AccessDenied.html";
            }
        }
    }
}

// Listen for URL change events
window.addEventListener("popstate", handleUrlChange);
window.addEventListener("pushstate", handleUrlChange);
window.addEventListener("replacestate", handleUrlChange);
