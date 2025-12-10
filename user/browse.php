<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}
include '../db/connect.php';

$books = $conn->query("SELECT * FROM books");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Browse Books</title>
    <link rel="stylesheet" href="../css/sidebar.css">
    <link rel="stylesheet" href="../css/browse_books.css">
</head>
<body>

<?php include '../includes/sidebar.php'; ?>

<div class="main-content">
    <h2>Browse Books</h2>
    <input type="text" id="searchBar" placeholder="Search books by title, author, genre..." onkeyup="filterBooks()">

    <div class="books-container" id="booksContainer">
        <?php while ($book = $books->fetch_assoc()): ?>
            <div class="book-card">
            <img src="<?= !empty($book['cover_image']) && file_exists('../uploads/' . $book['cover_image']) 
                            ? '../uploads/' . htmlspecialchars($book['cover_image']) 
                            : '../assets/img/hobit_cover.png' ?>" 
                            class="book-cover" width="50" alt="Book Cover">
                <h3><?= htmlspecialchars($book['title']) ?></h3>
                <p><strong>Author:</strong> <?= htmlspecialchars($book['author']) ?></p>
                <p><strong>Genre:</strong> <?= htmlspecialchars($book['genre']) ?></p>
                <div class="description">
                    <strong>Description:</strong>
                    <span class="short"><?= nl2br(htmlspecialchars(substr($book['description'], 0, 100))) ?>...</span>
                    <span class="full" style="display: none;"><?= nl2br(htmlspecialchars($book['description'])) ?></span>
                    <a href="javascript:void(0);" class="toggle-link" onclick="toggleDescription(this)">Read More</a>
                </div>

                <button class="<?= $book['stock'] <= 0 ? 'out-of-stock' : 'borrow-btn' ?>" 
                        onclick="borrowBook(<?= $book['id'] ?>)" 
                        <?= $book['stock'] <= 0 ? 'disabled' : '' ?>>
                    <?= $book['stock'] <= 0 ? 'Out of Stock' : 'Borrow' ?>
                </button>

            </div>
        <?php endwhile; ?>
    </div>
</div>

<script src="../js/sidebar.js"></script>
<script>
function filterBooks() {
    const input = document.getElementById('searchBar').value.toLowerCase();
    const cards = document.querySelectorAll('.book-card');

    cards.forEach(card => {
        const text = card.innerText.toLowerCase();
        card.style.display = text.includes(input) ? '' : 'none';
    });
}

function borrowBook(bookId) {
    if (confirm("Do you want to borrow this book?")) {
        fetch("../api/books/borrowBook.php", {
            method: "POST",
            body: JSON.stringify({ book_id: bookId }),
            headers: { "Content-Type": "application/json" }
        }).then(res => res.text()).then(alert).then(() => location.reload());
    }
}
</script>
<script>
function toggleDescription(link) {
    const shortDesc = link.previousElementSibling.previousElementSibling;
    const fullDesc = link.previousElementSibling;

    if (fullDesc.style.display === "none") {
        fullDesc.style.display = "inline";
        shortDesc.style.display = "none";
        link.textContent = "Read Less";
    } else {
        fullDesc.style.display = "none";
        shortDesc.style.display = "inline";
        link.textContent = "Read More";
    }
}
</script>

</body>
</html>
