document.addEventListener("DOMContentLoaded", function () {
    const contentDiv = document.getElementById("content");
    const tabs = document.querySelectorAll(".tab");
    const searchInput = document.querySelector(".search-bar input");
    const faqItems = document.querySelectorAll(".faq-item"); // Collect all FAQ items
    const categoryButtons = document.querySelectorAll(".category-btn"); // Collect category buttons

    const pages = {
        faq: `<h2>FAQ</h2><div class="faq-item"><h3>How do I reset my password?</h3><p>Go to settings and click "Reset Password".</p></div>`,
        contact: `<h2>Contact Us</h2><p>Email us at support@carelocal.com or call (123) 456-7890.</p>`,
        general: `<h2>General Info</h2><p>General information about our platform.</p>`,
        account: `<h2>Account</h2><p>Manage your account settings here.</p>`,
        services: `<h2>Services</h2><p>Discover the services we offer.</p>`
    };

    // Handle tab switching (for dynamic content)
    tabs.forEach(tab => {
        tab.addEventListener("click", function () {
            const page = this.getAttribute("data-page");
            contentDiv.innerHTML = pages[page] || `<p>Content not found.</p>`;
        });
    });

    // Handle search functionality
    searchInput.addEventListener("input", function () {
        const query = this.value.toLowerCase();
        let results = 0;

        faqItems.forEach(item => {
            const question = item.querySelector("h3").textContent.toLowerCase();
            const answer = item.querySelector(".answer") ? item.querySelector(".answer").textContent.toLowerCase() : '';

            // Show the item if it matches the search query
            if (question.includes(query) || answer.includes(query)) {
                item.style.display = 'block'; // Show the matching item
                results++;
            } else {
                item.style.display = 'none'; // Hide non-matching items
            }
        });

        // If no results are found, show a "No results" message
        if (results === 0) {
            document.getElementById("content").innerHTML = "<p>No results found.</p>";
        }
    });

    // Function to filter and display the FAQ content based on the selected category
    categoryButtons.forEach(button => {
        button.addEventListener("click", function () {
            // Hide all sections
            document.querySelectorAll('.faq-content').forEach(section => section.style.display = 'none');

            // Show the section corresponding to the clicked category
            const category = this.textContent.trim().toLowerCase();
            const contentSection = document.getElementById(`${category}-content`);
            contentSection.style.display = 'block';

            // Optionally, you can highlight the active category button
            categoryButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
        });
    });
});

// Toggle the fullscreen menu visibility
function toggleMenu() {
    const menu = document.getElementById('menu');
    menu.style.display = menu.style.display === 'flex' ? 'none' : 'flex';
}

// Dynamically set the page name and close the menu when navigating
function setPage(pageName) {
    document.getElementById('current-page').innerText = pageName;
    toggleMenu(); // Close menu
}

