document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("bookForm");

    if (form) {
        form.addEventListener("submit", function (event) {
            event.preventDefault();

            let bookId = document.getElementById("bookId").value;
            let title = document.getElementById("title").value.trim();
            let author = document.getElementById("author").value.trim();
            let genre = document.getElementById("genre").value;
            let coverImage = document.getElementById("cover_image").files[0];

            let formData = new FormData();
            formData.append("id", bookId);
            formData.append("title", title);
            formData.append("author", author);
            formData.append("genre", genre);
            if (coverImage) {
                formData.append("cover_image", coverImage);
            }

            let endpoint = bookId ? "../api/books/updateBook.php" : "../api/books/addBook.php";

            fetch(endpoint, {
                method: "POST",
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                alert(data.trim());
                location.reload();
            })
            .catch(error => console.error("Error:", error));
        });
    }
});

function openAddBookModal() {
    document.getElementById("modalTitle").innerText = "Add Book";
    document.getElementById("bookId").value = "";
    document.getElementById("title").value = "";
    document.getElementById("author").value = "";
    document.getElementById("genre").value = ""
    document.getElementById("supplier").value = "";
    document.getElementById("cover_image").value = "";
    document.getElementById("bookModal").style.display = "flex";
}

function openEditBookModal(id, title, author, genre) {
    document.getElementById("modalTitle").innerText = "Edit Book";
    document.getElementById("bookId").value = id;
    document.getElementById("title").value = title;
    document.getElementById("author").value = author;
    document.getElementById("genre").value = genre;
    document.getElementById("supplier").value = supplierId;
    document.getElementById("bookModal").style.display = "flex";
}

function closeBookModal() {
    document.getElementById("bookModal").style.display = "none";
}

function deleteBook(bookId) {
    if (confirm("Are you sure you want to delete this book?")) {
        fetch("../api/books/deleteBook.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "book_id=" + encodeURIComponent(bookId)
        })
        .then(response => response.text())
        .then(data => {
            alert(data.trim());
            location.reload();
        })
        .catch(error => console.error("Error:", error));
    }
}
