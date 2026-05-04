import "../../css/front/main.css";
import { initFilmViewer } from "./modules/filmViewer.js";
import { initFormSend } from "./form.ts";
import {
	electricAnimate,
	fadeInAnimateLinks,
	bgGranulateTransition,
	sectionFadeIn,
	aboutPageTransition,
	contactPageTransition,
	contactPageFade,
} from "./animation.js";
import { toggleMobileMenu, toggleSubmenus } from "./navigation.js";
import {
	initJournalism,
	journalismPageFade,
	journalismPageTransition,
} from "./journalism.js";

document.addEventListener("DOMContentLoaded", () => {
	const body = document.querySelector("body");

	// if(!body.className.includes('home')) {
	// 	initScrollHeader();
	// 	console.log('not home')
	// }

	toggleMobileMenu();
	toggleSubmenus(".journalism-toggle");

	if (body.className.includes("home")) {
		bgGranulateTransition();
		electricAnimate();
		fadeInAnimateLinks();
	}

	if (body.className.includes("contact")) {
		initFormSend();
	}

	if (body.className.includes("filmmaking")) {
		initFilmViewer(2, "/api/extracontent.php");
		sectionFadeIn();
	}

	if (body.className.includes("journalism")) {
		journalismPageTransition();
		initJournalism();
	}

	if (body.className.includes("subcategory")) {
		sectionFadeIn();
		initFilmViewer(1, "/api/extracontent.php");
	}
	if (body.className.includes("about")) {
		aboutPageTransition();
	}
	if (body.className.includes("contact")) {
		contactPageTransition();
		contactPageFade();
	}
	const navbarDropdown = document.getElementById("navbarDropdown");
	if (navbarDropdown) {
		navbarDropdown.addEventListener("click", () => {
			document
				.querySelectorAll("ul.dropdown-menu")
				.forEach((menu) => menu.classList.toggle("hidden"));
			document
				.querySelectorAll(".nav-item.dropdown")
				.forEach((item) => item.classList.toggle("fit"));
		});
	}
});
