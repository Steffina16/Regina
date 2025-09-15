document.addEventListener("DOMContentLoaded", () => {
  // === Hamburger ===
  const menuBtn = document.getElementById("menuBtn");
  const sidebar = document.getElementById("sidebar");
  if (menuBtn && sidebar) {
    menuBtn.addEventListener("click", () => {
      menuBtn.classList.toggle("open");
      sidebar.classList.toggle("open");
    });
  }

  // === Dark Mode Toggle ===
  const darkLink = document.getElementById("toggle-dark");
  const darkIcon = document.getElementById("darkIcon");
  const darkStatus = document.getElementById("darkStatus");

  function applyTheme(theme) {
    const isDark = theme === "dark";
    document.body.classList.toggle("dark", isDark);
    if (isDark) {
      darkIcon.classList.replace("fa-sun", "fa-moon");
      darkStatus.textContent = "ON";
    } else {
      darkIcon.classList.replace("fa-moon", "fa-sun");
      darkStatus.textContent = "OFF";
    }
  }

  const savedTheme = localStorage.getItem("theme") || "light";
  applyTheme(savedTheme);

  if (darkLink) {
    darkLink.addEventListener("click", (e) => {
      e.preventDefault();
      const next = document.body.classList.contains("dark") ? "light" : "dark";
      applyTheme(next);
      localStorage.setItem("theme", next);
    });
  }

  // === Theme Color Modal ===
  const themeTrigger = document.getElementById("theme-trigger");
  const themeModal = document.getElementById("themeModal");
  const colorCircles = document.querySelectorAll(".color-circle");

  themeTrigger.addEventListener("click", (e) => {
    e.preventDefault();
    themeModal.style.display = "flex";
  });

  themeModal.addEventListener("click", (e) => {
    if (e.target === themeModal) themeModal.style.display = "none";
  });

  // Apply saved color
  const savedColor = localStorage.getItem("accentColor") || "#ffb6c1";
  applyThemeColor(savedColor);

  colorCircles.forEach(circle => {
    circle.addEventListener("click", () => {
      const color = circle.getAttribute("data-color");
      applyThemeColor(color);
      localStorage.setItem("accentColor", color);
      themeModal.style.display = "none";
    });
  });

  // === Gradient generator ===
  function lightenDarkenColor(col, amt) {
    let usePound = false;
    if (col[0] === "#") {
      col = col.slice(1);
      usePound = true;
    }
    let num = parseInt(col, 16);
    let r = (num >> 16) + amt;
    let g = ((num >> 8) & 0x00FF) + amt;
    let b = (num & 0x0000FF) + amt;
    r = Math.min(255, Math.max(0, r));
    g = Math.min(255, Math.max(0, g));
    b = Math.min(255, Math.max(0, b));
    return (usePound ? "#" : "") + ((r << 16) | (g << 8) | b).toString(16).padStart(6, '0');
  }

  function applyThemeColor(color) {
    document.documentElement.style.setProperty('--accent-color', color);

    const start = lightenDarkenColor(color, 60);
    const end = lightenDarkenColor(color, -10);
    document.documentElement.style.setProperty('--bg-color-start', start);
    document.documentElement.style.setProperty('--bg-color-end', end);

    if (document.body.classList.contains("dark")) {
      document.documentElement.style.setProperty('--sidebar-bg', 'rgba(50, 30, 50, 0.85)');
    } else {
      document.documentElement.style.setProperty('--sidebar-bg', '#fff');
    }
  }

  // === Click Sound Toggle ===
  const soundLink = document.getElementById("toggle-sound");
  const soundStatus = document.getElementById("soundStatus");
  const clickSound = document.getElementById("click-sound");

  function updateSoundStatus(enabled) {
    if (enabled) {
      soundIcon.classList.replace("fa-volume-mute", "fa-volume-up");
      soundStatus.textContent = "ON";
    } else {
      soundIcon.classList.replace("fa-volume-up", "fa-volume-mute");
      soundStatus.textContent = "OFF";
    }
  }

  // Load saved preference
  function isSoundEnabled() {
    return localStorage.getItem("soundEnabled") === "true";
  }

  updateSoundStatus(isSoundEnabled());

  // Toggle when clicking sidebar link
  if (soundLink) {
    soundLink.addEventListener("click", (e) => {
      e.preventDefault();
      const nextState = !isSoundEnabled();
      localStorage.setItem("soundEnabled", nextState);
      updateSoundStatus(nextState);
    });
  }

// Play sound on *every* click when enabled
document.addEventListener("click", () => {
  if (!isSoundEnabled() || !clickSound) return;

  // Clone the audio node so overlapping clicks still play
  const clone = clickSound.cloneNode();
  clone.play().catch(() => {});

});

// === Background Music Toggle ===
const musicLink = document.getElementById("toggle-music");
const musicIcon = document.getElementById("musicIcon");
const musicStatus = document.getElementById("musicStatus");
const bgMusic = document.getElementById("bg-music");

// Always start OFF
localStorage.setItem("musicEnabled", "false");

function isMusicEnabled() {
  return localStorage.getItem("musicEnabled") === "true";
}

function updateMusicUI(enabled) {
  if (enabled) {
    musicIcon.classList.replace("fa-music", "fa-play-circle"); // change icon
    musicStatus.textContent = "ON";
    bgMusic.play().catch(() => {});
  } else {
    musicIcon.classList.replace("fa-play-circle", "fa-music"); // back to normal
    musicStatus.textContent = "OFF";
    bgMusic.pause();
    bgMusic.currentTime = 0; // reset song
  }
}

// Initialize UI to OFF
updateMusicUI(false);

// Toggle when clicking sidebar link
if (musicLink) {
  musicLink.addEventListener("click", (e) => {
    e.preventDefault();
    const nextState = !isMusicEnabled();
    localStorage.setItem("musicEnabled", nextState);
    updateMusicUI(nextState);
  });
}

});
