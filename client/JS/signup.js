let login = document.getElementById("log-in-button");
let signup = document.getElementById("signup");
let email = document.getElementById("email");
let password = document.getElementById("password");

let emailValue = email.value;
let passwordValue = password.value;

login.addEventListener("click", () => {
	window.location.href = "../views/login.html";
});

signup.addEventListener("click", () => {
	if (emailValue === "" || passwordValue === "") {
		return;
	} else {
		window.location.href = "../index.html";
	}
});
