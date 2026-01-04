const btn = document.getElementById('novboard');
const btnCancel = document.getElementById('cancelBoard');
const overlay = document.getElementById('boardobrazec');

btn.addEventListener('click', () => {
    overlay.classList.add('is-active');
});

btnCancel.addEventListener('click', () => {
    overlay.classList.remove('is-active');
});
