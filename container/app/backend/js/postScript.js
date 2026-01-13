const titleBtn = document.getElementById('titleBtn');
const titleInput = document.getElementById('title');
const titleForm = document.getElementById('titleForm');
const contentBtn = document.getElementById('contentBtn');
const contentInput = document.getElementById('content');
const contentForm = document.getElementById('contentForm');
const ratingInput = document.getElementById('ratingField');
const rateBtn = document.getElementById('rateBtn');
let stars = [];
let filled = 0;

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


function fillStars(count) {
    for (let i = 1; i <= 5; i++) {
        document.getElementById('star' + i).innerHTML =
            i <= count ? "&#9733;" : "&#9734;";
    }
}

function saveStars(i){
    rateBtn.disabled = false;
    filled = i;
    ratingInput.value = i;
    fillStars(i);
}


for(let i = 1; i <= 5; i++){
    const currentStar = document.getElementById('star' + i);
    stars.push(currentStar);
    stars[(i-1)].addEventListener("mouseover", ()=>{ fillStars(i); });
    stars[(i-1)].addEventListener("mouseout", ()=>{ fillStars(filled) });
    stars[(i-1)].addEventListener("click", ()=>{ saveStars(i); });
}