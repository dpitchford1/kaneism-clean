document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.swiper').forEach(function (el) {
        new Swiper(el, {
            loop: true,
            //pagination: { el: el.querySelector('.swiper-pagination'), clickable: true },
            keyboard: {
                enabled: true,
              },
              freeMode: true,
              scrollbar: {
                el: ".swiper-scrollbar",
                hide: true,
              },
              spaceBetween: 10,
            navigation: {
                nextEl: el.querySelector('.swiper-button-next'),
                prevEl: el.querySelector('.swiper-button-prev')
            }
        });
    });
});