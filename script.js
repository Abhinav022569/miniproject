window.addEventListener('DOMContentLoaded', () => {
  // VANTA.NET Background
  VANTA.NET({
    el: "#vanta-bg",
    mouseControls: true,
    touchControls: true,
    gyroControls: false,
    minHeight: 200.00,
    minWidth: 200.00,
    scale: 1.00,
    scaleMobile: 1.00,
    color: 0x00ffd5,
    backgroundColor: 0x0
  });

  // Login dropdown hover
  const dropdown = document.querySelector('.login-dropdown');
  let timeout;

  dropdown.addEventListener('mouseenter', () => {
    clearTimeout(timeout);
    dropdown.querySelector('.dropdown-content').style.display = 'block';
  });

  dropdown.addEventListener('mouseleave', () => {
    timeout = setTimeout(() => {
      dropdown.querySelector('.dropdown-content').style.display = 'none';
    }, 400);
  });

  // Section fade-in animation
  const fadeSections = document.querySelectorAll('.fade-in-section');

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      const content = entry.target.querySelector('.section-content');
      if (entry.isIntersecting && content) {
        content.classList.add('visible');
      } else if (content) {
        content.classList.remove('visible');
      }
    });
  }, { threshold: 0.6 });

  fadeSections.forEach(section => observer.observe(section));
});
