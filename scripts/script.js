// Hide boot screen after 2 seconds + play sound
window.addEventListener('load', () => {
  const bootScreen = document.getElementById('boot-screen');
  const bootSound = document.getElementById('boot-sound');
  if(bootSound) {
    bootSound.play().catch(() => {}); 
  }

  setTimeout(() => {
    bootScreen.style.display = 'none';
    showWelcomeBanner();
  }, 2000);
});

// Show and close welcome banner
function showWelcomeBanner() {
  const banner = document.getElementById('welcome-banner');
  if (!banner) return;
  banner.style.display = 'block';
}
function closeBanner() {
  const banner = document.getElementById('welcome-banner');
  if (!banner) return;
  banner.style.display = 'none';
}

const startButton = document.getElementById("start-button");
const startMenu = document.getElementById("start-menu");

startButton.addEventListener("click", () => {
  startMenu.classList.toggle("active");
});

// Click outside closes start menu
document.addEventListener("click", (e) => {
  if (!startMenu.contains(e.target) && !startButton.contains(e.target)) {
    startMenu.classList.remove("active");
  }
});


// Open windows via desktop icons
document.querySelectorAll('.icon').forEach(icon => {
  icon.addEventListener('click', () => {
    const winId = icon.getAttribute('data-window');
    openWindow(winId);
  });
});

// Open window via start menu
document.querySelectorAll('#start-menu li').forEach(item => {
  item.addEventListener('click', () => {
    const winId = item.getAttribute('data-window');
    if (winId) {
      openWindow(winId);
      startMenu.classList.remove('active');
    }
  });
});

// Window functions
function openWindow(id) {
  const win = document.getElementById(id);
  if (!win) return;
  win.classList.add('active');
  win.style.zIndex = getHighestZIndex() + 1;

  adjustWindowPosition(win);
}

function closeWindow(id) {
  const win = document.getElementById(id);
  if (!win) return;
  win.classList.remove('active');
}

function getHighestZIndex() {
  const windows = document.querySelectorAll('.window');
  let highest = 0;
  windows.forEach(win => {
    const z = parseInt(window.getComputedStyle(win).zIndex) || 0;
    if (z > highest) highest = z;
  });
  return highest;
}

// Keep window inside viewport bounds
function adjustWindowPosition(windowElement) {
  const maxTop = window.innerHeight - windowElement.offsetHeight - 50;
  const maxLeft = window.innerWidth - windowElement.offsetWidth - 50;

  let top = parseInt(windowElement.style.top, 10);
  let left = parseInt(windowElement.style.left, 10);

  if (isNaN(top)) top = 50;
  if (isNaN(left)) left = 50;

  if (top < 0) top = 0;
  if (left < 0) left = 0;
  if (top > maxTop) top = maxTop;
  if (left > maxLeft) left = maxLeft;

  windowElement.style.top = `${top}px`;
  windowElement.style.left = `${left}px`;
}

// Dragging windows
let isDragging = false;
let offsetX = 0, offsetY = 0, draggedWindow = null;

document.querySelectorAll('.title-bar').forEach(bar => {
  bar.addEventListener('mousedown', e => {
    draggedWindow = e.target.closest('.window');
    if (!draggedWindow) return;

    isDragging = true;
    offsetX = e.clientX - draggedWindow.getBoundingClientRect().left;
    offsetY = e.clientY - draggedWindow.getBoundingClientRect().top;

    draggedWindow.style.transition = 'none';
    draggedWindow.style.zIndex = getHighestZIndex() + 1;

    const onMouseMove = (event) => {
      if (!isDragging) return;

      const newLeft = event.clientX - offsetX;
      const newTop = event.clientY - offsetY;

      draggedWindow.style.left = `${newLeft}px`;
      draggedWindow.style.top = `${newTop}px`;
    };

    const onMouseUp = () => {
      isDragging = false;

      draggedWindow.style.transition = 'top 0.2s ease-in-out, left 0.2s ease-in-out';
      adjustWindowPosition(draggedWindow);

      document.removeEventListener('mousemove', onMouseMove);
      document.removeEventListener('mouseup', onMouseUp);
    };

    document.addEventListener('mousemove', onMouseMove);
    document.addEventListener('mouseup', onMouseUp, { once: true });

    e.preventDefault();
  });
});

// Time function
function updateClock() {
  const clock = document.getElementById('clock');
  const now = new Date();

  let hours = now.getHours();
  const minutes = now.getMinutes();
  const ampm = hours >= 12 ? 'PM' : 'AM';

  hours = hours % 12;
  hours = hours ? hours : 12;

  const timeString = `${hours}:${minutes.toString().padStart(2, '0')} ${ampm}`;
  clock.textContent = timeString;
}

setInterval(updateClock, 1000);
updateClock();
