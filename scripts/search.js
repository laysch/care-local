document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.querySelector(".searchquery");
    const searchForm = document.getElementById("searchbar");

    // Create and append search results container
    let searchResultsContainer = document.createElement("div");
    searchResultsContainer.classList.add("search-results");
    searchForm.appendChild(searchResultsContainer);

    // Prevent form submission on Enter key press
    searchForm.addEventListener("submit", function (event) {
        event.preventDefault();
    });

    searchInput.addEventListener("keydown", function (event) {
        if (event.key === "Enter") {
            event.preventDefault();
        }
    });

    searchInput.addEventListener("input", function () {
        const query = this.value.trim();

        if (query.length < 2) {
            searchResultsContainer.style.display = "none"; // Hide results when input is empty
            searchResultsContainer.innerHTML = "";
            return;
        }

        fetch(`/search.php?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(results => {
                searchResultsContainer.innerHTML = "";

                if (results.length === 0) {
                    searchResultsContainer.style.display = "none";
                    return;
                }

                searchResultsContainer.style.display = "block"; // Show results only if they exist

                results.forEach(result => {
                    const item = document.createElement("div");
                    item.classList.add("search-item");

                    let link = "#";
                    if (result.type === "job") {
                        link = `job-details.php?id=${result.id}`;
                    } else if (result.type === "user") {
                        link = `profile.php?id=${result.id}`;
                    } else if (result.type === "event") {
                        link = `event-details.php?id=${result.id}`;
                    }

                    item.innerHTML = `<a href="${link}">${result.title} <small>(${result.type})</small></a>`;
                    searchResultsContainer.appendChild(item);
                });
            })
            .catch(error => console.error("Error fetching search results:", error));
    });

    // Hide results when clicking outside the search box
    document.addEventListener("click", function (e) {
        if (!searchForm.contains(e.target) && !searchResultsContainer.contains(e.target)) {
            searchResultsContainer.style.display = "none";
        }
    });
});
