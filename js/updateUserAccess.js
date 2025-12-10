function updateUserAccess(id, newAccess) {
    fetch("../api/users/updateStatus.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `id=${id}&access=${newAccess}`
    })
    .then(res => res.text())
    .then(data => alert(data))
    .catch(err => alert("Error updating user access"));
}
