const imagesPerPage = 20;
const totalImages = 6;

const gallery = document.getElementById("gallery");
const pagination = document.getElementById("pagination");

function formatNumber(num) {
    return num.toString().padStart(3, '0'); // 001, 002, etc.
}

function loadImages(page) {
    gallery.innerHTML = "";
    const start = (page - 1) * imagesPerPage + 1;
    const end = Math.min(start + imagesPerPage - 1, totalImages);

    for (let i = start; i <= end; i++) {
        const imageNumber = formatNumber(i);
        const imgPath = `../albums/Funnypic/Chin-${imageNumber}.jpg`;

        const img = document.createElement("img");
        img.src = imgPath;
        img.alt = `Chin ${imageNumber}`;

        const wrapper = document.createElement("div");
        wrapper.className = "photo";
        wrapper.appendChild(img);

        gallery.appendChild(wrapper);
    }

    renderPagination(page);
}

function renderPagination(currentPage) {
    pagination.innerHTML = "";

    const totalPages = Math.ceil(totalImages / imagesPerPage);
    for (let i = 1; i <= totalPages; i++) {
        const btn = document.createElement("button");
        btn.textContent = i;
        if (i === currentPage) btn.classList.add("active");

        btn.addEventListener("click", () => loadImages(i));
        pagination.appendChild(btn);
    }
}

loadImages(1);
