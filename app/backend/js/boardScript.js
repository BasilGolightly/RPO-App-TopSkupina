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

//EDIT
const editBtn = document.getElementById('editBoardBtn');
const obrazec = document.getElementById('editobrazec');
const form = document.getElementById('editBoardForm');
const info = document.getElementById('board-info');

editBtn?.addEventListener('click', () => {
    obrazec.style.display = 'flex';
    form.style.display = 'flex';
});

document.getElementById('cancelEdit')?.addEventListener('click', () => {
    obrazec.style.display = 'none';
    form.style.display = 'none';
});

//delete board
const deleteBtn = document.getElementById('deleteBoardBtn');

deleteBtn?.addEventListener('click', () => {
    const boardId = deleteBtn.dataset.boardId;

    const ok = confirm(
        "Are you sure you want to delete this board?\n\nThis action cannot be undone."
    );

    if (!ok) return;

    window.location.href =
        `backend/php/deleteBoard.php?id=${encodeURIComponent(boardId)}`;
});