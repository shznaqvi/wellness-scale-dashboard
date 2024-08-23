// session_timeout.js

// Set initial session timeout (e.g., 15 minutes)
const sessionTimeout = 15 * 60 * 1000; // 15 minutes in milliseconds

// Variable to store last activity time
let lastActivityTime = Date.now();

// Function to reset session timer
function resetSessionTimer() {
    lastActivityTime = Date.now();
    // Send AJAX request to update session on server-side
}

// Function to check session timeout
function checkSessionTimeout() {
    const currentTime = Date.now();
    const elapsedTime = currentTime - lastActivityTime;
  if (elapsedTime > sessionTimeout) {
        // Session has timed out
        // Redirect user to logout page or invalidate session
        window.location.href = 'login.php';
    } else if (elapsedTime > sessionTimeout * 0.8) {
        // Display warning message to the user (e.g., "Your session will expire soon")
        alert('Your session will expire soon. Please continue your activity to keep the session active.');
    }
}

// Event listeners for user activity
document.addEventListener('mousemove', resetSessionTimer);
document.addEventListener('keydown', resetSessionTimer);
document.addEventListener('click', resetSessionTimer);

// Check session timeout periodically
setInterval(checkSessionTimeout, 3000); // Check every minute
