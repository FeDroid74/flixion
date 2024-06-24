const menuBtn = document.querySelector('.menu-burger');
const menu = document.querySelector('.menu-mobile');

menuBtn.addEventListener('click', () => {
    menu.classList.toggle('show');
    menuBtn.classList.toggle('active');
});