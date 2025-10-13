
const header = document.getElementById('header');
const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
const navMenu = document.querySelector('.nav-menu');
const navLinks = document.querySelectorAll('.nav-link');
const contactForm = document.getElementById('contactForm');

mobileMenuToggle.addEventListener('click', () => {
    mobileMenuToggle.classList.toggle('active');
    navMenu.classList.toggle('active');
    document.body.style.overflow = navMenu.classList.contains('active') ? 'hidden' : 'auto';
});
navLinks.forEach(link => {
    link.addEventListener('click', () => {
        mobileMenuToggle.classList.remove('active');
        navMenu.classList.remove('active');
        document.body.style.overflow = 'auto';
    });
});
document.addEventListener('click', (e) => {
    if (!e.target.closest('.navbar') && navMenu.classList.contains('active')) {
        mobileMenuToggle.classList.remove('active');
        navMenu.classList.remove('active');
        document.body.style.overflow = 'auto';
    }
});
window.addEventListener('scroll', () => {
    if (window.scrollY > 100) {
        header.classList.add('scrolled');
    } else {
        header.classList.remove('scrolled');
    }
    
    
    updateActiveNavLink();
    
    
    toggleScrollToTopButton();
});
navLinks.forEach(link => {
    link.addEventListener('click', (e) => {
        e.preventDefault();
        const targetId = link.getAttribute('href');
        const targetSection = document.querySelector(targetId);
        
        if (targetSection) {
            const headerHeight = header.offsetHeight;
            const targetPosition = targetSection.offsetTop - headerHeight;
            
            window.scrollTo({
                top: targetPosition,
                behavior: 'smooth'
            });
        }
    });
});
function updateActiveNavLink() {
    const sections = document.querySelectorAll('section');
    const scrollPosition = window.scrollY + header.offsetHeight + 100;
    
    sections.forEach(section => {
        const sectionTop = section.offsetTop;
        const sectionHeight = section.offsetHeight;
        const sectionId = section.getAttribute('id');
        
        if (scrollPosition >= sectionTop && scrollPosition < sectionTop + sectionHeight) {
            navLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href') === `#${sectionId}`) {
                    link.classList.add('active');
                }
            });
        }
    });
}
const scrollToTopButton = document.createElement('button');
scrollToTopButton.classList.add('scroll-to-top');
scrollToTopButton.innerHTML = '↑';
scrollToTopButton.setAttribute('aria-label', 'Voltar ao topo');
document.body.appendChild(scrollToTopButton);


function toggleScrollToTopButton() {
    if (window.scrollY > 500) {
        scrollToTopButton.classList.add('visible');
    } else {
        scrollToTopButton.classList.remove('visible');
    }
}

