(function($) {
    'use strict';

    $(document).ready(function() {
        // Lazy loading de imágenes
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const image = entry.target;
                        image.src = image.dataset.src;
                        image.classList.remove('rcn-lazy');
                        observer.unobserve(image);
                    }
                });
            });

            $('.rcn-category-item img').each(function() {
                $(this).addClass('rcn-lazy');
                $(this).attr('data-src', $(this).attr('src'));
                $(this).attr('src', 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');
                imageObserver.observe(this);
            });
        }

        // Animación suave al hacer clic en los enlaces de categoría
        $('.rcn-category-item a').on('click', function(e) {
            e.preventDefault();
            const href = $(this).attr('href');
            console.log('Link clicked, navigating to:', href);
            
            // Instead of trying to scroll, let's navigate to the link directly
            if (href) {
                window.location.href = href;
            }
        });
    });
})(jQuery);

