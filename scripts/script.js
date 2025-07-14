function closeBanner() {
  const banner = document.getElementById('welcome-banner');
  banner.style.display = 'none';  
  localStorage.setItem('hasVisited', 'true'); 
}

if (!localStorage.getItem('hasVisited')) {
  const banner = document.getElementById('welcome-banner');
  banner.style.display = 'block';  
}

window.onload = function() {
  const hash = window.location.hash.substring(1);
  if (hash) {
      openWindow(hash);
  }

  document.querySelectorAll('.icon').forEach(icon => {
      icon.addEventListener('click', () => {
          const windowId = icon.getAttribute('data-window');
          openWindow(windowId);
      });
  });
};

function openWindow(id) {
  const windowElement = document.getElementById(id);
  windowElement.classList.add('active');
  windowElement.style.zIndex = getHighestZIndex() + 1;

  adjustWindowPosition(windowElement);
  window.location.hash = id;
}

function closeWindow(id) {
  document.getElementById(id).classList.remove('active');
  window.location.hash = '';
}

function getHighestZIndex() {
  const elements = document.querySelectorAll('.window');
  let highest = 0;
  elements.forEach(el => {
      const zIndex = parseInt(window.getComputedStyle(el).zIndex, 10);
      if (!isNaN(zIndex) && zIndex > highest) {
          highest = zIndex;
      }
  });
  return highest;
}

function adjustWindowPosition(windowElement) {
  const maxTop = window.innerHeight - windowElement.offsetHeight - 50;
  const maxLeft = window.innerWidth - windowElement.offsetWidth - 50;

  let top = parseInt(windowElement.style.top, 10);
  let left = parseInt(windowElement.style.left, 10);

  if (top < 0) top = 0;
  if (left < 0) left = 0;
  if (top > maxTop) top = maxTop;
  if (left > maxLeft) left = maxLeft;

  windowElement.style.top = `${top}px`;
  windowElement.style.left = `${left}px`;
}

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
