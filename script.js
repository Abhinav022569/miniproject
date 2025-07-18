  const dropdown = document.querySelector('.login-dropdown');
  let timeout;

  dropdown.addEventListener('mouseenter', () => {
    clearTimeout(timeout);
    dropdown.querySelector('.dropdown-content').style.display = 'block';
  });

  dropdown.addEventListener('mouseleave', () => {
    timeout = setTimeout(() => {
      dropdown.querySelector('.dropdown-content').style.display = 'none';
    }, 300); // 300ms delay before hiding
    });

const fadeSections = document.querySelectorAll('.fade-in-section');

const observer = new IntersectionObserver((entries, observer) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('visible');
        /*observer.unobserve(entry.target); // Animate once*/
      }
      else {
        entry.target.classList.remove('visible'); // 👈 hides it again when out of view
      }
    });
  }, { threshold: 0.7 });

  fadeSections.forEach(section => {
    observer.observe(section);
  });