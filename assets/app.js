/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';
import 'select2/dist/css/select2.min.css';
import './select2_init'; 
import './styles/customSelect.css';
import 'select2/dist/js/i18n/es';
import A11yDialog from 'a11y-dialog'

var dialogEl = document.getElementById('my-dialog')

// Comprobar si dialogEl existe
if (dialogEl) {
    var dialog = new A11yDialog(dialogEl)

    dialog.on('show', function (event) {
        const container = event.target

        // And if the event is the result of a UI interaction (i.e. was not triggered
        // programmatically via `.show(..)`), the `detail` prop contains the original
        // event
        const target = event.detail.target
        const opener = target.closest('[data-a11y-dialog-show]')

        // console.log(container, target, opener)
    })
}


// Icons and img
const sunIcon = document.getElementById("sun");
const moonIcon = document.getElementById("moon");
const logoBlack = document.getElementById("logoBlack");
const logoWhite = document.getElementById("logoWhite");
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
    logoBlack.classList.toggle("display-none");
    logoWhite.classList.toggle("display-none");
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
        logoBlack.classList.add("display-none");
        // moonIcon2.classList.add("display-none");
        return;
    }
    sunIcon.classList.add("display-none");
    logoWhite.classList.add("display-none");
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

