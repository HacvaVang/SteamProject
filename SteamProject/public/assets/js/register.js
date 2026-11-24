// Clear form
function clearInput() {
    document.getElementById("registerForm").reset();
    document.getElementById("errUsername").textContent = "";
    document.getElementById("errEmail").textContent = "";
    document.getElementById("errPhone").textContent = "";
    document.getElementById("errPassword").textContent = "";
}


// Validate
function checkUsername() {
    const username = document.getElementById("username").value.trim();
    const error = document.getElementById("errUsername");

    if (username.length < 3) {
        error.textContent = "Username must contain at least 3 characters.";
        return false;
    }

    error.textContent = "";
    return true;
}

function checkEmail() {
    const email = document.getElementById("email").value.trim();
    const error = document.getElementById("errEmail");

    const pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (!pattern.test(email)) {
        error.textContent = "Invalid email address.";
        return false;
    }

    error.textContent = "";
    return true;
}

function checkPhone() {
    const phone = document.getElementById("phone").value.trim();
    const error = document.getElementById("errPhone");

    const pattern = /^[0-9]{10}$/;

    if (!pattern.test(phone)) {
        error.textContent = "Phone number must contain 10 digits.";
        return false;
    }

    error.textContent = "";
    return true;
}

function checkPassword() {
    const password = document.getElementById("password").value;
    const error = document.getElementById("errPassword");

    if (password.length < 8) {
        error.textContent = "Password must contain at least 8 characters.";
        return false;
    }

    error.textContent = "";
    return true;
}

document.getElementById("registerForm").onsubmit = function (e) {
    const r1 = checkUsername();
    const r2 = checkEmail();
    const r3 = checkPhone();
    const r4 = checkPassword();

    if (!(r1 && r2 && r3 && r4)) {
        e.preventDefault();
    }
};