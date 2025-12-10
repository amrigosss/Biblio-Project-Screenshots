document.getElementById("loginForm").addEventListener("submit", function (event) {
    event.preventDefault();

    let formData = new FormData(this);

    fetch("login.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        if (data.trim() === "admin") {
            window.location.href = "admin/admin_dashboard.php";
        } else if (data.trim() === "user") {
            window.location.href = "user/user_dashboard.php";
        } else {
            alert(data.trim());
        }
    })
    .catch(error => console.error("Error:", error));
});
