import './bootstrap';

// Lazy loading dla komponentów Filament
document.addEventListener('DOMContentLoaded', function() {
    // Lazy load dla ciężkich komponentów
    const lazyComponents = document.querySelectorAll('[data-lazy-component]');
    
    if (lazyComponents.length > 0) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const component = entry.target;
                    const componentName = component.dataset.lazyComponent;
                    
                    // Dynamicznie załaduj komponent
                    import(`./components/${componentName}.js`)
                        .then(module => {
                            if (module.default) {
                                module.default(component);
                            }
                        })
                        .catch(error => {
                            console.warn(`Failed to load component: ${componentName}`, error);
                        });
                    
                    observer.unobserve(component);
                }
            });
        }, {
            rootMargin: '50px' // Załaduj 50px przed wejściem w viewport
        });
        
        lazyComponents.forEach(component => {
            observer.observe(component);
        });
    }
});

// Optymalizacja dla Alpine.js
document.addEventListener('alpine:init', () => {
    // Lazy loading dla Alpine.js komponentów
    Alpine.directive('lazy', (el, { expression }, { evaluateLater, effect }) => {
        const evaluate = evaluateLater(expression);
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    evaluate(() => {
                        // Komponent jest widoczny, można go załadować
                    });
                    observer.unobserve(el);
                }
            });
        });
        
        observer.observe(el);
    });
});
