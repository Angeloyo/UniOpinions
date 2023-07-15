/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';

// Icons and img
const sunIcon = document.getElementById("sun");
const moonIcon = document.getElementById("moon");
// phone version different icons
// const sunIcon2 = document.getElementById("sun2");
// const moonIcon2 = document.getElementById("moon2");
const switchButton = document.getElementById("switchButton");
// const switchButton2 = document.getElementById("switchButton2");
// const blackBanner = document.getElementById("banner-black");
// const whiteBanner = document.getElementById("banner-white");

// Theme vars
const userTheme = localStorage.getItem("theme");
const systemTheme = window.matchMedia("(prefers-color-scheme: dark)").matches;

// Icon toggling
const iconToggle = () => {
    // moonIcon.classList.toggle("hidden");
    // sunIcon.classList.toggle("hidden");
    moonIcon.classList.toggle("display-none");
    sunIcon.classList.toggle("display-none");
    // moonIcon2.classList.toggle("display-none");
    // sunIcon2.classList.toggle("display-none");
    // blackBanner.classList.toggle("display-none");
    // whiteBanner.classList.toggle("display-none");
}

// Initial Theme check
const themeCheck = () => {
    if(userTheme == "dark" || (!userTheme && systemTheme)){
        document.documentElement.classList.add("dark");
        // moonIcon.classList.add("hidden");
        moonIcon.classList.add("display-none");
        // moonIcon2.classList.add("display-none");
        return;
    }
    sunIcon.classList.add("display-none");
    // sunIcon2.classList.add("display-none");

}

// Manual theme switch
const themeSwitch = () => {
    if(document.documentElement.classList.contains("dark")){
        document.documentElement.classList.remove("dark");
        localStorage.setItem("theme", "light");
        iconToggle();
        return;
    }
    document.documentElement.classList.add("dark");
    localStorage.setItem("theme", "dark");
    iconToggle();
}

switchButton.addEventListener("click", () => {
    themeSwitch();
});

// switchButton2.addEventListener("click", () => {
//     themeSwitch();
// });

// Invoke theme check on initial load
themeCheck();

// document.getElementById("menu-button").addEventListener("click", function() {
//     var menuItems = document.getElementById("menu-items");
//     if (menuItems.classList.contains("hidden")) {
//         menuItems.classList.remove("hidden");
//     } else {
//         menuItems.classList.add("hidden");
//     }
// });

