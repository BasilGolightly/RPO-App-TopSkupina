const btn = document.getElementById('novpost');
const btnCancel = document.getElementById('cancelPost');
const overlay = document.getElementById('postobrazec');

btn.addEventListener('click', () => {
    overlay.classList.add('is-active');
});

btnCancel.addEventListener('click', () => {
    overlay.classList.remove('is-active');
});
