// Get the download buttons
const androidButton = document.getElementById("android-download-button");
const iosButton = document.getElementById("ios-download-button");

// Add event listeners to the buttons
androidButton.addEventListener("click", () => {
  // Simulate a loading state for the Android download button
  androidButton.innerText = "Downloading...";
  androidButton.disabled = true;
  
  // Perform some action when the download is complete
  setTimeout(() => {
    alert("Download complete!");
    androidButton.innerText = "Download for Android";
    androidButton.disabled = false;
  }, 3000); // Change the timeout value to simulate a longer or shorter download time
});

iosButton.addEventListener("click", () => {
  // Show a message indicating that iOS downloads are not available
  alert("iOS downloads are not currently available.");
});
