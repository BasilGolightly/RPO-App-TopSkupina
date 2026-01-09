const titleBtn = document.getElementById('titleBtn');
const titleInput = document.getElementById('title');
const titleForm = document.getElementById('titleForm');
const contentBtn = document.getElementById('contentBtn');
const contentInput = document.getElementById('content');
const contentForm = document.getElementById('contentForm');

titleBtn.addEventListener("click", ()=>{
    titleInput.readOnly = !titleInput.readOnly;
    if(titleInput.readOnly){
        titleInput.classList.add("readonly");
        titleBtn.innerHTML = "Edit title";
        titleForm.submit();
    }
    else{
        titleInput.classList.remove("readonly");
        titleBtn.innerHTML = "Save title";
    }
});

contentBtn.addEventListener("click", ()=>{
    contentInput.readOnly = !contentInput.readOnly;
    if(contentInput.readOnly){
        contentInput.classList.add("readonly");
        contentBtn.innerHTML = "Edit content";
        contentForm.submit();
    }
    else{
        contentInput.classList.remove("readonly");
        contentBtn.innerHTML = "Save Content";
    }
});