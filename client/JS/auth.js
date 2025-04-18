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
	  const email = form.querySelector("#email").value;
	  const password = form.querySelector("#password").value;
	  const errorMessage = document.getElementById("p");
  
	  if (action === "signup") {
		const validEmailDomains = ["gmail.com", "hotmail.com", "outlook.com", "yahoo.com", "icloud.com"];
		const emailRegex = /^[^\s@]+@([^\s@]+)\.([^\s@]+)$/;
  
		if (!emailRegex.test(email)) {
		  errorMessage.textContent = "Email must be in a valid format with @ and . characters.";
		  return;
		}
  
		const emailDomain = email.split("@")[1].toLowerCase();
		const isDomainValid = validEmailDomains.some((domain) =>
		  emailDomain.includes(domain.split(".")[0])
		);
  
		if (!isDomainValid) {
		  errorMessage.textContent = "Please use a common email provider (Gmail, Hotmail, Outlook, Yahoo, etc.)";
		  return;
		}
  
		if (password.length < 8) {
		  errorMessage.textContent = "Password must be at least 8 characters long.";
		  return;
		}
  
		const hasUppercase = /[A-Z]/.test(password);
		const hasNumber = /[0-9]/.test(password);
  
		if (!hasUppercase || !hasNumber) {
		  errorMessage.textContent = "Password must contain at least one uppercase letter and one number.";
		  return;
		}
	  }
  
	  const payload = {
		email: email,
		password: password,
		action: action,
	  };
  
	  fetch("https://cs1xd3.cas.mcmaster.ca/~khamia4/1XD3-Final-Project/server/auth/auth.php", {
		method: "POST",
		credentials: "include",
		headers: {
		  "Content-Type": "application/json",
		},
		body: JSON.stringify(payload),
	  })
		.then((response) => response.json())
		.then((data) => {
		  if (data) {
			if (action === "login" && data.success === true) {
			  window.location.href = "../index.html";
			} else if (action === "signup" && data.success === true) {
			  window.location.href = "../views/login.html";
			} else {
			  errorMessage.textContent = data.message;
			}
		  }
		})
		.catch((error) => {
		  console.error("Error:", error);
		  errorMessage.textContent = "Something went wrong. Try again.";
		});
	}
  });
  