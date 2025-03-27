let signup = document.getElementById("sign-up-button");
let login = document.getElementById("login");
let email = document.getElementById("email");
let password = document.getElementById("password");

let emailValue = email.value;
let passwordValue = password.value;

signup.addEventListener("click", () => {
	window.location.href = "../views/signup.html";
});

login.addEventListener("click", () => {
	if (emailValue === "" || passwordValue === "") {
		return;
	} else {
		window.location.href = "../index.html";
	}
});
