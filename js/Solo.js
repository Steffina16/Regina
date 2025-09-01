// Solo.js
const imagesPerPage = 20;
const gallery = document.getElementById("gallery");
const pagination = document.getElementById("pagination");

// Create image elements for all images from the PHP array
images.forEach((imagePath, index) => {
  // Create the photo container with the correct class structure
  const photoDiv = document.createElement("div");
  photoDiv.className = "photo";
  
  // Create the frame div
  const frameDiv = document.createElement("div");
  frameDiv.className = "photo-frame";
  
  // Create the image element
  const img = document.createElement("img");
  img.src = imagePath;
  img.alt = "Solo Glamour Image " + (index + 1);
  
  // Create the overlay div
  const overlayDiv = document.createElement("div");
  overlayDiv.className = "photo-overlay";
  
  // Add the heart icon to the overlay
  const heartIcon = document.createElement("i");
  heartIcon.className = "fas fa-heart";
  overlayDiv.appendChild(heartIcon);
  
  // Assemble the structure
  frameDiv.appendChild(img);
  photoDiv.appendChild(frameDiv);
  photoDiv.appendChild(overlayDiv);
  
  // Add to gallery
  gallery.appendChild(photoDiv);
});

// Get all photo elements after creating them
const allPhotos = Array.from(gallery.querySelectorAll(".photo"));
const totalPhotos = allPhotos.length;

function showPage(page) {
  const start = (page - 1) * imagesPerPage;
  const end = start + imagesPerPage;

  allPhotos.forEach((photo, index) => {
    if (index >= start && index < end) {
      photo.style.display = "block";
    } else {
      photo.style.display = "none";
    }
  });

  renderPagination(page);
}

function renderPagination(currentPage) {
  pagination.innerHTML = "";
  const totalPages = Math.ceil(totalPhotos / imagesPerPage);

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