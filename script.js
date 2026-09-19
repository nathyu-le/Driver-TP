const observer = new IntersectionObserver(
  (entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        entry.target.classList.add('visible');
      }
    });
  },
  { threshold: 0.12 }
);

document.querySelectorAll('.reveal').forEach((element) => observer.observe(element));

const toggleButtons = document.querySelectorAll('.toggle');
toggleButtons.forEach((button) => {
  button.addEventListener('click', () => {
    toggleButtons.forEach((item) => item.classList.remove('active'));
    button.classList.add('active');
  });
});

const swapBtn = document.querySelector('.swap-btn');
const inputs = document.querySelectorAll('.input-field input');

if (swapBtn && inputs.length >= 2) {
  swapBtn.addEventListener('click', () => {
    const temp = inputs[0].value;
    inputs[0].value = inputs[1].value;
    inputs[1].value = temp;
  });
}

const modal = document.getElementById('authModal');
const authTitle = document.getElementById('authTitle');
const authTabs = document.querySelectorAll('.tab');
const signupFields = document.querySelectorAll('.signup-only');
const authTriggerButtons = document.querySelectorAll('.sign-in, .ghost-search, .primary-btn, .cta-pill');
const closeModalButton = document.querySelector('.close-modal');

const setMode = (mode) => {
  const isSignup = mode === 'signup';
  authTitle.textContent = isSignup ? 'Create account' : 'Sign in';
  authTabs.forEach((tab) => tab.classList.toggle('active', tab.dataset.mode === mode));
  signupFields.forEach((field) => field.classList.toggle('hidden', !isSignup));
};

authTriggerButtons.forEach((button) => {
  button.addEventListener('click', () => {
    const mode = button.classList.contains('sign-in') ? 'login' : 'signup';
    setMode(mode);
    modal.classList.add('open');
    modal.setAttribute('aria-hidden', 'false');
  });
});

authTabs.forEach((tab) => {
  tab.addEventListener('click', () => setMode(tab.dataset.mode));
});

closeModalButton.addEventListener('click', () => {
  modal.classList.remove('open');
  modal.setAttribute('aria-hidden', 'true');
});

modal.addEventListener('click', (event) => {
  if (event.target === modal) {
    modal.classList.remove('open');
    modal.setAttribute('aria-hidden', 'true');
  }
});
