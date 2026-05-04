export function fadeInAnimate () {
    const h1 = document.querySelector('h1');
    const spans = h1.querySelectorAll('span');

    spans.forEach(span => {
      span.style.opacity = '0';
      span.style.transform = 'translateY(10px)';
    });
    
    spans.forEach((span, index) => {
      setTimeout(() => {
        span.style.transition = 'opacity 0.8s ease-out, transform 0.8s ease-out';
        span.style.opacity = '1';
        span.style.transform = 'translateY(0)';
      }, index * 200); 
    });
}

export const electricAnimate = () => {
  
  const h1 = document.querySelector('header h1');
  const spans = h1.querySelectorAll('span');
  
  spans.forEach(span => {
    const text = span.textContent;
    span.textContent = '';
    
    text.split('').forEach((char, index) => {
      const charSpan = document.createElement('span');
      charSpan.textContent = char;
      charSpan.classList.add('char');
      charSpan.style.opacity = '0';
      charSpan.style.display = 'inline-block';
      charSpan.style.animationDelay = `${index * 0.05}s`;
      span.appendChild(charSpan);
    });
  });
  
  setTimeout(() => {
    document.querySelectorAll('.char').forEach(char => {
      char.classList.add('electric-in');
    });
  }, 100);
}


export function fadeInAnimateLinks () {
  const mainLinks = document.getElementById('main-links');
  setTimeout(() => {
    mainLinks.classList.remove('opacity-0', 'translate-y-4');
    mainLinks.classList.add('opacity-100', 'translate-y-0');
  }, 600); 
}

export function animateH1Electric() {
  const h1 = document.querySelector('header h1');
  if (!h1) return;
  
  const spans = h1.querySelectorAll('span');
  
  spans.forEach(span => {
    const text = span.textContent;
    span.textContent = '';
    
    text.split('').forEach((char, index) => {
      const charSpan = document.createElement('span');
      charSpan.textContent = char;
      charSpan.classList.add('char');
      charSpan.style.display = 'inline-block';
      charSpan.style.animationDelay = `${index * 0.05}s`;
      span.appendChild(charSpan);
    });
  });
  
  setTimeout(() => {
    document.querySelectorAll('.char').forEach(char => {
      char.classList.add('electric-in');
    });
  }, 100);
}

export function bgGranulateTransition() {
  const bgImage = document.querySelector('main picture img');
  if (!bgImage) return;
  
  bgImage.classList.add('bg-granulate-in');
  
  if (bgImage.complete) {
    bgImage.classList.add('bg-granulate-in');
  } else {
    bgImage.addEventListener('load', () => {
      bgImage.classList.add('bg-granulate-in');
    });
  }
}

export function sectionFadeIn() {
    const sections = document.querySelectorAll('[data-item-section]');

    sections.forEach((section) => {
        section.style.transform = 'translateY(6rem)';
    });

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add('reveal');
                observer.unobserve(entry.target);
            }
        });
    }, {
          rootMargin: '0px 0px -96px 0px',
          threshold: 0.2
    });
    
    setTimeout(() => {
        sections.forEach((section) => {
            observer.observe(section);
        });
    }, 100);    
}

// Smooth about page load animation
export function aboutPageTransition() {
  const main = document.querySelector('[data-page-main]');
  const leftSlide = document.querySelector('[data-fade-slide="left"]');
  const rightSlide = document.querySelector('[data-fade-slide="right"]');
  
  if (!main) return;
  
  // Fade in main container
  setTimeout(() => {
    main.classList.remove('opacity-0', 'translate-y-4');
    main.classList.add('opacity-100', 'translate-y-0');
  }, 100);
  
  // Stagger the image and content
  if (leftSlide) {
    setTimeout(() => {
      leftSlide.classList.add('transition-all', 'duration-700', 'ease-out');
      leftSlide.classList.remove('opacity-0', '-translate-x-4');
      leftSlide.classList.add('opacity-100', 'translate-x-0');
    }, 300);
  }
  
  if (rightSlide) {
    setTimeout(() => {
      rightSlide.classList.add('transition-all', 'duration-700', 'ease-out');
      rightSlide.classList.remove('opacity-0', 'translate-x-4');
      rightSlide.classList.add('opacity-100', 'translate-x-0');
    }, 500);
  }
}

// Alternative: Simpler unified fade
export function aboutPageFade() {
  const main = document.querySelector('[data-page-main]');
  if (!main) return;
  
  requestAnimationFrame(() => {
    main.classList.remove('opacity-0', 'translate-y-4');
    main.classList.add('opacity-100', 'translate-y-0');
  });
}

// Contact page smooth cascade animation
export function contactPageTransition() {
  const main = document.querySelector('[data-page-main]');
  const heading = document.querySelector('[data-contact-heading]');
  const intro = document.querySelector('[data-contact-intro]');
  const form = document.querySelector('[data-contact-form]');
  const formFields = document.querySelectorAll('[data-form-field]');
  const formButton = document.querySelector('[data-form-button]');
  
  if (!main) return;
  
  // 1. Fade in main container
  setTimeout(() => {
    main.classList.remove('opacity-0', 'translate-y-4');
    main.classList.add('opacity-100', 'translate-y-0');
  }, 100);
  
  // 2. Animate heading
  if (heading) {
    setTimeout(() => {
      heading.classList.add('transition-all', 'duration-500', 'ease-out');
      heading.classList.remove('opacity-0', '-translate-y-2');
      heading.classList.add('opacity-100', 'translate-y-0');
    }, 300);
  }
  
  // 3. Animate intro text
  if (intro) {
    setTimeout(() => {
      intro.classList.add('transition-all', 'duration-500', 'ease-out');
      intro.classList.remove('opacity-0', 'translate-y-2');
      intro.classList.add('opacity-100', 'translate-y-0');
    }, 450);
  }
  
  // 4. Fade in form container
  if (form) {
    setTimeout(() => {
      form.classList.add('transition-all', 'duration-600', 'ease-out');
      form.classList.remove('opacity-0', 'translate-y-3');
      form.classList.add('opacity-100', 'translate-y-0');
    }, 600);
  }
  
  // 5. Stagger form fields
  formFields.forEach((field) => {
    const delay = parseInt(field.dataset.delay) || 0;
    setTimeout(() => {
      field.classList.remove('opacity-0', 'translate-x-2');
      field.classList.add('opacity-100', 'translate-x-0');
    }, 750 + (delay * 100));
  });
  
  // 6. Animate submit button
  if (formButton) {
    setTimeout(() => {
      formButton.classList.add('transition-all', 'duration-500', 'ease-out');
      formButton.classList.remove('opacity-0', 'translate-y-2');
      formButton.classList.add('opacity-100', 'translate-y-0');
    }, 1050);
  }
}

// Alternative: Simpler version without field stagger
export function contactPageFade() {
  const main = document.querySelector('[data-page-main]');
  if (!main) return;
  
  requestAnimationFrame(() => {
    main.classList.remove('opacity-0', 'translate-y-4');
    main.classList.add('opacity-100', 'translate-y-0');
    
    // Fade all children together
    const children = main.querySelectorAll('[data-contact-heading], [data-contact-intro], [data-contact-form]');
    children.forEach((child, index) => {
      setTimeout(() => {
        child.classList.add('transition-all', 'duration-600', 'ease-out');
        child.classList.remove('opacity-0', '-translate-y-2', 'translate-y-2', 'translate-y-3');
        child.classList.add('opacity-100', 'translate-y-0');
      }, 200 + (index * 150));
    });
  });
}