window.addEventListener("load", () => {
    const MAX_YEAR = 9999;
    let currentYear = new Date().getFullYear();
    let currentMonth = new Date().getMonth();


    renderCalendar(currentMonth, currentYear);

    let calendar = document.getElementById("calendar");
    let generateForm = document.getElementById("generateForm");
    let prevMonth = document.getElementById("prevMonth");
    let nextMonth = document.getElementById("nextMonth");

    prevMonth.addEventListener("click", () => {
        currentMonth--;
        if (currentMonth < 0) {
            currentMonth = 11;
            currentYear--;
        }
        renderCalendar(currentMonth, currentYear);
    });

    nextMonth.addEventListener("click", () => {
        currentMonth++;
        if (currentMonth > 11) {
            currentMonth = 0;
            currentYear++;
        }
        renderCalendar(currentMonth, currentYear);
    });

    generateForm.addEventListener("submit", (event) => {
        event.preventDefault();

        let monthInput = document.getElementById("month");
        let yearInput = document.getElementById("year");


        let month = stringToMonth(monthInput.value);
        let year = parseInt(yearInput.value);

        //Validation
        if (isNaN(year)) {
            year = currentYear;//Unusable Input yields current year
        }


        if (isNaN(month)) {
            month = 0; //Unusable input yields January
        } else {
            //Month needs to be made less than 12 if it's greater
            while (month > 13) {
                month -= 12;
                year++; //Increment year, so that the 24th month is December of the next year
            }
        }

        if (year > MAX_YEAR) {
            year = MAX_YEAR;
        }

        renderCalendar(month, year);
        currentMonth = month;
        currentYear = year;
        monthInput.value = "";
        yearInput.value = "";
    });

    function renderCalendar(month, year) {
        clearCalendar();
        let numDays = new Date(year, month + 1, 0).getDate();

        let monthname = document.getElementById("monthname");
        monthname.innerHTML = monthToString(month) + " " + year;

        let week = 1;

        for (let i = 1; i <= numDays; i++) {
            let today = new Date(year, month, i);
            let weekday = today.getDay();

            let dayModify = document.getElementById("(" + week + "," + weekday + ")");

            dayModify.innerHTML = "<a href=./dayView.php>" + i + "</a>";

            if (weekday > 5) {
                week++;
            }
        }

        //Hide Empty Weeks
        if (document.getElementById("(6,0)").innerHTML == "") {
            document.getElementById("week6").style.visibility = "hidden";
            if (document.getElementById("(5,0)").innerHTML == "") {
                document.getElementById("week5").style.visibility = "hidden";
            }
        }

    }
    function clearCalendar() {
        for (let i = 1; i <= 6; i++) {
            for (let j = 0; j <= 6; j++) {
                document.getElementById("(" + i + "," + j + ")").innerHTML = "";
            }
        }
        document.getElementById("week6").style.visibility = "visible";
        document.getElementById("week5").style.visibility = "visible";
    }

    function monthToString(month) {
        switch (month) {
            case 0:
                return "January";
                break;
            case 1:
                return "February";
                break;
            case 2:
                return "March";
                break;
            case 3:
                return "April";
                break;
            case 4:
                return "May";
                break;
            case 5:
                return "June";
                break;
            case 6:
                return "July";
                break;
            case 7:
                return "August";
                break;
            case 8:
                return "September";
                break;
            case 9:
                return "October";
                break;
            case 10:
                return "November";
                break;
            case 11:
                return "December";
                break;
            default:
                return null; 
        }
    }

    function stringToMonth(month) {
        switch (month) {
            case "January":
                return 0;
                break;
            case "February":
                return 1;
                break;
            case "March":
                return 2;
                break;
            case "April":
                return 3;
                break;
            case "May":
                return 4;
                break;
            case "June":
                return 5;
                break;
            case "July":
                return 6;
                break;
            case "August":
                return 7;
                break;
            case "September":
                return 8;
                break;
            case "October":
                return 9;
                break;
            case "November":
                return 10;
                break;
            case "December":
                return 11;
                break;
            default:
                return null;
        }
    }
    
});