document.addEventListener('DOMContentLoaded', () => {
    const burger = document.getElementById('burgerBtn');
    const nav = document.getElementById('mainNav');

    if (burger && nav) {
        burger.addEventListener('click', () => {
            burger.classList.toggle('active');
            nav.classList.toggle('active');
        });

        // Закрывать меню при клике на ссылку
        nav.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', () => {
                burger.classList.remove('active');
                nav.classList.remove('active');
            });
        });

        // Закрывать при клике вне меню
        document.addEventListener('click', (e) => {
            if (!nav.contains(e.target) && !burger.contains(e.target)) {
                burger.classList.remove('active');
                nav.classList.remove('active');
            }
        });
    }
});