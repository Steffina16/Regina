document.addEventListener("DOMContentLoaded", () => {
  const slideshow = document.getElementById("slideshow");

  // ===== SLIDESHOW IMAGES =====
  const totalImages = 22; 
  const folderPath = "../albums/concert/";
  const prefix = "concert-";
  const extension = ".jpg";

  const images = [];
  for (let i = 1; i <= totalImages; i++) {
    const num = String(i).padStart(3, "0");
    images.push(`${folderPath}${prefix}${num}${extension}`);
  }

  let currentIndex = 0;
  const img = document.createElement("img");
  img.src = images[currentIndex];
  img.className = "slideshow-img";
  slideshow.appendChild(img);

  // Buttons
  document.getElementById("prev").addEventListener("click", () => {
    currentIndex = (currentIndex - 1 + images.length) % images.length;
    img.src = images[currentIndex];
  });

  document.getElementById("next").addEventListener("click", () => {
    currentIndex = (currentIndex + 1) % images.length;
    img.src = images[currentIndex];
  });

  // ===== LIGHTBOX SETUP =====
  const lightbox = document.createElement("div");
  lightbox.id = "concert-lightbox";
  lightbox.style.display = "none";
  lightbox.style.position = "fixed";
  lightbox.style.inset = "0";
  lightbox.style.background = "rgba(0,0,0,0.85)";
  lightbox.style.backdropFilter = "blur(4px)";
  lightbox.style.justifyContent = "center";
  lightbox.style.alignItems = "center";
  lightbox.style.zIndex = "9999";
  lightbox.style.cursor = "zoom-out";

  const lightboxImg = document.createElement("img");
  lightboxImg.id = "concert-lightbox-img";
  lightboxImg.style.maxWidth = "92%";
  lightboxImg.style.maxHeight = "92%";
  lightboxImg.style.borderRadius = "10px";
  lightboxImg.style.boxShadow = "0 12px 40px rgba(0,0,0,0.6)";
  lightbox.appendChild(lightboxImg);

  document.body.appendChild(lightbox);

  // Open lightbox for static images
  document.querySelectorAll(".scrapbook img").forEach(el => {
    el.addEventListener("click", () => {
      lightboxImg.src = el.src;
      lightbox.style.display = "flex";
    });
  });

  // Open lightbox for slideshow image
  img.addEventListener("click", () => {
    lightboxImg.src = img.src;
    lightbox.style.display = "flex";
  });

  // Close lightbox
  lightbox.addEventListener("click", e => {
    if (e.target !== lightboxImg) {
      lightbox.style.display = "none";
      lightboxImg.src = "";
    }
  });

  document.addEventListener("keydown", e => {
    if (e.key === "Escape" && lightbox.style.display === "flex") {
      lightbox.style.display = "none";
      lightboxImg.src = "";
    }
  });
});
