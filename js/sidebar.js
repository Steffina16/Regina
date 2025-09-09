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
    let num = parseInt(col,16);
    let r = (num >> 16) + amt;
    let g = ((num >> 8) & 0x00FF) + amt;
    let b = (num & 0x0000FF) + amt;
    r = Math.min(255, Math.max(0,r));
    g = Math.min(255, Math.max(0,g));
    b = Math.min(255, Math.max(0,b));
    return (usePound?"#":"") + ((r<<16) | (g<<8) | b).toString(16).padStart(6,'0');
  }

  function applyThemeColor(color){
    document.documentElement.style.setProperty('--accent-color', color);

    // Light/dark variants for gradient
    const start = lightenDarkenColor(color, 60); // lighter tint
    const end = lightenDarkenColor(color, -10);  // slightly darker
    document.documentElement.style.setProperty('--bg-color-start', start);
    document.documentElement.style.setProperty('--bg-color-end', end);

    // Optional: set sidebar light for light mode, dark for dark mode
    if(document.body.classList.contains("dark")){
      document.documentElement.style.setProperty('--sidebar-bg', 'rgba(50, 30, 50, 0.85)');
    } else {
      document.documentElement.style.setProperty('--sidebar-bg', '#fff');
    }
  }

});
