document.addEventListener("DOMContentLoaded", function() {
    let sidebar = document.getElementById("sidebar");
    let toggleBtn = document.getElementById("toggle-btn");
    let mainContent = document.querySelector(".main-content");

    if (toggleBtn) {
        toggleBtn.addEventListener("click", function() {
            sidebar.classList.toggle("collapsed");
            mainContent.classList.toggle("collapsed");
            localStorage.setItem("sidebarCollapsed", sidebar.classList.contains("collapsed"));
        });

        if (localStorage.getItem("sidebarCollapsed") === "true") {
            sidebar.classList.add("collapsed");
            mainContent.classList.add("collapsed");
        }
    }
});
