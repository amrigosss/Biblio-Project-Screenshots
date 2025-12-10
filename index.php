document.getElementById("loginForm").addEventListener("submit", function(event) {
    event.preventDefault();
    let email = document.getElementById("email").value.trim();
    let password = document.getElementById("password").value.trim();
    let errorMessage = document.getElementById("errorMessage");

    fetch("login.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `email=${email}&password=${password}`
    })
    .then(response => response.text())
    .then(data => {
        console.log("Login Response:", data); // Debugging

        if (data.trim() === "admin") {
            window.location.href = "admin/admin_dashboard.php";
        } else if (data.trim() === "user") {
            window.location.href = "user/user_dashboard.php";
        } else {
            errorMessage.textContent = data; // Display error message
        }
    })
    .catch(error => {
        console.error("Error:", error);
        errorMessage.textContent = "Something went wrong.";
    });
});