scrollToTopButton.addEventListener('click', () => {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
});
contactForm.addEventListener('submit', (e) => {
    e.preventDefault();
    

    const name = document.getElementById('name').value;
    const email = document.getElementById('email').value;
    const message = document.getElementById('message').value;
    

    if (!name || !email || !message) {
        showNotification('Por favor, preencha todos os campos!', 'error');
        return;
    }
    
    if (!isValidEmail(email)) {
        showNotification('Por favor, insira um email válido!', 'error');
        return;
    }
    

    showNotification('Mensagem enviada com sucesso! Entraremos em contato em breve.', 'success');
    

    contactForm.reset();
});
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}
function showNotification(message, type = 'info') {

    const existingNotification = document.querySelector('.notification');
    if (existingNotification) {
        existingNotification.remove();
    }
    
    const notification = document.createElement('div');
    notification.classList.add('notification', `notification-${type}`);
    notification.textContent = message;
    
    notification.style.cssText = `
        position: fixed;
        top: 100px;
        right: 20px;
        padding: 20px 30px;
        background-color: ${type === 'success' ? '#466e63' : '#cc4f5d'};
        color: white;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        z-index: 10000;
        animation: slideInRight 0.5s ease, slideOutRight 0.5s ease 3.5s;
        max-width: 400px;
        font-size: 1rem;
        font-weight: 500;
    `;
    

    document.body.appendChild(notification);
    

    setTimeout(() => {
        notification.remove();
    }, 4000);
}
const notificationStyles = document.createElement('style');
notificationStyles.textContent = `
    @keyframes slideInRight {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(400px);
            opacity: 0;
        }
    }
`;
document.head.appendChild(notificationStyles);
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -100px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
        }
    });
}, observerOptions);
const animateElements = document.querySelectorAll(`
    .feature-card,
    .benefit-item,
    .step,
    .testimonial-card,
    .value-item,
    .contact-item
`);
animateElements.forEach((element, index) => {
    element.style.opacity = '0';
    element.style.transform = 'translateY(30px)';
    element.style.transition = `all 0.6s ease ${index * 0.1}s`;
    observer.observe(element);
});
function animateCounter(element, target, duration = 2000) {
    const start = 0;
    const increment = target / (duration / 16);
    let current = start;
    
    const timer = setInterval(() => {
        current += increment;
        if (current >= target) {
            element.textContent = Math.round(target);
            clearInterval(timer);
        } else {
            element.textContent = Math.round(current);
        }
    }, 16);
}
window.addEventListener('scroll', () => {
    const scrolled = window.scrollY;
    const heroImage = document.querySelector('.hero-image');
    
    if (heroImage && window.innerWidth > 768) {
        heroImage.style.transform = `translateY(${scrolled * 0.3}px)`;
    }
});
if ('loading' in HTMLImageElement.prototype) {
    const images = document.querySelectorAll('img');
    images.forEach(img => {
        img.loading = 'lazy';
    });
} else {

    const imageObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src || img.src;
                imageObserver.unobserve(img);
            }
        });
    });
    
    const images = document.querySelectorAll('img');
    images.forEach(img => imageObserver.observe(img));
}
window.addEventListener('load', () => {
    document.body.style.opacity = '1';
    document.body.style.transition = 'opacity 0.3s ease';
});
document.addEventListener('keydown', (e) => {
    // ESC fecha o menu mobile
    if (e.key === 'Escape' && navMenu.classList.contains('active')) {
        mobileMenuToggle.classList.remove('active');
        navMenu.classList.remove('active');
        document.body.style.overflow = 'auto';
    }
});
navMenu.addEventListener('keydown', (e) => {
    if (!navMenu.classList.contains('active')) return;
    
    const focusableElements = navMenu.querySelectorAll('a, button');
    const firstElement = focusableElements[0];
    const lastElement = focusableElements[focusableElements.length - 1];
    
    if (e.key === 'Tab') {
        if (e.shiftKey && document.activeElement === firstElement) {
            e.preventDefault();
            lastElement.focus();
        } else if (!e.shiftKey && document.activeElement === lastElement) {
            e.preventDefault();
            firstElement.focus();
        }
    }
});
function debounce(func, wait = 10) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}
const debouncedScroll = debounce(() => {
    updateActiveNavLink();
    toggleScrollToTopButton();
}, 10);
window.addEventListener('scroll', debouncedScroll);
document.addEventListener('DOMContentLoaded', () => {
    console.log('ECO.REC One Page carregada com sucesso! 🚀');
    

    if (navLinks.length > 0) {
        navLinks[0].classList.add('active');
    }
    

    document.body.style.opacity = '0';
    setTimeout(() => {
        document.body.style.opacity = '1';
    }, 100);
});
console.log('%cECO.REC - Inteligência Territorial Urbana 🌆', 'color: #466e63; font-size: 24px; font-weight: bold;');
console.log('%cTransformando dados em decisões e cidades em oportunidades', 'color: #cc4f5d; font-size: 14px;');
console.log('%cDesenvolvido com ❤️ para cidades mais inteligentes e sustentáveis', 'color: #d8865d; font-size: 12px;');

