function loadReviews(bookId) {
    fetch(`../api/reviews/fetchReviews.php?book_id=${bookId}`)
        .then(res => res.json())
        .then(data => {
            const container = document.getElementById("reviewsContainer");
            container.innerHTML = data.map(r => `
                <div class="review">
                    <strong>${r.name}</strong> ⭐${r.rating}<br>
                    <small>${r.created_at}</small>
                    <p>${r.review}</p>
                </div>
            `).join('');
        });
}

function submitReview(bookId) {
    const rating = document.getElementById("rating").value;
    const review = document.getElementById("review").value;

    const formData = new FormData();
    formData.append("book_id", bookId);
    formData.append("rating", rating);
    formData.append("review", review);

    fetch("../api/reviews/addReview.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.text())
    .then(alert)
    .then(() => loadReviews(bookId));
}
