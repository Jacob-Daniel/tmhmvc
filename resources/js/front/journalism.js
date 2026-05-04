export const initJournalism = () => {
  document.querySelectorAll("main h2 a.journalism-links").forEach((link) => {
    link.addEventListener("mouseover", updateImage);
    link.addEventListener("touchstart", updateImage);
  });

  function updateImage(e) {
    const id = e.currentTarget.dataset.id;

    document.querySelectorAll("#switch > div").forEach((div) => {
      div.classList.remove("opacity-100", "z-10");
      div.classList.add("opacity-0", "z-0");
    });

    const targetDiv = document.getElementById(id);
    if (targetDiv) {
      targetDiv.classList.remove("opacity-0", "z-0");
      targetDiv.classList.add("opacity-100", "z-10");
    }
  }
};

// Journalism page load with image preload
export function journalismPageTransition() {
  const main = document.querySelector("[data-journalism-main]");
  const titlesContainer = document.querySelector("[data-journalism-titles]");
  const titles = document.querySelectorAll("[data-journalism-title]");
  const activeImage = document.querySelector("#switch > div.opacity-100 img");

  if (!main) return;

  // Wait for the active image to load before showing content
  const showContent = () => {
    // 1. Fade in main container
    setTimeout(() => {
      main.classList.remove("opacity-0");
      main.classList.add("opacity-100");
    }, 100);

    // 2. Fade in titles container
    setTimeout(() => {
      titlesContainer.classList.remove("opacity-0", "translate-y-6");
      titlesContainer.classList.add("opacity-100", "translate-y-0");
    }, 300);

    // 3. Stagger individual titles
    titles.forEach((title) => {
      const delay = parseInt(title.dataset.delay) || 0;
      setTimeout(
        () => {
          title.classList.remove("opacity-0", "translate-y-4");
          title.classList.add("opacity-100", "translate-y-0");
        },
        500 + delay * 150,
      );
    });
  };

  // Check if image is already loaded
  if (activeImage) {
    if (activeImage.complete && activeImage.naturalHeight !== 0) {
      showContent();
    } else {
      activeImage.addEventListener("load", showContent);
      // Fallback timeout in case image fails to load
      setTimeout(showContent, 1000);
    }
  } else {
    // No active image, just show content
    showContent();
  }
}

// Alternative: Simpler version without image preload check
export function journalismPageFade() {
  const main = document.querySelector("[data-journalism-main]");
  const titlesContainer = document.querySelector("[data-journalism-titles]");

  if (!main) return;

  requestAnimationFrame(() => {
    main.classList.remove("opacity-0");
    main.classList.add("opacity-100");

    setTimeout(() => {
      titlesContainer.classList.remove("opacity-0", "translate-y-6");
      titlesContainer.classList.add("opacity-100", "translate-y-0");

      // Fade all titles together
      const titles = document.querySelectorAll("[data-journalism-title]");
      titles.forEach((title) => {
        title.classList.remove("opacity-0", "translate-y-4");
        title.classList.add("opacity-100", "translate-y-0");
      });
    }, 300);
  });
}
