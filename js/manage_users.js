document.addEventListener("DOMContentLoaded", function () {
    let userForm = document.getElementById("userForm");

    userForm.addEventListener("submit", function (event) {
        event.preventDefault();

        let id = document.getElementById("userId").value;
        let name = document.getElementById("name").value.trim();
        let email = document.getElementById("email").value.trim();
        let password = document.getElementById("password").value;
        let role = document.getElementById("role").value;

        if (!name || !email || !role || (!id && !password)) {
            alert("All fields are required, including password for new users!");
            return;
        }

        let formData = new FormData();
        formData.append("id", id);
        formData.append("name", name);
        formData.append("email", email);
        formData.append("role", role);

        if (!id || password) {
            formData.append("password", password);
        }

        let apiUrl = id ? "../api/users/updateUser.php" : "../api/users/addUser.php";

        fetch(apiUrl, {
            method: "POST",
            body: formData,
        })
        .then((response) => response.text())
        .then((data) => {
            alert(data.trim());
            closeUserModal();
            location.reload();
        })
        .catch((error) => {
            console.error("Error:", error);
            alert("Something went wrong. Please check console.");
        });
    });
});

function openUserModal() {
    document.getElementById("modalTitle").textContent = "Add User";
    document.getElementById("userId").value = "";
    document.getElementById("name").value = "";
    document.getElementById("email").value = "";
    document.getElementById("password").value = "";
    document.getElementById("role").value = "user";
    document.getElementById("userModal").style.display = "flex";
}

function editUser(id, name, email, role) {
    document.getElementById("modalTitle").textContent = "Edit User";
    document.getElementById("userId").value = id;
    document.getElementById("name").value = name;
    document.getElementById("email").value = email;
    document.getElementById("password").value = "";
    document.getElementById("role").value = role;
    document.getElementById("userModal").style.display = "flex";
}

function closeUserModal() {
    document.getElementById("userModal").style.display = "none";
}

function deleteUser(id) {
    if (confirm("Are you sure you want to delete this user?")) {
        fetch("../api/users/deleteUser.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ id: id })
        })
        .then(res => res.text())
        .then(data => {
            alert(data.trim());
            location.reload();
        })
        .catch(error => {
            console.error("Error:", error);
            alert("Failed to delete user.");
        });
    }
}
