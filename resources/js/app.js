/**
 * FluxStack Frontend Scripts
 */

// Mobile Navigation
const mobileNav = document.getElementById('mobile-nav');
const openBtns = document.querySelectorAll('[data-open-nav]');
const closeBtns = document.querySelectorAll('[data-close-nav]');

function openNav() {
    if (!mobileNav) return;
    mobileNav.classList.add('is-open');
    mobileNav.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
}

function closeNav() {
    if (!mobileNav) return;
    mobileNav.classList.remove('is-open');
    mobileNav.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
}

openBtns.forEach(btn => btn.addEventListener('click', openNav));
closeBtns.forEach(btn => btn.addEventListener('click', closeNav));

// Close on Escape key
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeNav();
});
