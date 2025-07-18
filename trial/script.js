window.addEventListener('DOMContentLoaded', () => {
  const sections = document.querySelectorAll('.fade-in-section');

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      const content = entry.target.querySelector('.section-content');
      if (entry.isIntersecting && content) {
        content.classList.add('visible');
      } else if (content) {
        content.classList.remove('visible');
      }
    });
  }, {
    threshold: 0.6
  });

  sections.forEach(section => observer.observe(section));
});
