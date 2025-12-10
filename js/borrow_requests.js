function returnBook(borrow_id) {
    if (!confirm("Are you sure you want to return this book?")) return;

    fetch("../api/books/returnBook.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `borrow_id=${borrow_id}`
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        if (data.status === "success") {
            location.reload(); // Refresh page after returning the book
        }
    })
    .catch(error => console.error("Error:", error));
}
