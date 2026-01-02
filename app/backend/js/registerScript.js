document.getElementById('repeatPass').addEventListener('input', function () {
    checkPass();
});

function togglePassword(checkbox, inputId) {
    const input = document.getElementById(inputId);
    input.type = checkbox.checked ? 'text' : 'password';
}

function checkPass(){
    var pass = document.getElementById('password').value;
    var rPass = document.getElementById('repeatPass').value;
    if(pass != rPass)
    {
        document.getElementById('registerBtn').disabled = true;
    } 
    else{
        document.getElementById('registerBtn').disabled = false;
    }
}