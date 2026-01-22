/**
 * Password Protection Script
 * Protects pages from unauthorized access
 */

(function () {
  "use strict";

  // Check if already authenticated in this session
  if (sessionStorage.getItem("ausittt_authenticated") !== "true") {
    var password = prompt(
      "This page is password protected. Please enter the password:",
    );
    var correctPassword = "AUSITTT2026"; // Change this to your desired password

    if (password !== correctPassword) {
      alert("Incorrect password. Redirecting to homepage.");
      window.location.href = "index.html";
    } else {
      sessionStorage.setItem("ausittt_authenticated", "true");
    }
  }
})();
