document.addEventListener("DOMContentLoaded", function () {
    function updateBorrowStatus(button, requestId, status) {
        if (!requestId || !status) {
            alert("Invalid request data");
            return;
        }

        if (confirm(`Are you sure you want to ${status} this request?`)) {
            fetch("../api/borrowRequests/updateBorrowRequest.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ request_id: requestId, status: status })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);

                    const row = button.closest("tr");
                    if (!row) return;

                    const statusCell = row.querySelector("td:nth-child(4)");
                    const actionCell = row.querySelector("td:nth-child(6)");

                    if (statusCell && actionCell) {
                        statusCell.textContent = status.charAt(0).toUpperCase() + status.slice(1);
                        statusCell.className = 'status-' + status.toLowerCase();

                        actionCell.innerHTML = status === "approved"
                            ? `<span class="approved-label">✅ Approved</span>`
                            : `<span class="rejected-label">❌ Rejected</span>`;
                    }
                } else {
                    alert("Error: " + data.error);
                }
            })
            .catch(error => console.error("Fetch Error:", error));
        }
    }

    document.querySelectorAll(".approve-btn").forEach(button => {
        button.addEventListener("click", function () {
            const requestId = this.getAttribute("data-id");
            updateBorrowStatus(this, requestId, "approved");
        });
    });

    document.querySelectorAll(".reject-btn").forEach(button => {
        button.addEventListener("click", function () {
            const requestId = this.getAttribute("data-id");
            updateBorrowStatus(this, requestId, "rejected");
        });
    });
});
