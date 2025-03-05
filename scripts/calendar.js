function changeMonth(month, year) {
    window.location.href = `calendar.php?month=${month}&year=${year}`;
}

function toggleMonthDropdown() {
    document.getElementById("month-dropdown").classList.toggle("hidden");
}

function toggleYearDropdown() {
    document.getElementById("year-dropdown").classList.toggle("hidden");
}

function updateMonthYear() {
    const selectedMonth = document.getElementById("month-dropdown").value;
    const selectedYear = document.getElementById("year-dropdown").value;
    window.location.href = `calendar.php?month=${selectedMonth}&year=${selectedYear}`;
}

function resetToCurrentMonth() {
    const currentDate = new Date();
    const currentMonth = currentDate.getMonth() + 1; 
    const currentYear = currentDate.getFullYear();
    window.location.href = `calendar.php?month=${currentMonth}&year=${currentYear}`;
}