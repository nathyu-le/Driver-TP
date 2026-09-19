document.addEventListener('DOMContentLoaded', function () {
  const revealItems = document.querySelectorAll('.reveal');
  const revealObserver = new IntersectionObserver(
    function (entries) {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add('visible');
        }
      });
    },
    { threshold: 0.18 }
  );

  revealItems.forEach((item) => revealObserver.observe(item));

  const toggles = document.querySelectorAll('.toggle');
  toggles.forEach((button) => {
    button.addEventListener('click', () => {
      toggles.forEach((item) => item.classList.remove('active'));
      button.classList.add('active');
    });
  });

  const swapButton = document.querySelector('.swap-btn');
  if (swapButton) {
    swapButton.addEventListener('click', () => {
      const pickup = document.querySelector('input[name="pickup"]');
      const destination = document.querySelector('input[name="destination"]');
      if (!pickup || !destination) return;
      const temp = pickup.value;
      pickup.value = destination.value;
      destination.value = temp;
    });
  }
});
