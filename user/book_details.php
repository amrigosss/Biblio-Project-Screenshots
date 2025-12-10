<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}
include '../db/connect.php';

$book_id = $_GET['id'] ?? null;
$user_id = $_SESSION['user_id'];

// Fetch book info
$stmt = $conn->prepare("SELECT * FROM books WHERE id = ?");
$stmt->bind_param("i", $book_id);
$stmt->execute();
$book = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Check if user can review
$canReview = false;
$check = $conn->prepare("SELECT * FROM borrow_requests WHERE user_id = ? AND book_id = ? AND return_status = 'returned'");
$check->bind_param("ii", $user_id, $book_id);
$check->execute();
$check->store_result();
if ($check->num_rows > 0) $canReview = true;
$check->close();

// Get existing reviews
$reviews = $conn->prepare("SELECT reviews.rating, reviews.comment, users.name FROM reviews JOIN users ON reviews.user_id = users.id WHERE reviews.book_id = ?");
$reviews->bind_param("i", $book_id);
$reviews->execute();
$reviewResult = $reviews->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title><?= htmlspecialchars($book['title']) ?> - Details</title>
    <link rel="stylesheet" href="../css/sidebar.css">
    <link rel="stylesheet" href="../css/book_details.css"> <!-- Create this later -->
</head>
<body>

<?php include '../includes/sidebar.php'; ?>

<div class="main-content">
    <h2><?= htmlspecialchars($book['title']) ?></h2>
    <p><strong>Author:</strong> <?= htmlspecialchars($book['author']) ?></p>
    <p><strong>Genre:</strong> <?= htmlspecialchars($book['genre']) ?></p>
    <p><strong>Stock:</strong> <?= htmlspecialchars($book['stock']) ?></p>

    <hr>
    <h3>Reviews</h3>
    <?php while ($r = $reviewResult->fetch_assoc()): ?>
        <div class="review">
            <strong><?= htmlspecialchars($r['name']) ?></strong> ⭐ <?= $r['rating'] ?>/5
            <p><?= htmlspecialchars($r['comment']) ?></p>
        </div>
    <?php endwhile; ?>

    <?php if ($canReview): ?>
        <hr>
        <h3>Leave a Review</h3>
        <form action="../api/books/submitReview.php" method="POST">
            <input type="hidden" name="book_id" value="<?= $book_id ?>">
            <label for="rating">Rating (1–5):</label>
            <input type="number" name="rating" min="1" max="5" required>
            <br>
            <label for="comment">Comment:</label>
            <textarea name="comment" rows="4" cols="50" required></textarea>
            <br>
            <button type="submit">Submit Review</button>
        </form>
    <?php else: ?>
        <p style="color:gray;">You can only review this book after returning it.</p>
    <?php endif; ?>
</div>
</body>
</html>
