// Clear form
function clearInput() {
    document.getElementById("registerForm").reset();
    document.getElementById("errUsername").textContent = "";
    document.getElementById("errEmail").textContent = "";
    document.getElementById("errPhone").textContent = "";
    document.getElementById("errPassword").textContent = "";
}

// Check username
function checkUsername() {
    var username = document.getElementById("username").value.trim();
    var error = document.getElementById("errUsername");

    if (username.length < 3) {
        error.textContent = "Username must contain at least 3 characters.";
        return false;
    }

    error.textContent = "";
    return true;
}

// Check email
function checkEmail() {
    var email = document.getElementById("email").value.trim();
    var error = document.getElementById("errEmail");
    var pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (!pattern.test(email)) {
        error.textContent = "Invalid email address.";
        return false;
    }

    error.textContent = "";
    return true;
}

// Check phone
function checkPhone() {
    var phone = document.getElementById("phone").value.trim();
    var error = document.getElementById("errPhone");
    var pattern = /^[0-9]{10}$/;

    if (!pattern.test(phone)) {
        error.textContent = "Phone number must contain 10 digits.";
        return false;
    }

    error.textContent = "";
    return true;
}

// Check password
function checkPassword() {
    var password = document.getElementById("password").value;
    var error = document.getElementById("errPassword");

    if (password.length < 8) {
        error.textContent = "Password must contain at least 8 characters.";
        return false;
    }

    error.textContent = "";
    return true;
}

document.getElementById("registerForm").onsubmit = function (event) {
    var validUsername = checkUsername();
    var validEmail = checkEmail();
    var validPhone = checkPhone();
    var validPassword = checkPassword();

    if (!validUsername || !validEmail || !validPhone || !validPassword) {
        event.preventDefault();
    }
};