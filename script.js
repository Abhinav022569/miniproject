document.addEventListener('DOMContentLoaded', () => {
  // Login Dropdown
  const dropdown = document.querySelector('.login-dropdown');
  let timeout;

  dropdown.addEventListener('mouseenter', () => {
    clearTimeout(timeout);
    dropdown.querySelector('.dropdown-content').style.display = 'block';
  });

  dropdown.addEventListener('mouseleave', () => {
    timeout = setTimeout(() => {
      dropdown.querySelector('.dropdown-content').style.display = 'none';
    }, 300);
  });

  // Text animation
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
