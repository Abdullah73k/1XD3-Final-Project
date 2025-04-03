document.addEventListener("DOMContentLoaded", () => {
	const loginForm = document.querySelector(".login");
	const signupForm = document.querySelector(".signup");

	if (loginForm) {
		loginForm.addEventListener("submit", handleSubmit);
	}
	if (signupForm) {
		signupForm.addEventListener("submit", handleSubmit);
	}

	function handleSubmit(e) {
		e.preventDefault();
		const form = e.target;
		const action = form.classList.contains("login") ? "login" : "signup";
        console.log(action);

		const email = form.querySelector("#email").value;
		const password = form.querySelector("#password").value;

		const payload = {
			email: email,
			password: password,
            action: action,
		};

        console.log(payload);
		fetch("http://localhost/1XD3-Final-Project/server/auth/auth.php", {
			method: "POST",
			headers: {
				"Content-Type": "application/json",
			},
			body: JSON.stringify(payload),
		})
			.then((response) => response.json())
			.then((data) => {
				if (data) {
                    console.log("Success, here's the data:", data);
					if (action === "login" && data['success'] === true) {
						window.location.href = "../index.html";
					} else if (action === "signup" && data['success'] === true) {
						window.location.href = "../views/login.html";
					} else {
                        const errorMessage = document.getElementById("p");
                        errorMessage.textContent = data['message'];
                    }
				}
			})
			.catch((error) => console.error("Error:", error));
	}
});