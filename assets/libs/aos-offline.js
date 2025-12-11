// Minimal AOS fallback for offline use
// Provides basic initialization without animation features
window.AOS = {
    init: function(options) {
        // Add aos-init and aos-animate classes to all [data-aos] elements
        document.querySelectorAll('[data-aos]').forEach(element => {
            element.classList.add('aos-init', 'aos-animate');
        });
        return this;
    },
    refresh: function() {
        return this;
    },
    refreshHard: function() {
        return this;
    }
};
