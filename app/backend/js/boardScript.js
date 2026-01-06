const btn = document.getElementById('novpost');
const btnCancel = document.getElementById('cancelPost');
const overlay = document.getElementById('postobrazec');

btn.addEventListener('click', () => {
    overlay.classList.add('is-active');
});

btnCancel.addEventListener('click', () => {
    overlay.classList.remove('is-active');
});


// NEW DISCUSSION
const newDiscussionButton = document.getElementById("dis-new-button")
const btnCancelDiscussion = document.getElementById('cancelPost-dis');
const overlayDiscussion = document.getElementById('postobrazec-discussion');

newDiscussionButton.addEventListener('click', () => {
    overlayDiscussion.classList.add('is-active');
});

btnCancelDiscussion.addEventListener('click', () => {
    overlayDiscussion.classList.remove('is-active');
});