const form = document.getElementById("signupForm");
const username = form.username;
const email = form.email;
const password = form.password;
const confirm = form.confirm_password;

function showError(input, message) {
  const small = input.parentElement.querySelector(".error-msg");
  small.textContent = message;
  small.style.color = message ? "red" : "green";
}

function validatePassword() {
  const value = password.value;
  if (!value.match(/^(?=.*[A-Z])(?=.*\d).{8,}$/)) {
    showError(password, "Min 8 chars, 1 uppercase, 1 number");
    return false;
  }
  showError(password, "");
  return true;
}

function validateConfirm() {
  if (password.value !== confirm.value) {
    showError(confirm, "Passwords do not match");
    return false;
  }
  showError(confirm, "");
  return true;
}

function validateEmail() {
  const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!regex.test(email.value)) {
    showError(email, "Invalid email format");
    return false;
  }
  showError(email, "");
  return true;
}

// Live validation
password.addEventListener("input", validatePassword);
confirm.addEventListener("input", validateConfirm);
email.addEventListener("input", validateEmail);

// Final check before submit
form.addEventListener("submit", (e) => {
  if (!validatePassword() || !validateConfirm() || !validateEmail()) {
    e.preventDefault();
  }
});
