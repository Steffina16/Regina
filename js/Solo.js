// solo.js
document.addEventListener("DOMContentLoaded", () => {
  const imagesPerPage = 20;
  const gallery = document.getElementById("gallery");
  const pagination = document.getElementById("pagination");
  const lightbox = document.getElementById("lightbox");
  const lightboxImg = document.getElementById("lightbox-img");
  const clickSound = document.getElementById("clickSound"); // optional

  if (!gallery || !pagination || !lightbox || !lightboxImg) {
    console.error("solo.js: missing required DOM elements.");
    return;
  }

  if (typeof images === "undefined" || !Array.isArray(images)) {
    console.error("solo.js: 'images' array is not defined.");
    return;
  }

  // Play click sound if enabled
  function playClickSound() {
    if (clickSound && localStorage.getItem("soundEnabled") === "true") {
      const clone = clickSound.cloneNode();
      clone.play().catch(() => {});
    }
  }

  // Create a photo element
  function createPhotoElement(imagePath, index) {
    const photoDiv = document.createElement("div");
    photoDiv.className = "photo";

    const frameDiv = document.createElement("div");
    frameDiv.className = "photo-frame";

    const img = document.createElement("img");
    img.src = imagePath;
    img.alt = "Photo " + (index + 1);
    img.loading = "lazy";

    frameDiv.appendChild(img);
    photoDiv.appendChild(frameDiv);

    // Overlay heart icon
    const overlayDiv = document.createElement("div");
    overlayDiv.className = "photo-overlay";
    const heartIcon = document.createElement("i");
    heartIcon.className = "fas fa-heart";
    overlayDiv.appendChild(heartIcon);
    photoDiv.appendChild(overlayDiv);

    return photoDiv;
  }

  // Show gallery page
  function showPage(page) {
    gallery.innerHTML = "";
    const start = (page - 1) * imagesPerPage;
    const end = Math.min(start + imagesPerPage, images.length);

    for (let i = start; i < end; i++) {
      gallery.appendChild(createPhotoElement(images[i], i));
    }

    renderPagination(page);
  }

  // Render pagination buttons
  function renderPagination(currentPage) {
    pagination.innerHTML = "";
    const totalPages = Math.ceil(images.length / imagesPerPage);

    for (let i = 1; i <= totalPages; i++) {
      const btn = document.createElement("button");
      btn.textContent = i;
      if (i === currentPage) btn.classList.add("active");
      btn.addEventListener("click", () => {
        playClickSound();
        showPage(i);
      });
      pagination.appendChild(btn);
    }
  }

  // Open lightbox on image click using event delegation
  gallery.addEventListener("click", (e) => {
    const img = e.target.tagName === "IMG" ? e.target : e.target.closest("img");
    if (!img) return;

    playClickSound();
    lightboxImg.src = img.src;
    lightbox.classList.add("open");
  });

  // Close lightbox by clicking outside the image
  lightbox.addEventListener("click", (e) => {
    if (e.target === lightbox || e.target === lightboxImg) {
      lightbox.classList.remove("open");
      lightboxImg.src = "";
    }
  });

  // Close lightbox on ESC key
  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape" && lightbox.classList.contains("open")) {
      lightbox.classList.remove("open");
      lightboxImg.src = "";
    }
  });

  // Initialize gallery
  showPage(1);
});
