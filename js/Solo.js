// solo.js (drop this in /js/ and make sure your page loads the exact filename)
document.addEventListener("DOMContentLoaded", () => {
  // Configuration
  const imagesPerPage = 20;
  const gallery = document.getElementById("gallery");
  const pagination = document.getElementById("pagination");

  // Debug early exit
  if (!gallery || !pagination) {
    console.error("solo.js: #gallery or #pagination not found in DOM.");
    return;
  }

  // Sound support (optional)
  const clickSound = document.getElementById("clickSound"); // ensure your audio element uses this id if you want sound

  function playClickSound() {
    try {
      if (clickSound && localStorage.getItem("soundEnabled") === "true") {
        // clone node so overlapping clicks still play
        const clone = clickSound.cloneNode();
        clone.play().catch(() => {});
      }
    } catch (err) {
      // ignore autoplay rejections
    }
  }

  // Lightbox support
  const lightbox = document.getElementById("lightbox");
  const lightboxImg = document.getElementById("lightbox-img");
  if (!lightbox || !lightboxImg) {
    console.warn("solo.js: lightbox elements not found. Lightbox will be disabled.");
  }

  // images variable comes from PHP: const images = <?php echo json_encode($files); ?>;
  if (typeof images === "undefined" || !Array.isArray(images)) {
    console.error("solo.js: 'images' array is not defined. Make sure PHP passes it correctly.");
    return;
  }

  // Create photo element (we set src immediately for page-based lazy loading)
  function createPhotoElement(imagePath, index) {
    const photoDiv = document.createElement("div");
    photoDiv.className = "photo";

    const frameDiv = document.createElement("div");
    frameDiv.className = "photo-frame";

    const img = document.createElement("img");
    img.src = imagePath;           // set src now (we're loading per-page only)
    img.alt = "Photo " + (index + 1);
    img.loading = "lazy";
    img.className = "gallery-img";

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
    gallery.innerHTML = "";
    const start = (page - 1) * imagesPerPage;
    const end = Math.min(start + imagesPerPage, images.length);

    for (let i = start; i < end; i++) {
      const photo = createPhotoElement(images[i], i);
      gallery.appendChild(photo);
    }
    renderPagination(page);
  }

  function renderPagination(currentPage) {
    pagination.innerHTML = "";
    const totalPages = Math.ceil(images.length / imagesPerPage) || 1;

    for (let i = 1; i <= totalPages; i++) {
      const btn = document.createElement("button");
      btn.textContent = i;
      btn.className = (i === currentPage) ? "active" : "";
      btn.addEventListener("click", () => {
        playClickSound();
        showPage(i);
      });
      pagination.appendChild(btn);
    }
  }

  // Event delegation: single listener on gallery to open lightbox when any image is clicked
  gallery.addEventListener("click", (e) => {
    const img = e.target.closest("img");
    if (!img) return; // clicked something else

    playClickSound();

    // If lightbox exists, open it using the image src
    if (lightbox && lightboxImg) {
      const src = img.getAttribute("src") || img.dataset.src;
      if (src) {
        lightboxImg.src = src;
        lightbox.classList.add("open"); // CSS should show when .open
      } else {
        console.warn("solo.js: clicked image has no src.");
      }
    }
  });

  // Close lightbox by clicking outside the img or pressing ESC
  if (lightbox && lightboxImg) {
    lightbox.addEventListener("click", (e) => {
      if (e.target !== lightboxImg) {
        lightbox.classList.remove("open");
        lightboxImg.src = "";
      }
    });

    document.addEventListener("keydown", (e) => {
      if (e.key === "Escape" && lightbox.classList.contains("open")) {
        lightbox.classList.remove("open");
        lightboxImg.src = "";
      }
    });
  }

  // Initialize
  showPage(1);
  console.log("solo.js: initialized —", images.length, "images, showing page 1.");
});
