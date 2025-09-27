// solo_merged.js
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

  // Show notification
  function showNotification(message, isError = false) {
    const notification = document.getElementById('notification');
    notification.innerHTML = '';

    const notificationEl = document.createElement('div');
    notificationEl.className = `favorite-notification ${isError ? 'error' : ''}`;
    notificationEl.textContent = message;
    notification.appendChild(notificationEl);

    setTimeout(() => notificationEl.classList.add('show'), 100);
    setTimeout(() => {
      notificationEl.classList.remove('show');
      setTimeout(() => notificationEl.remove(), 300);
    }, 3000);
  }

  // Toggle favorite via API
  async function toggleFavorite(heartBtn) {
    const imagePath = heartBtn.getAttribute('data-image-path');
    const albumName = heartBtn.getAttribute('data-album-name');

    try {
      const response = await fetch('../api/favorite_handler.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=toggle_favorite&image_path=${encodeURIComponent(imagePath)}&album_name=${encodeURIComponent(albumName)}`
      });
      const data = await response.json();

      if (data.success) {
        if (data.is_favorite) {
          heartBtn.classList.add('active');
          heartBtn.classList.remove('inactive');
          heartBtn.innerHTML = '❤️';
          showNotification('Added to favorites!');
        } else {
          heartBtn.classList.remove('active');
          heartBtn.classList.add('inactive');
          heartBtn.innerHTML = '🤍';
          showNotification('Removed from favorites!');
        }
      } else {
        showNotification('Failed to update favorite', true);
      }
    } catch (error) {
      console.error(error);
      showNotification('Error updating favorite', true);
    }
  }

  // Check if image is already favorited
  async function checkFavoriteStatus(heartBtn) {
    const imagePath = heartBtn.getAttribute('data-image-path');
    try {
      const response = await fetch('../api/favorite_handler.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=check_favorite&image_path=${encodeURIComponent(imagePath)}`
      });
      const data = await response.json();
      if (data.is_favorite) {
        heartBtn.classList.add('active');
        heartBtn.classList.remove('inactive');
        heartBtn.innerHTML = '❤️';
      } else {
        heartBtn.classList.remove('active');
        heartBtn.classList.add('inactive');
        heartBtn.innerHTML = '🤍';
      }
    } catch (error) {
      console.error(error);
    }
  }

  // Create a photo element with optional heart
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

    if (isLoggedIn) {
      const heartBtn = document.createElement("button");
      heartBtn.className = "favorite-heart inactive";
      heartBtn.setAttribute("data-image-path", imagePath);
      heartBtn.setAttribute("data-album-name", currentAlbum);
      heartBtn.innerHTML = "🤍";

      // Attach click listener immediately
      heartBtn.addEventListener('click', e => {
        e.stopPropagation(); // prevent lightbox
        toggleFavorite(heartBtn);
      });

      // Check existing favorite
      checkFavoriteStatus(heartBtn);

      photoDiv.appendChild(heartBtn);
    }

    return photoDiv;
  }

  // Render gallery page
  function showPage(page) {
    gallery.innerHTML = "";
    const start = (page - 1) * imagesPerPage;
    const end = Math.min(start + imagesPerPage, images.length);

    for (let i = start; i < end; i++) {
      gallery.appendChild(createPhotoElement(images[i], i));
    }

    renderPagination(page);
  }

  // Pagination buttons
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

  // Lightbox click
  gallery.addEventListener("click", (e) => {
    if (e.target.classList.contains("favorite-heart")) return;

    const img = e.target.tagName === "IMG" ? e.target : e.target.closest("img");
    if (!img) return;

    playClickSound();
    lightboxImg.src = img.src;
    lightbox.classList.add("open");
  });

  // Close lightbox
  lightbox.addEventListener("click", (e) => {
    if (e.target === lightbox || e.target === lightboxImg) {
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

  // Initialize first page
  showPage(1);
});
