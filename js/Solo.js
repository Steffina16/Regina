const imagesPerPage = 20;
const gallery = document.getElementById("gallery");
const pagination = document.getElementById("pagination");

// Store the image paths from PHP
// const images = [...]; // already passed from PHP

function createPhotoElement(imagePath, index) {
  const photoDiv = document.createElement("div");
  photoDiv.className = "photo";

  const frameDiv = document.createElement("div");
  frameDiv.className = "photo-frame";

  const img = document.createElement("img");
  img.dataset.src = imagePath; // store path
  img.alt = "Solo Glamour Image " + (index + 1);
  img.loading = "lazy";

  const overlayDiv = document.createElement("div");
  overlayDiv.className = "photo-overlay";

  const heartIcon = document.createElement("i");
  heartIcon.className = "fas fa-heart";
  overlayDiv.appendChild(heartIcon);

  frameDiv.appendChild(img);
  photoDiv.appendChild(frameDiv);
  photoDiv.appendChild(overlayDiv);

  return photoDiv;
}

function showPage(page) {
  // Clear current gallery
  gallery.innerHTML = "";

  const start = (page - 1) * imagesPerPage;
  const end = Math.min(start + imagesPerPage, images.length);

  for (let i = start; i < end; i++) {
    const photo = createPhotoElement(images[i], i);
    gallery.appendChild(photo);

    // Immediately load the image
    const img = photo.querySelector("img");
    img.src = img.dataset.src;
    img.removeAttribute("data-src");
  }

  renderPagination(page);
}

function renderPagination(currentPage) {
  pagination.innerHTML = "";
  const totalPages = Math.ceil(images.length / imagesPerPage);

  for (let i = 1; i <= totalPages; i++) {
    const btn = document.createElement("button");
    btn.textContent = i;
    if (i === currentPage) btn.classList.add("active");

    btn.addEventListener("click", () => showPage(i));
    pagination.appendChild(btn);
  }
}

// Initialize the gallery
showPage(1);
