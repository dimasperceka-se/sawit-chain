// const eye = document.querySelector(".open");
// const eyeoff = document.querySelector(".look-off");
// const eyeConfirm = document.querySelector(".open2");
// const eyeoffConfirm = document.querySelector(".look-off2");
// const passwordField = document.querySelector("input[type=password]");
// const passwordFieldNew = document.getElementById("new_password");
// const passwordFieldConfirm = document.getElementById("confirm_password");

// eye.addEventListener("click", () => {
//   eye.style.display = "none";
//   eyeoff.style.display = "block";
//   passwordField.type = "text";
// });
// eyeoff.addEventListener("click", () => {
//   eyeoff.style.display = "none";
//   eye.style.display = "block";
//   passwordField.type = "password";
// });
// eyeConfirm.addEventListener("click", () => {
//   console.log("test")
//   eyeConfirm.style.display = "none";
//   eyeoffConfirm.style.display = "block";
//   passwordFieldConfirm.type = "text";
// });
// eyeoffConfirm.addEventListener("click", () => {
//   eyeoffConfirm.style.display = "none";
//   eyeConfirm.style.display = "block";
//   passwordFieldConfirm.type = "password";
// });

function error(e) {
  e.add('error');
}

function rmError (e) {
  e.remove('error');
}

function validationEmail() {
  let form = document.getElementById('form-input')
  let email = document.getElementById('email').value
  let text = document.getElementById('valEmail')
  let pattern = /^[^ ]+@[^ ]+\.[a-z]{2,3}$/

  if (email.match(pattern)) {
    form.classList.add('valid')
    form.classList.remove('invalid')
    rmError(form.classList)
    text.innerHTML = ""
    text.style.color = ''
  } else {
    form.classList.remove('valid')
    form.classList.add('invalid')
    error(form.classList)
    text.innerHTML = "Invalid email address."
    text.style.color = '#d02630'
    text.style.fontSize = '14px'
    text.style.marginTop = '8px'
  }

  if (email == '') {
    form.classList.remove('valid')
    form.classList.add('invalid')
    console.log("masak")
    error(form.classList)
    text.innerHTML = "Invalid email address."
    text.style.color = '#d02630'
  }
}
function validationCode() {
  let form = document.getElementById('form-input')
  let code = document.getElementById('code').value
  let text = document.getElementById('valCode')
  let pattern = /(?<=\b)[0-9][0-9]\d[0-9](?=\b)/

  if (code.match(pattern)) {
    form.classList.add('valid')
    form.classList.remove('invalid')
    rmError(form.classList)
    text.innerHTML = ""
    text.style.color = ''
  } else {
    form.classList.remove('valid')
    form.classList.add('invalid')
    error(form.classList)
    text.innerHTML = "Invalid Code. Please re-check the code to your email."
    text.style.color = '#d02630'
    text.style.fontSize = '14px'
    text.style.marginTop = '8px'
  }

  if (code == '') {
    form.classList.remove('valid')
    form.classList.add('invalid')
    console.log("masak")
    error(form.classList)
    text.innerHTML = "Invalid Code. Please re-check the code to your email."
    text.style.color = '#d02630'
  }
}
function validatePasswordConfirm() {
  let form = document.getElementById('form-input')
  let text = document.getElementById('valPasswordConfirm')
  var password = document.getElementById("new_password").value;
  var confirmPassword = document.getElementById("confirm_password").value;
  if (password != confirmPassword) {
    form.classList.add('invalid')
    form.classList.remove('valid')
    error(form.classList)
    text.innerHTML = "The specified password must be identical"
    text.style.color = '#d02630'
    text.style.fontSize = '14px'
    text.style.marginTop = '8px'
  } else {
    form.classList.remove('invalid')
    form.classList.add('valid')
    rmError(form.classList)
    text.innerHTML = ""
    text.style.color = ''
  }
  if (confirmPassword == '') {
    form.classList.remove('valid')
    form.classList.add('invalid')
    error(form.classList)
    text.innerHTML = "The specified password must be identical"
    text.style.color = '#d02630'
  }
}
function validationPassword(password) {
  var password_strength = document.getElementById("password_strength_text");
  var loading = document.getElementById("progress-bar-pass");
  var container_loading = document.getElementById("container-progress");
    //if textBox is empty
    if(password.length==0){
        password_strength.innerHTML = "";
        return;
    }
    //Regular Expressions
    var regex = new Array();
    regex.push("[A-Z]"); //For Uppercase Alphabet
    regex.push("[a-z]"); //For Lowercase Alphabet
    regex.push("[0-9]"); //For Numeric Digits
    regex.push("[$@$!%*#?&]"); //For Special Characters

    var passed = 0;

    //Validation for each Regular Expression
    for (var i = 0; i < regex.length; i++) {
        if((new RegExp (regex[i])).test(password)){
            passed++;
        }
    }

    //Validation for Length of Password
    if(passed > 2 && password.length > 8){
        passed++;
    }

    //Display of Status
    var color = "";
    var passwordStrength = "";
    loading.style.background = "";
    container_loading.style.display = "none";
    
    switch(passed){
        case 0:
          container_loading.style.display = "none"
            break;
        case 1:
          passwordStrength = "Password too Short";
          password_strength.style.fontSize = "14px"
          loading.style.width = "30%"
          loading.style.background = "#d02630"
          container_loading.style.display = "block"
            color = "#d02630";
            break;
        case 2:
          passwordStrength = "Medium  Password";
          password_strength.style.fontSize = "14px"
          loading.style.width = "65%"
          loading.style.background = "#f77d2b"
          container_loading.style.display = "block"
            color = "#f77d2b";
            break;
        case 3:
          passwordStrength = "Strong  Password";
          password_strength.style.fontSize = "14px"
          loading.style.width = "100%"
          loading.style.background = "#2bbe72"
          container_loading.style.display = "block"
            color = "#2bbe72";
            break;
    }
    password_strength.innerHTML = passwordStrength;
    password_strength.style.color = color;
}