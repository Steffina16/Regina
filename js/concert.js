document.addEventListener("DOMContentLoaded", () => {
  const slideshow = document.getElementById("slideshow");

  // ✅ Set how many images you actually have in albums/concert
  const totalImages = 22; 
  const folderPath = "../albums/concert/";
  const prefix = "concert-";
  const extension = ".jpg";

  // Generate all file names
  const images = [];
  for (let i = 1; i <= totalImages; i++) {
    const num = String(i).padStart(3, "0"); // makes 001, 002, 003...
    images.push(`${folderPath}${prefix}${num}${extension}`);
  }

  let currentIndex = 0;

  // Create and show the first image
  const img = document.createElement("img");
  img.src = images[currentIndex];
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
});
