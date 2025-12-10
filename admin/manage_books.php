<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
include '../db/connect.php';

// Low stock threshold
$lowStockThreshold = 5;

// Fetch all books
$books = $conn->query("SELECT books.*, suppliers.name AS supplier_name FROM books LEFT JOIN suppliers ON books.supplier_id = suppliers.id");

// Fetch all suppliers for dropdown
$suppliers = $conn->query("SELECT * FROM suppliers");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Books</title>
    <link rel="stylesheet" href="../css/sidebar.css">
    <link rel="stylesheet" href="../css/manage_books.css">
</head>
<body>

<?php include '../includes/sidebar.php'; ?>

<div class="main-content">
    <h2>Manage Books</h2>
    <button class="add-btn" onclick="openAddBookModal()">+ Add Book</button>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Cover</th>
                <th>Title</th>
                <th>Author</th>
                <th>Genre</th>
                <th>Stock</th>
                <th>Supplier</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($book = $books->fetch_assoc()): ?>
                <tr>
                    <td><?= $book['id'] ?></td>
                    <td>
                        <img src="<?= !empty($book['cover_image']) && file_exists('../uploads/' . $book['cover_image']) 
                            ? '../uploads/' . htmlspecialchars($book['cover_image']) 
                            : '../assets/img/hobit_cover.png' ?>" 
                            class="book-cover" width="50" alt="Book Cover">
                    </td>
                    <td><?= htmlspecialchars($book['title']) ?></td>
                    <td><?= htmlspecialchars($book['author']) ?></td>
                    <td><?= htmlspecialchars($book['genre']) ?></td>
                    <td>
                        <?php if ($book['stock'] <= $lowStockThreshold): ?>
                            <span style="color: red; font-weight: bold;">
                                <?= htmlspecialchars($book['stock']) ?> (Low)
                            </span>
                        <?php else: ?>
                            <?= htmlspecialchars($book['stock']) ?>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($book['supplier_name']) ?></td>
                    <td>
                        <button class="edit-btn" onclick="openEditBookModal(
                            <?= $book['id'] ?>,
                            <?= htmlspecialchars(json_encode($book['title']), ENT_QUOTES) ?>,
                            <?= htmlspecialchars(json_encode($book['author']), ENT_QUOTES) ?>,
                            <?= htmlspecialchars(json_encode($book['genre']), ENT_QUOTES) ?>,
                            <?= $book['supplier_id'] ?>,
                            <?= $book['stock'] ?>,
                            <?= htmlspecialchars(json_encode($book['description'] ?? ''), ENT_QUOTES) ?>
                        )">Edit</button>
                        <button class="delete-btn" onclick="deleteBook(<?= $book['id'] ?>)">Delete</button>
                        <?php if ($book['stock'] <= $lowStockThreshold): ?>
                            <button class="order-btn" onclick="orderBook(<?= $book['id'] ?>)">Order</button>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- Add/Edit Book Modal -->
<div id="bookModal" class="modal">
    <div class="modal-content">
        <span onclick="closeBookModal()" class="close">&times;</span>
        <h3 id="modalTitle">Add Book</h3>

        <form id="bookForm" enctype="multipart/form-data">
            <input type="hidden" id="bookId" name="bookId">

            <input type="text" id="title" name="title" placeholder="Title" required>
            <input type="text" id="author" name="author" placeholder="Author" required>
            <input type="number" id="stock" name="stock" placeholder="Stock" min="0" required>

            <!-- ✅ New Description Field -->
            <textarea id="description" name="description" placeholder="Brief description..." rows="3" required></textarea>

            <select id="genre" name="genre" required>
                <option value="">Select Genre</option>
                <option value="Fiction">Fiction</option>
                <option value="Non-Fiction">Non-Fiction</option>
                <option value="Science">Science</option>
                <option value="History">History</option>
                <option value="Fantasy">Fantasy</option>
                <option value="Drama">Drama</option>
                <option value="Horror">Horror</option>
                <option value="Mystery">Mystery</option>
                <option value="Biography">Biography</option>
                <option value="Romance">Romance</option>
                <option value="Adventure">Adventure</option>
            </select>

            <select id="supplier" name="supplier" required>
                <option value="">Select Supplier</option>
                <?php while ($s = $suppliers->fetch_assoc()): ?>
                    <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['name']) ?></option>
                <?php endwhile; ?>
            </select>

            <input type="file" id="cover_image" name="cover_image" accept="image/*">
            <button type="submit">Save</button>
        </form>
    </div>
</div>


<script>
document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("bookForm");

    if (form) {
        form.addEventListener("submit", function (event) {
            event.preventDefault();

            let bookId = document.getElementById("bookId").value;
            let title = document.getElementById("title").value.trim();
            let author = document.getElementById("author").value.trim();
            let genre = document.getElementById("genre").value;
            let stock = document.getElementById("stock").value;
            let supplier = document.getElementById("supplier").value;
            let coverImage = document.getElementById("cover_image").files[0];

            // ✅ Insert this line to grab the description
            let description = document.getElementById("description").value.trim();

            let formData = new FormData();
            formData.append("id", bookId);
            formData.append("title", title);
            formData.append("author", author);
            formData.append("genre", genre);
            formData.append("stock", stock);
            formData.append("supplier", supplier);
            formData.append("description", description);  // ✅ Append it to the form data

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
    document.getElementById("genre").value = "";
    document.getElementById("stock").value = "";
    document.getElementById("supplier").value = "";
    document.getElementById("cover_image").value = "";
    document.getElementById("description").value = "";
    document.getElementById("bookModal").style.display = "flex";
}

function openEditBookModal(id, title, author, genre, supplierId, stock, description) {
    document.getElementById("modalTitle").innerText = "Edit Book";
    document.getElementById("bookId").value = id;
    document.getElementById("title").value = title;
    document.getElementById("author").value = author;
    document.getElementById("genre").value = genre;
    document.getElementById("stock").value = stock;
    document.getElementById("supplier").value = supplierId;
    document.getElementById("description").value = description; // ✅ this now works
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

function orderBook(bookId) {
    let qty = prompt("Enter number of copies to order:", "10");
    if (qty !== null && !isNaN(qty) && Number(qty) > 0) {
        fetch("../api/books/orderBook.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `book_id=${encodeURIComponent(bookId)}&quantity=${encodeURIComponent(qty)}`
        })
        .then(response => response.text())
        .then(data => {
            alert(data.trim());
            location.reload();
        })
        .catch(error => console.error("Error:", error));
    } else {
        alert("Invalid quantity!");
    }
}
</script>

<script src="../js/sidebar.js"></script>
</body>
</html>
