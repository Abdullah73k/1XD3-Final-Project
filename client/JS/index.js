window.addEventListener("load", () => {
	const MAX_YEAR = 9999;
	let currentYear = new Date().getFullYear();
	let currentMonth = new Date().getMonth();

	// Get DOM elements
	const monthTitle = document.querySelector(".month-title");
	const calendarBody = document.querySelector("#calendar tbody");
	const generateForm = document.getElementById("generateForm");
	const prevMonthBtn = document.getElementById("prevMonth");
	const nextMonthBtn = document.getElementById("nextMonth");
	const monthSelect = document.getElementById("month");
	const yearInput = document.getElementById("year");

	// Initialize calendar
	renderCalendar(currentMonth, currentYear);

	// Event Listeners
	prevMonthBtn.addEventListener("click", () => {
		currentMonth--;
		if (currentMonth < 0) {
			currentMonth = 11;
			currentYear--;
		}
		renderCalendar(currentMonth, currentYear);
	});

	nextMonthBtn.addEventListener("click", () => {
		currentMonth++;
		if (currentMonth > 11) {
			currentMonth = 0;
			currentYear++;
		}
		renderCalendar(currentMonth, currentYear);
	});

	generateForm.addEventListener("submit", (event) => {
		event.preventDefault();

		const month = parseInt(monthSelect.value);
		let year = parseInt(yearInput.value);

		// Validation
		if (isNaN(year)) year = currentYear;
		if (isNaN(month)) {
			currentMonth = 0;
		} else {
			currentMonth = month;
		}

		if (year > MAX_YEAR) year = MAX_YEAR;

		renderCalendar(currentMonth, year);
		currentYear = year;
	});

	function renderCalendar(month, year) {
		// Update month title
		monthTitle.textContent = `${monthToString(month)} ${year}`;

		// Clear existing calendar
		calendarBody.innerHTML = "";

		// Get days in month and first day
		const daysInMonth = new Date(year, month + 1, 0).getDate();
		const firstDay = new Date(year, month, 1).getDay();

		let date = 1;

		// Create 6 rows (weeks)
		for (let i = 0; i < 6; i++) {
			const row = document.createElement("tr");
			row.className = "week-row";

			// Create 7 cells (days) for each week
			for (let j = 0; j < 7; j++) {
				const cell = document.createElement("td");
				cell.className = "calendar-day";

				if (i === 0 && j < firstDay) {
					// Empty cells before first day
					cell.classList.add("empty");
				} else if (date > daysInMonth) {
					// Empty cells after last day
					cell.classList.add("empty");
				} else {
					// Cells with dates
					cell.innerHTML = `<a href="./dayView.php?day=${date}&month=${month}&year=${year}">${date}</a>`;

					// Highlight current day
					const today = new Date();
					if (
						date === today.getDate() &&
						month === today.getMonth() &&
						year === today.getFullYear()
					) {
						cell.classList.add("today");
					}

					date++;
				}

				row.appendChild(cell);
			}

			calendarBody.appendChild(row);

			// Stop creating rows if we've shown all days
			if (date > daysInMonth) break;
		}
	}

	function monthToString(month) {
		const months = [
			"January",
			"February",
			"March",
			"April",
			"May",
			"June",
			"July",
			"August",
			"September",
			"October",
			"November",
			"December",
		];
		return months[month];
	}
});
