const url = "http://localhost/realhouse_api/User/signup.php";

let firstname = document.querySelector(".firstname");
let lastname = document.querySelector(".lastname");
let email = document.querySelector(".email");
let password = document.querySelector(".password");
let password2 = document.querySelector(".password2");
let registerBtn = document.querySelector(".registerBtn");
let Tenant = document.querySelector(".radio1");
let Realtor = document.querySelector(".radio2");

document.getElementById('togglePassword').addEventListener('change', function (e) {
    if (this.checked) {
        password.type = 'text';
        password2.type = 'text';  // Apply to both password fields
    } else {
        password.type = 'password';
        password2.type = 'password';  // Apply to both password fields
    }
});

password.addEventListener('input', (e) => {
    const passwordValue = e.target.value;

    const hasLetter = /[a-zA-Z]/.test(passwordValue);
    const hasNumber = /[0-9]/.test(passwordValue);
    const hasSpecialChar = /[!@#$%^&*(),.?":{}|<>]/.test(passwordValue);

    if (passwordValue.length < 6) {
        e.target.setCustomValidity('Password must be at least 6 characters long.');
    } else if (!hasLetter || !hasNumber || !hasSpecialChar) {
        e.target.setCustomValidity('Password must contain at least one letter, one number, and one special character.');
    } else {
        e.target.setCustomValidity('');
    }
});

firstname.addEventListener('input', (e) => {
    e.target.value = e.target.value.replace(/[^a-zA-Z]/g, '');
});

lastname.addEventListener('input', (e) => {
    e.target.value = e.target.value.replace(/[^a-zA-Z]/g, '');
});

email.addEventListener('input', (e) => {
    e.target.value = e.target.value.replace(/[^a-zA-Z0-9@.]/g, '');
});

registerBtn.addEventListener("click", async (e) => {
    e.preventDefault();
    
    const fnameValue = firstname.value.trim();
    const lnameValue = lastname.value.trim();
    const emailValue = email.value.trim();
    const passwordValue = password.value.trim();
    const cpasswordValue = password2.value.trim();

    const all_inputs = [firstname, lastname, email, password, password2];

    let valid = true;
    all_inputs.forEach((input) => {
        if (input.value.trim() === '') {
            input.style.borderColor = 'red';
            valid = false;
        } else {
            input.style.borderColor = '';
        }
    });

    if (!valid || (!Tenant.checked && !Realtor.checked)) {
        alert('Please fill in all fields.');
        return;
    }

    if (passwordValue !== cpasswordValue) {
        alert("Password and confirm password do not match");
        return;
    }

    const role = Tenant.checked ? Tenant.value : Realtor.checked ? Realtor.value : null;

    const data = {
        firstname: fnameValue,
        lastname: lnameValue,
        email: emailValue,
        password: passwordValue,
        password2: cpasswordValue,
        role: role
    };

    try {
        const res = await fetch(url, {
            method: 'POST',
            body: JSON.stringify(data),
            headers: {
                'Content-Type': 'application/json'
            }
        });

        const resdata = await res.json();

        if (resdata.status) {
            alert(resdata.message);
            window.location.href = "Login.html";
        } else {
            // if (resdata.message === "Email already exists") {
            //     alert('Email already exists');
            //     window.location.href = "Signup.html";
            // } else if (resdata.message === "Password must be at least 6 characters and must contain at least one lower case letter, one upper case letter and one digit") {
            //     alert('Password must be at least 6 characters and must contain at least one lower case letter, one upper case letter and one digit');
            // }
            if (resdata.status === false) {
                alert(resdata.message);

        }
    }
    } catch (error) {
        console.error('Error:', error);
    }
});
