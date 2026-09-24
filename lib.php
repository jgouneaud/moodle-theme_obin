<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Theme OBIN - fonctions SCSS.
 *
 * @package    theme_obin
 * @copyright  2026 Citoyenneté et Inclusion Numérique (OBIN) <contact@obin-asso.fr>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Contenu SCSS principal : on réutilise tel quel le preset "default" de
 * Boost. Le thème OBIN est un thème enfant, il ne redéfinit pas
 * Bootstrap/Boost lui-même - seules les variables de marque (couleurs,
 * polices) sont modifiées, via theme_obin_get_pre_scss() ci-dessous.
 *
 * @param theme_config $theme
 * @return string
 */
function theme_obin_get_main_scss_content($theme) {
    global $CFG;
    return file_get_contents($CFG->dirroot . '/theme/boost/scss/preset/default.scss');
}

/**
 * SCSS injected BEFORE the Boost preset compilation: this is where Bootstrap
 * default variables are replaced with those from the official OBIN graphic
 * charter (Charte_Graphique_OBIN.pdf v1.0, June 2026). Colours and font
 * are configurable via Site administration > Appearance > OBIN (see
 * settings.php), with the charter values as defaults.
 *
 * @param theme_config $theme
 * @return string
 */
function theme_obin_get_pre_scss($theme) {
    $scss = '';

    $brandcolor = !empty($theme->settings->brandcolor) ? $theme->settings->brandcolor : '#3D8FE8';
    $secondarycolor = !empty($theme->settings->secondarycolor) ? $theme->settings->secondarycolor : '#64D6A8';

    // Brand primary colours.
    $scss .= '$primary: ' . $brandcolor . ";\n";       // OBIN blue.
    $scss .= '$success: ' . $secondarycolor . ";\n";   // Teal green.
    $scss .= '$info: #4CBFD1;' . "\n";                 // Cyan accent.

    // Brand neutrals: text black (never pure #000000), light grey for section/card backgrounds.
    $scss .= '$body-color: #111827;' . "\n";
    $scss .= '$gray-100: #F3F4F6;' . "\n";
    $scss .= '$gray-700: #374151;' . "\n";
    $scss .= '$gray-600: #9CA3AF;' . "\n";

    // Brand typography: Poppins for headings, Lato for body text. Loaded from Google Fonts
    // (see extra SCSS callback; the @import must come before any font-family use).
    $scss .= '$font-family-sans-serif: "Lato", -apple-system, BlinkMacSystemFont,'
        . ' "Segoe UI", Roboto, Arial, sans-serif;' . "\n";
    $scss .= '$headings-font-family: "Poppins", "Lato", sans-serif;' . "\n";
    $scss .= '$headings-font-weight: 600;' . "\n";

    if (!empty($theme->settings->scsspre)) {
        $scss .= $theme->settings->scsspre;
    }

    return $scss;
}

/**
 * Extra SCSS injected AFTER the Boost preset compilation: supplementary rules
 * that are not simple Bootstrap variables (font import, brand gradient,
 * keyboard-focus accessibility improvement).
 *
 * @param theme_config $theme
 * @return string
 */
function theme_obin_get_extra_scss($theme) {
    $brandcolor = !empty($theme->settings->brandcolor) ? $theme->settings->brandcolor : '#3D8FE8';
    $secondarycolor = !empty($theme->settings->secondarycolor) ? $theme->settings->secondarycolor : '#64D6A8';
    $logowhitefilter = !isset($theme->settings->logowhitefilter) || $theme->settings->logowhitefilter;
    $footerbgcolor = !empty($theme->settings->footerbgcolor) ? $theme->settings->footerbgcolor : '#1a3a5c';

    $content = '
/*
 * Official teal → blue gradient (direction reversed per OBIN\'s brand guidelines).
 * The charter reserves this gradient "for the logo and strong graphic elements"
 * (headings, CTA buttons, accents) — not for general use. Applied only to
 * the navbar and primary buttons. The navbar gradient is subtly animated
 * (requested "wow effect" on the menu) via an oversized background (200%)
 * whose position is shifted.
 */
.navbar.fixed-top {
    background: linear-gradient(90deg, ' . $secondarycolor . ' 0%, ' . $brandcolor . ' 100%) !important;
    background-size: 200% 200%;
    animation: obin-navbar-gradient 12s ease infinite;
}
@keyframes obin-navbar-gradient {
    0%, 100% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
}
.btn-primary {
    background-color: ' . $brandcolor . ';
    border-color: ' . $brandcolor . ';
    transition: transform .15s ease, box-shadow .15s ease, filter .15s ease;
}
.btn-primary:hover,
.btn-primary:focus {
    filter: brightness(0.92);
    transform: translateY(-1px);
    box-shadow: 0 4px 14px rgba(17, 24, 39, .18);
}

/*
 * The logo (Site administration > Appearance > Logos) is provided by each
 * organisation that installs this theme: format and proportions vary (very
 * wide, very tall, square...). Only the SIZE is constrained (object fitted
 * inside a fixed frame, proportions preserved).
 *
 * In the navbar, the logo sits on the brand gradient: whatever its original
 * colour (the uploaded file may be dark, coloured...), it is converted to a
 * WHITE silhouette via a CSS filter (brightness(0) = all black, invert(1) =
 * black → white), so it remains readable across any part of the animated
 * gradient, consistent with the navbar text already forced white below.
 * Same technique as the Moodle footer logo (.obin-footer-moodlelogo).
 * The background must be transparent (PNG) for a clean result — an opaque
 * white background would become a plain white rectangle once inverted.
 */
.navbar-brand .logo,
.navbar-brand img {
    max-height: 40px;
    max-width: 180px;
    width: auto;
    object-fit: contain;' . ($logowhitefilter ? '
    filter: brightness(0) invert(1);' : '') . '
}
/*
 * Login page: WHITE background (see rules below), not the gradient — the logo
 * is therefore kept in its original colours (the white filter above would make
 * it invisible against a light background).
 */
body.pagelayout-login #logoimage,
body.pagelayout-login .login-logo img {
    max-height: 120px;
    max-width: 100%;
    width: auto;
    object-fit: contain;
}

/*
 * Contrast on the gradient: Boost assumes a white navbar ("navbar-light") and
 * colours its links/text in brand blue — which makes them unreadable (or even
 * invisible) once the navbar is dressed in the colour gradient. We therefore
 * force white text throughout the entire navbar, regardless of which part of
 * the gradient it sits on.
 */
.navbar.fixed-top,
.navbar.fixed-top .nav-link,
.navbar.fixed-top a,
.navbar.fixed-top .usermenu .login,
.navbar.fixed-top .navbar-toggler-icon,
.navbar.fixed-top label {
    color: rgba(255,255,255,.82);
}
.navbar.fixed-top .nav-link:hover,
.navbar.fixed-top a:hover {
    color: #fff;
    opacity: 1;
}

/*
 * The edit-mode toggle was reported as unreadable despite the text-shadow above
 * (the lightest portion of the animated gradient is too close to white for a
 * shadow alone to suffice). A semi-transparent dark background is added to
 * guarantee contrast at any moment of the animation, regardless of the exact
 * gradient colour at that point.
 */
.navbar.fixed-top .editmode-switch-form {
    background-color: rgba(17, 24, 39, .55);
    border-radius: 6px;
    padding: .25rem .6rem;
}

/*
 * An old Moodle core generic ":focus" rule applies a plain WHITE background to
 * any focused element (e.g. the "Log in" link, which is neither ".nav-link" nor
 * ".dropdown-menu a" and was therefore not covered by any rule above): combined
 * with our white text, this becomes unreadable on focus/click. We neutralise
 * that white background throughout the navbar and fall back to the same focus
 * style as the menu tabs.
 */
.navbar.fixed-top a:focus {
    background-color: rgba(255, 255, 255, .18);
    color: #fff;
    outline: none;
    box-shadow: inset 0 0 0 3px rgba(255, 255, 255, .9);
}

/*
 * Dropdowns and popovers (bell notifications, messages, user menu...) are
 * nested inside <nav class="navbar fixed-top"> in the DOM, but appear as
 * pop-ups on a WHITE background, not on the gradient. The rules above (white
 * text + shadow throughout the navbar) made them completely unreadable: white
 * text on white background. We restore normal text colour and remove the shadow,
 * in all states (default/hover/focus). Two different components to cover:
 * ".dropdown-menu" (Bootstrap, e.g. user menu) and
 * ".popover-region-container" (Moodle component, notifications/messages panel —
 * NOT ".popover-region" itself, which also wraps the bell/messages button: that
 * button must stay white since it sits on the gradient, not a white background —
 * bug originally noticed on the bell icon, which became invisible when
 * accidentally matched by this rule).
 */
.navbar.fixed-top .dropdown-menu,
.navbar.fixed-top .dropdown-menu a,
.navbar.fixed-top .dropdown-menu .icon,
.navbar.fixed-top .popover-region-container,
.navbar.fixed-top .popover-region-container a,
.navbar.fixed-top .popover-region-container .icon,
.navbar.fixed-top .popover-region-container label {
    color: #111827;
    text-shadow: none;
}
.navbar.fixed-top .dropdown-menu a:hover,
.navbar.fixed-top .dropdown-menu a:focus,
.navbar.fixed-top .popover-region-container a:hover,
.navbar.fixed-top .popover-region-container a:focus {
    color: #111827;
    background-color: #F3F4F6;
}

/*
 * Hover/focus on menu tabs (.moremenu) inherits a Moodle core rule that applies
 * a light grey background ($gray-100, #F3F4F6) and an active blue border —
 * readable on a white navbar, but unreadable here: white text on near-white
 * background, and blue border invisible over the blue portion of the gradient.
 * Replaced with a translucent white hover that remains consistent wherever the
 * gradient is.
 */
.navbar.fixed-top .nav-link:hover,
.navbar.fixed-top .nav-link:focus,
.navbar.fixed-top .nav-link.active,
.navbar.fixed-top .nav-link.active:hover,
.navbar.fixed-top .nav-link.active:focus {
    background-color: transparent !important;
    border-color: transparent;
    color: #fff;
}

/*
 * Navbar icons (language switcher, menu toggle, etc.): the general rule below
 * (".icon" in brand blue) would render them blue on a blue background —
 * unreadable. They are kept white here, regardless of the gradient portion.
 */
.navbar.fixed-top .icon,
.navbar.fixed-top .dropdown-toggle::after {
    color: #fff;
}

/*
 * "Wow effect" on the main menu: subtle hover lift and animated white underline
 * (more readable than the default blue on a coloured gradient background,
 * especially over its blue portion).
 */
.navbar.fixed-top .nav-link,
.navbar.fixed-top .nav-link.active,
.navbar.fixed-top .nav-link:hover,
.navbar.fixed-top .nav-link:focus {
    box-shadow: none !important;
    background: transparent !important;
    background-color: transparent !important;
    border-bottom-color: transparent;
}

/* Suppression totale de tous les fonds/halos sur les liens navbar */
.navbar.fixed-top .nav-link,
.navbar.fixed-top .nav-link.active,
.navbar.fixed-top .nav-link:hover,
.navbar.fixed-top .nav-link:focus,
.navbar.fixed-top .nav-link:focus-visible,
.navbar.fixed-top .nav-link.active:hover,
.navbar.fixed-top .nav-link.active:focus,
.navbar.fixed-top li.nav-item > .nav-link {
    background: none !important;
    background-color: transparent !important;
    box-shadow: none !important;
    border-bottom-color: transparent !important;
    outline: none !important;
}

.navbar .nav-link {
    position: relative;
    transition: transform .2s ease;
}
.navbar .nav-link:hover {
    transform: translateY(-2px);
}
.navbar .nav-link.active {
    /*
     * Boost sets "color: rgba(0,0,0,.9)" (near-black text) on the active tab
     * via ".navbar-light .navbar-nav .nav-link.active" — more specific than our
     * white colour rules above. !important used here rather than stacking more
     * classes, as this is a one-off, well-identified case (unlike the rest of
     * the stylesheet).
     */
    color: #fff !important;
    border-bottom-color: transparent;
}
.navbar .nav-link::after {
    content: "";
    position: absolute;
    left: 50%;
    right: 50%;
    bottom: 0;
    height: 3px;
    border-radius: 2px 2px 0 0;
    background: rgba(255,255,255,.75);
    transition: left .25s ease, right .25s ease;
}
.navbar .nav-link:hover::after,
.navbar .nav-link.active::after {
    left: 10px;
    right: 10px;
}

/*
 * On the front page, the site-name heading ("Theme Obin", displayed by default
 * by Boost via the standard page header) duplicates the banner title below: it
 * is hidden only when the banner is actually shown (class "obin-hero-active",
 * set by layout/frontpage.php only for logged-out visitors — no effect on the
 * rest of the site or on a logged-in visit to the same page). The space freed
 * up at the top of the page is also collapsed (".main-inner" has its own
 * padding/margin-top of 24px each, intended to add breathing room below the
 * normally-displayed title — an empty gap would otherwise remain between the
 * navbar and the banner).
 */
body.obin-hero-active #page-header,
body.obin-hero-active .page-context-header,
body.obin-hero-active [data-region="page-header-wrapper"] {
    display: none;
}
body.obin-hero-active #topofscroll.main-inner {
    padding-top: 0;
    margin-top: 0;
}
body.obin-hero-active #page-header {
    height: 0;
    overflow: hidden;
    padding: 0;
    margin: 0;
}

/*
 * Front-page hero banner for logged-out visitors: photo + brand gradient
 * overlay + official charter tagline, above the standard content (site
 * summary + available courses) which remains unchanged below. Full viewport
 * width and responsive: the calc() margin/width technique breaks the banner
 * out of any maximum width imposed by parent containers (".limitedwidth"...),
 * regardless of their nesting level.
 * See layout/frontpage.php and templates/frontpage.mustache.
 */
.obin-hero {
    position: relative;
    width: 100vw;
    margin-left: calc(50% - 50vw);
    margin-right: calc(50% - 50vw);
    margin-top: 0;
    margin-bottom: 2rem;
    min-height: 380px;
    display: flex;
    align-items: center;
    justify-content: center;
    background-size: cover;
    background-position: center;
    overflow: hidden;
}
.obin-hero-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(315deg, ' . $brandcolor . 'D9 0%, ' . $secondarycolor . 'C7 100%);
}
.obin-hero-content {
    position: relative;
    z-index: 1;
    max-width: 720px;
    padding: 2rem 1.5rem;
    text-align: center;
    color: #fff;
}
.obin-hero-content h1 {
    font-size: 2.25rem;
    margin-bottom: .75rem;
    color: #fff;
    text-shadow: 0 2px 12px rgba(0, 0, 0, .25);
}
.obin-hero-content p {
    font-size: 1.15rem;
    font-style: italic;
    opacity: .95;
    margin-bottom: 0;
    text-shadow: 0 1px 8px rgba(0, 0, 0, .2);
}
@media (max-width: 575.98px) {
    .obin-hero { min-height: 280px; }
    .obin-hero-content h1 { font-size: 1.5rem; }
    .obin-hero-content p { font-size: 1rem; }
}

/*
 * Inline login form accessible directly from the front-page navbar (instead of
 * redirecting to /login/index.php): panel hidden by default, toggled by clicking
 * the "Log in" link in the navbar (see templates/frontpage.mustache for the
 * toggle script).
 */
.obin-navbar-login {
    display: none;
    position: absolute;
    top: 60px;
    right: 1rem;
    z-index: 1031;
    width: 300px;
    max-width: calc(100vw - 2rem);
    background: #fff;
    color: #111827;
    border-radius: 8px;
    box-shadow: 0 12px 32px rgba(17, 24, 39, .22);
    padding: 1.25rem;
}
.obin-navbar-login.show {
    display: block;
}
.obin-navbar-login label {
    color: #111827;
    font-weight: 600;
    font-size: .9rem;
}

/*
 * Icônes, pictos et boutons secondaires : la charte précise que "le Bleu
 * seul (#3D8FE8) est la couleur principale pour les liens, icônes et
 * éléments interactifs" - or Bootstrap/Moodle les affiche par défaut en
 * gris ($gray-600/$gray-700), ce qui les fait paraître fades/désactivés.
 * On les aligne sur la couleur de marque plutôt que sur le gris.
 */
.btn-secondary,
.btn-outline-secondary {
    color: ' . $brandcolor . ';
    background-color: transparent;
    border-color: ' . $brandcolor . ';
}
.btn-secondary:hover,
.btn-secondary:focus,
.btn-outline-secondary:hover,
.btn-outline-secondary:focus {
    color: #fff;
    background-color: ' . $brandcolor . ';
    border-color: ' . $brandcolor . ';
}
a .icon,
[role="button"] .icon,
.action-icon .icon,
.dropdown-toggle::after {
    color: ' . $brandcolor . ';
    opacity: 1;
}
/*
 * Cas particulier des boutons ".btn" pleins (ex. ".btn-primary" bleu plein,
 * "Rechercher des cours") : forcer leurs icônes en bleu marque les rendrait
 * invisibles (bleu sur bleu). L\'icône doit plutôt suivre la couleur de
 * texte déjà correcte du bouton (blanc sur un bouton plein, bleu sur un
 * bouton secondaire/outline).
 */
.btn .icon {
    color: inherit;
    opacity: 1;
}

/*
 * Focus clavier sur le menu principal : ombre INTERNE (inset) plutôt que le
 * box-shadow "sortant" utilisé par défaut par Moodle sur .nav-link:focus.
 * Constat de débogage : la barre de navigation est en position:fixed ; un
 * box-shadow sortant appliqué à n\'importe lequel de ses descendants
 * (.nav-link, son <li>, le <ul>...) se retrouve systématiquement rogné pile
 * au bord bas de la navbar, alors que overflow/contain/clip-path valent
 * tous "visible"/"none" à chaque niveau (vérifié par inspection), et que ce
 * même box-shadow appliqué à la navbar ELLE-MÊME ne subit aucun rognage.
 * Un box-shadow "inset" reste entièrement DANS la boîte de l\'élément : rien
 * à rogner, donc le problème (quelques pixels manquants en bas de l\'anneau
 * de focus) disparaît par construction.
 */
.navbar .nav-link:focus,
.navbar .nav-link:focus-visible {
    outline: none;
    box-shadow: none;
}

/*
 * Accessibilité : indicateur de focus clavier renforcé pour le reste du
 * site (hors barre de navigation fixe, cf. ci-dessus), cohérent avec la
 * mission d\'inclusion numérique de l\'association (le focus par défaut de
 * Bootstrap est parfois trop discret pour la navigation au clavier).
 */
a:focus-visible,
button:focus-visible,
input:focus-visible,
.btn:focus-visible {
    outline: 3px solid ' . $brandcolor . ';
    outline-offset: 2px;
}

/*
 * Couleur des liens du contenu (hors navbar/boutons, qui ont leurs propres
 * règles de couleur ci-dessus, plus spécifiques et donc prioritaires).
 */
a {
    color: ' . $brandcolor . ';
}
a:hover,
a:focus {
    color: ' . $brandcolor . ';
    filter: brightness(0.85);
}

/*
 * Couleur des menus à onglets (ex. onglets "Général / Utilisateurs / Cours..."
 * d\'Administration du site, ".nav-tabs" Bootstrap).
 */
.nav-tabs .nav-link {
    color: ' . $brandcolor . ';
}
.nav-tabs .nav-link.active {
    color: #111827;
    border-bottom-color: ' . $brandcolor . ';
}
/*
 * Pas de contour de focus sur ces onglets (demandé explicitement) : le
 * focus clavier générique (a:focus-visible plus bas) et le style natif du
 * navigateur y rendaient mal (contour épais, décalé, débordant sur les
 * onglets voisins).
 */
.nav-tabs .nav-link:focus,
.nav-tabs .nav-link:focus-visible {
    outline: none !important;
    box-shadow: none !important;
}

/*
 * Page de connexion : pleine largeur (demandé explicitement - pas de carte
 * centrée flottant sur un fond dégradé visible sur les côtés). Le dégradé
 * de marque reste présent, mais comme un simple liseré en tête de page
 * (cohérent avec la navbar), pas comme fond de toute la page.
 * Le logo "Moodle" par défaut affiché ici (Administration du site >
 * Apparence > Logos) n\'est en revanche pas modifiable en CSS - c\'est un
 * fichier à déposer côté administration (voir échange précédent).
 */
/*
 * Boost pose par défaut un fond gris à motif rayé sur ".pagelayout-login
 * #page" (sélecteur ID, donc plus prioritaire qu\'une simple règle de
 * classe - un premier essai avec ".container-fluid" n\'avait aucun effet
 * pour cette raison). On le neutralise explicitement.
 */
.pagelayout-login #page {
    background: #fff;
    background-image: none;
}
body.pagelayout-login .container-fluid {
    background: #fff;
    padding-top: 0;
}
body.pagelayout-login .login-wrapper {
    min-height: 100vh;
    padding: 0;
    background: #fff;
}
body.pagelayout-login .login-container {
    position: relative;
    width: 100%;
    max-width: 100%;
    margin: 0;
    border-radius: 0;
    box-shadow: none;
    padding-top: calc(3rem + 4px);
}
body.pagelayout-login .login-container::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, ' . $secondarycolor . ' 0%, ' . $brandcolor . ' 100%);
}

/*
 * Pied de page visible du site (cf. classes/output/core_renderer.php et
 * templates/footer_content.mustache), affiché sous le contenu sur toutes
 * les pages. Pleine largeur de viewport (même technique que la bannière
 * d\'accueil : la page a un conteneur à largeur maximale - classe
 * "limitedwidth" - qui empêcherait sinon le footer d\'atteindre les bords
 * sur un grand écran).
 */
.obin-footer {
    width: 100vw;
    margin-left: calc(50% - 50vw);
    margin-right: calc(50% - 50vw);
    background: ' . $footerbgcolor . ';
    color: #F3F4F6;
    padding: .5rem 1rem;
    margin-top: 2rem;
}
.obin-footer-inner {
    max-width: 1140px;
    margin: 0 auto;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: flex-end;
    gap: .5rem 1.5rem;
}
.obin-footer-moodlelogo {
    height: 24px;
    width: auto;
    margin-right: auto;
    filter: brightness(0) invert(1);
    opacity: .85;
}
.obin-footer-links {
    display: flex;
    flex-wrap: wrap;
    gap: .5rem 1.25rem;
}
.obin-footer-links a {
    color: #F3F4F6;
    text-decoration: underline;
    font-size: .9rem;
}
.obin-footer-links a:hover,
.obin-footer-links a:focus {
    color: #fff;
}
.obin-footer-copyright {
    color: #9CA3AF;
    font-size: .85rem;
}

/*
 * Lien discret "Je soutiens le Theme OBIN pour Moodle" (picto coeur) et
 * pop-in HelloAsso associée (cf. templates/footer_content.mustache pour le
 * balisage et le script d\'ouverture/fermeture). Volontairement sobre : un
 * simple lien texte, pas un bouton visuellement appuyé.
 */
.obin-footer-social { display: flex; gap: .5rem; margin-top: .75rem; }
.obin-social-btn { display: inline-flex; color: rgba(255,255,255,.6); text-decoration: none; transition: color .15s; }
.obin-social-btn:hover, .obin-social-btn:focus { color: #fff; }
.obin-donate-btn {
    background: none;
    border: none;
    color: #F3F4F6;
    opacity: .75;
    padding: 0;
    font-size: .85rem;
    cursor: pointer;
    transition: opacity .15s ease;
}
.obin-donate-btn:hover,
.obin-donate-btn:focus {
    opacity: 1;
    text-decoration: underline;
}

/*
 * Deux blocs de contenu libre en colonnes sous la liste des cours
 * disponibles, et bouton "Créer un compte" (cf. layout/frontpage.php et
 * templates/frontpage.mustache). Contenu et disponibilité entièrement
 * dépendants des réglages du thème - rien de figé ici, ce thème étant un
 * template réutilisable par n\'importe quelle structure.
 */
.obin-frontblocks {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1.5rem;
    margin-top: 2.5rem;
}
@media (min-width: 768px) {
    .obin-frontblocks {
        grid-template-columns: 1fr 1fr;
    }
}
.obin-frontblock {
    background: #F3F4F6;
    border-radius: 8px;
    padding: 1.5rem;
}
.obin-frontblock p:last-child {
    margin-bottom: 0;
}
.obin-signup-cta {
    text-align: center;
    margin-top: 2rem;
}
.obin-footer { padding: 3rem 1.5rem 2rem; }
.obin-footer-inner { display: grid; grid-template-columns: 1fr; gap: 2.5rem; max-width: 1140px; margin: 0 auto; }
@media (min-width: 768px) { .obin-footer-inner { grid-template-columns: 1.5fr 1.5fr 1fr; gap: 2rem 4rem; align-items: start; } }
.obin-footer-col { display: flex; flex-direction: column; gap: .6rem; }
.obin-footer-sitename { font-size: 1rem; font-weight: 600; color: #fff; letter-spacing: .01em; }
.obin-footer-tagline { font-size: .875rem; color: rgba(255,255,255,.65); margin: 0; line-height: 1.5; }
.obin-footer-copyright { font-size: .8rem; color: rgba(255,255,255,.45); margin-top: .5rem; }
.obin-footer-moodlelogo { height: 36px; width: auto; filter: brightness(0) invert(1); opacity: .5; margin-top: .75rem; }
.obin-footer-col--links { gap: .5rem; }
.obin-footer-col--links a { color: rgba(255,255,255,.7); text-decoration: none; font-size: .875rem; transition: color .15s ease; display: inline-block; }
.obin-footer-col--links a:hover, .obin-footer-col--links a:focus { color: #fff; }
.obin-footer-app-label { font-size: .7rem; text-transform: uppercase; letter-spacing: .08em; color: rgba(255,255,255,.4); margin: 0 0 .4rem; font-weight: 500; }
.obin-footer-app-btns { display: flex; flex-direction: column; gap: .4rem; }
.obin-app-btn { display: inline-flex; align-items: center; gap: .45rem; padding: .45rem .85rem; border: 1px solid rgba(255,255,255,.22); border-radius: 8px; color: rgba(255,255,255,.85); text-decoration: none; font-size: .78rem; font-weight: 500; transition: background .15s, border-color .15s, color .15s; width: fit-content; }
.obin-app-btn:hover, .obin-app-btn:focus { background: rgba(255,255,255,.1); border-color: rgba(255,255,255,.45); color: #fff; }
.obin-donate-btn { margin-top: 1.25rem; display: inline-flex; align-items: center; gap: .45rem; padding: .35rem .7rem; border: 1px solid rgba(255,255,255,.22); border-radius: 8px; background: none; color: rgba(255,255,255,.75); font-size: .78rem; font-weight: 500; cursor: pointer; transition: background .15s, border-color .15s, color .15s; width: fit-content; align-self: flex-start; }
.obin-donate-btn:hover, .obin-donate-btn:focus { background: rgba(255,255,255,.1); border-color: rgba(255,255,255,.45); color: #fff; }
';

    $content .= theme_obin_get_menu_icons_scss($theme);

    if (!empty($theme->settings->scss)) {
        $content .= $theme->settings->scss;
    }

    return $content;
}

/**
 * Pictogrammes SVG (style trait, "currentColor") utilisés dans les menus
 * d'administration quand le réglage "theme_obin/menuicons" (voir
 * settings.php) vaut "svg". Dessinés spécifiquement pour ce thème - pas un
 * set tiers - pour rester libre de toute question de licence et suivre
 * exactement la couleur de texte du lien qui les porte, comme le faisaient
 * les emoji qu'ils remplacent.
 *
 * @return array<string, string> clé => contenu interne du <svg> (sans la
 *     balise <svg> elle-même ni ses attributs communs, ajoutés par
 *     theme_obin_svg_icon_data_uri()).
 */
function theme_obin_get_menu_svg_icons() {
    return [
        'gear' => '<circle cx="12" cy="12" r="3.2"/><line x1="12" y1="2.5" x2="12" y2="5.5"/>'
            . '<line x1="12" y1="18.5" x2="12" y2="21.5"/><line x1="2.5" y1="12" x2="5.5" y2="12"/>'
            . '<line x1="18.5" y1="12" x2="21.5" y2="12"/><line x1="5.3" y1="5.3" x2="7.4" y2="7.4"/>'
            . '<line x1="16.6" y1="16.6" x2="18.7" y2="18.7"/><line x1="5.3" y1="18.7" x2="7.4" y2="16.6"/>'
            . '<line x1="16.6" y1="7.4" x2="18.7" y2="5.3"/>',
        'users' => '<circle cx="9" cy="8" r="3"/><path d="M4 20c0-3.3 2.2-5.5 5-5.5s5 2.2 5 5.5"/>'
            . '<circle cx="17" cy="9" r="2.3"/><path d="M15.5 14.2c2.4.3 3.8 2.2 3.8 5.3"/>',
        'book' => '<path d="M4 5.5C4 4.7 4.7 4 5.5 4H11v16H5.5A1.5 1.5 0 0 1 4 18.5z"/>'
            . '<path d="M20 5.5c0-.8-.7-1.5-1.5-1.5H13v16h5.5c.8 0 1.5-.7 1.5-1.5z"/>',
        'chartbar' => '<line x1="4" y1="20" x2="4" y2="12"/><line x1="10" y1="20" x2="10" y2="7"/>'
            . '<line x1="16" y1="20" x2="16" y2="14"/><line x1="20" y1="20" x2="20" y2="4"/>',
        'grid' => '<rect x="4" y="4" width="7" height="7" rx="1.2"/><rect x="13" y="4" width="7" height="7" rx="1.2"/>'
            . '<rect x="4" y="13" width="7" height="7" rx="1.2"/><rect x="13" y="13" width="7" height="7" rx="1.2"/>',
        'palette' => '<path d="M12 3.5a8.5 8.5 0 1 0 0 17c1 0 1.5-.6 1.5-1.4 0-.4-.15-.7-.4-1a1.4 1.4 0 0 1 1-2.4H16'
            . 'a3.5 3.5 0 0 0 3.5-3.5C19.5 6.8 16.1 3.5 12 3.5z"/>'
            . '<circle cx="7.5" cy="11" r="1.1" fill="currentColor" stroke="none"/>'
            . '<circle cx="9.5" cy="7.3" r="1.1" fill="currentColor" stroke="none"/>'
            . '<circle cx="14.5" cy="7.3" r="1.1" fill="currentColor" stroke="none"/>'
            . '<circle cx="16.5" cy="11" r="1.1" fill="currentColor" stroke="none"/>',
        'server' => '<rect x="3.5" y="4" width="17" height="6" rx="1.3"/><rect x="3.5" y="14" width="17" height="6" rx="1.3"/>'
            . '<circle cx="7" cy="7" r=".9" fill="currentColor" stroke="none"/>'
            . '<circle cx="7" cy="17" r=".9" fill="currentColor" stroke="none"/>',
        'chartline' => '<polyline points="4,17 9,11 13,14 20,5"/><polyline points="15,5 20,5 20,10"/>',
        'wrench' => '<path d="M14.7 6.3a4 4 0 0 1-5.4 5.4L4 17l3 3 5.3-5.3a4 4 0 0 1 5.4-5.4l-2.6 2.6-2-2z"/>',
        'home' => '<path d="M4 11.5 12 4l8 7.5"/>'
            . '<path d="M6 10v9.5a1 1 0 0 0 1 1h4v-6h2v6h4a1 1 0 0 0 1-1V10"/>',
        'question' => '<circle cx="12" cy="12" r="9"/>'
            . '<path d="M9.5 9.3a2.5 2.5 0 1 1 3.8 2.1c-.9.6-1.3 1.1-1.3 2.1"/>'
            . '<circle cx="12" cy="17" r=".9" fill="currentColor" stroke="none"/>',
        'megaphone' => '<path d="M3 10v4a1 1 0 0 0 1 1h2l1.5 5H10L9 15h1l9 3.5V5.5L10 10H4a1 1 0 0 0-1 1z"/>',
        'bell' => '<path d="M6 10a6 6 0 0 1 12 0v4l1.5 3h-15L6 14z"/><path d="M10 20a2 2 0 0 0 4 0"/>',
        'lightbulb' => '<path d="M9 18h6"/><path d="M10 21h4"/>'
            . '<path d="M12 3a6 6 0 0 0-3.6 10.8c.6.5 1 1.2 1 2.2h5.2c0-1 .4-1.7 1-2.2A6 6 0 0 0 12 3z"/>',
        'document' => '<path d="M7 3.5h7l4 4V20a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V4.5a1 1 0 0 1 1-1z"/>'
            . '<path d="M14 3.5V8h4"/><line x1="8.5" y1="12" x2="15.5" y2="12"/>'
            . '<line x1="8.5" y1="15.5" x2="15.5" y2="15.5"/>',
        'dotlive' => '<circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="3.4" fill="currentColor" stroke="none"/>',
        'clipboard' => '<rect x="6" y="4.5" width="12" height="16" rx="1.5"/><rect x="9" y="3" width="6" height="3" rx="1"/>'
            . '<line x1="9" y1="11" x2="15" y2="11"/><line x1="9" y1="15" x2="15" y2="15"/>',
        'chat' => '<path d="M4 6.5A2.5 2.5 0 0 1 6.5 4h11A2.5 2.5 0 0 1 20 6.5v7A2.5 2.5 0 0 1 17.5 16H10l-4 4v-4H6.5'
            . 'A2.5 2.5 0 0 1 4 13.5z"/>',
        'alerttriangle' => '<path d="M12 4 21 19H3z"/><line x1="12" y1="10" x2="12" y2="14.5"/>'
            . '<circle cx="12" cy="17" r=".9" fill="currentColor" stroke="none"/>',
    ];
}

/**
 * Encode un pictogramme (voir theme_obin_get_menu_svg_icons()) en data URI
 * utilisable directement dans une règle CSS "mask"/"-webkit-mask" : sa
 * couleur suit alors "currentColor", donc la couleur de texte du lien qui
 * le porte (cohérent avec les règles de couleur des onglets existantes, cf.
 * "$brandcolor" plus haut dans cette fonction).
 *
 * @param string $inner contenu interne du <svg> (voir tableau ci-dessus)
 * @return string data URI complète, prête pour "mask: url(...)"
 */
function theme_obin_svg_icon_data_uri($inner) {
    $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" '
        . 'fill="none" stroke="currentColor" stroke-width="1.8" '
        . 'stroke-linecap="round" stroke-linejoin="round">' . $inner . '</svg>';

    return 'data:image/svg+xml,' . rawurlencode($svg);
}

/**
 * Génère les règles CSS des pictogrammes des menus d'administration et du
 * menu secondaire de la page de cours, selon le réglage
 * "theme_obin/menuicons" (voir settings.php) :
 * - "none" : aucune règle générée (comportement natif de Boost, texte seul) ;
 * - "emoji" : jeu d'emoji (apparence historique du thème, conservée par
 *   défaut pour ne rien changer aux installations existantes) ;
 * - "svg" : pictogrammes SVG minimalistes dessinés pour ce thème (voir
 *   theme_obin_get_menu_svg_icons()), en "currentColor".
 *
 * Un seul tableau de correspondance pour les deux modes (emoji/svg), afin
 * que les deux couvrent exactement le même ensemble de liens sans jamais
 * diverger l'un de l'autre au fil des évolutions futures.
 *
 * @param theme_config $theme
 * @return string
 */
function theme_obin_get_menu_icons_scss($theme) {
    $mode = !empty($theme->settings->menuicons) ? $theme->settings->menuicons : 'svg';

    if ($mode === 'none') {
        return '';
    }

    // Sélecteur CSS => [emoji (mode "emoji"), clé d'icône (mode "svg")].
    $map = [
        '.nav-tabs .nav-link[href$="#linkroot"]' => ['⚙️ ', 'gear'],
        '.nav-tabs .nav-link[href$="#linkusers"]' => ['👤 ', 'users'],
        '.nav-tabs .nav-link[href$="#linkcourses"]' => ['📚 ', 'book'],
        '.nav-tabs .nav-link[href$="#linkgrades"]' => ['📊 ', 'chartbar'],
        '.nav-tabs .nav-link[href$="#linkmodules"]' => ['🧩 ', 'grid'],
        '.nav-tabs .nav-link[href$="#linkappearance"]' => ['🎨 ', 'palette'],
        '.nav-tabs .nav-link[href$="#linkserver"]' => ['🖥️ ', 'server'],
        '.nav-tabs .nav-link[href$="#linkreports"]' => ['📈 ', 'chartline'],
        '.nav-tabs .nav-link[href$="#linkdevelopment"]' => ['🛠️ ', 'wrench'],
        '.nav-tabs .nav-link[href*="course/view.php"]' => ['🏠 ', 'home'],
        '.nav-tabs .nav-link[href*="section=frontpagesettings"], .nav-tabs .nav-link[href*="course/edit.php"]'
            => ['⚙️ ', 'gear'],
        '.nav-tabs .nav-link[href*="user/index.php"]' => ['👥 ', 'users'],
        '.nav-tabs .nav-link[href*="report/view.php"]' => ['📈 ', 'chartline'],
        '.nav-tabs .nav-link[href*="question/edit.php"]' => ['❓ ', 'question'],
        '#site-news-forum h2' => ['📢 ', 'megaphone'],
        'a[href*="mod/forum/subscribe.php"]' => ['🔔 ', 'bell'],
        'a[href*="report/competency/index.php"]' => ['🧠 ', 'lightbulb'],
        'a[href*="report/log/index.php"]' => ['📜 ', 'document'],
        'a[href*="report/loglive/index.php"]' => ['🔴 ', 'dotlive'],
        'a[href*="report/outline/index.php"]' => ['📋 ', 'clipboard'],
        'a[href*="report/participation/index.php"]' => ['🙋 ', 'chat'],
        'a[href*="tool/monitor/managerules.php"]' => ['🚨 ', 'alerttriangle'],
    ];

    $scss = "\n/*\n * Pictogrammes des menus d'administration (réglage \"theme_obin/menuicons\").\n */\n";

    if ($mode === 'emoji') {
        foreach ($map as $selector => $pair) {
            $scss .= $selector . '::before { content: "' . $pair[0] . '"; }' . "\n";
        }
        return $scss;
    }

    // Mode "svg" : chaque icône n'est encodée qu'une seule fois (mise en
    // cache locale), même si plusieurs sélecteurs la réutilisent (ex. la clé
    // "gear" sert à la fois pour #linkroot et pour les réglages de cours).
    $icons = theme_obin_get_menu_svg_icons();
    $datauris = [];
    foreach ($map as $selector => $pair) {
        $iconkey = $pair[1];
        if (!isset($icons[$iconkey])) {
            continue;
        }
        if (!isset($datauris[$iconkey])) {
            $datauris[$iconkey] = theme_obin_svg_icon_data_uri($icons[$iconkey]);
        }
        $uri = $datauris[$iconkey];
        $scss .= $selector . '::before {'
            . ' content: "";'
            . ' display: inline-block;'
            . ' width: 1em;'
            . ' height: 1em;'
            . ' margin-right: .35em;'
            . ' vertical-align: -0.15em;'
            . ' background-color: currentColor;'
            . ' -webkit-mask: url("' . $uri . '") center / contain no-repeat;'
            . ' mask: url("' . $uri . '") center / contain no-repeat;'
            . ' }' . "\n";
    }

    return $scss;
}

// Google Fonts injection is now handled via the Hook API.
// See classes/hook/output/before_standard_html_head.php and db/hooks.php.

/**
 * Returns the URL of the front-page banner photo: the one uploaded by the
 * administrator (Appearance > OBIN > "Banner photo") if set, otherwise the
 * default photo bundled with the theme (pix/hero.jpg).
 *
 * @return string
 */
function theme_obin_get_hero_image_url() {
    global $OUTPUT;

    $theme = theme_config::load('obin');
    $herourl = $theme->setting_file_url('heroimage', 'heroimage');
    if (!empty($herourl)) {
        return $herourl;
    }

    return $OUTPUT->image_url('hero', 'theme_obin')->out(false);
}

/**
 * Serves the banner photo file uploaded by the administrator
 * (setting "heroimage", see settings.php), following the same mechanism as
 * "backgroundimage" in theme_boost.
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param context $context
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @param array $options
 * @return bool
 */
function theme_obin_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = []) {
    if ($context->contextlevel == CONTEXT_SYSTEM && $filearea === 'heroimage') {
        $theme = theme_config::load('obin');
        if (!array_key_exists('cacheability', $options)) {
            $options['cacheability'] = 'public';
        }
        return $theme->setting_file_serve($filearea, $args, $forcedownload, $options);
    } else {
        send_file_not_found();
    }
}

/**
 * Retourne les liens réseaux sociaux avec icône SVG détectée automatiquement.
 * @return array List of ['url' => string, 'icon' => string, 'label' => string]
 */
function theme_obin_get_social_links() {
    $raw = get_config('theme_obin', 'footersocial');
    if (empty($raw)) {
        return [];
    }
    $icons = [
        'linkedin' => ['label' => 'LinkedIn', 'svg' => '<svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>'],
        'facebook' => ['label' => 'Facebook', 'svg' => '<svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>'],
        'youtube'  => ['label' => 'YouTube',  'svg' => '<svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M23.495 6.205a3.007 3.007 0 00-2.088-2.088c-1.87-.501-9.396-.501-9.396-.501s-7.507-.01-9.396.501A3.007 3.007 0 00.527 6.205a31.247 31.247 0 00-.522 5.805 31.247 31.247 0 00.522 5.783 3.007 3.007 0 002.088 2.088c1.868.502 9.396.502 9.396.502s7.506 0 9.396-.502a3.007 3.007 0 002.088-2.088 31.247 31.247 0 00.5-5.783 31.247 31.247 0 00-.5-5.805zM9.609 15.601V8.408l6.264 3.602z"/></svg>'],
    ];

    $links = [];
    foreach (preg_split('/\r\n|\r|\n/', $raw) as $url) {
        $url = trim($url);
        if (empty($url)) continue;
        $icon_html = '<svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M10 13a5 5 0 007.54.54l3-3a5 5 0 00-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 00-7.54-.54l-3 3a5 5 0 007.07 7.07l1.71-1.71"/></svg>';
        $label = 'Lien';
        foreach ($icons as $key => $data) {
            if (strpos($url, $key) !== false) {
                $icon_html = $data['svg'];
                $label = $data['label'];
                break;
            }
        }
        $links[] = ['url' => clean_param($url, PARAM_URL), 'icon' => $icon_html, 'label' => $label];
    }
    return $links;
}

/**
 * Returns footer links as configured by the administrator (setting "footerlinks",
 * see settings.php): one link per line, in "Label|URL" format. Intentionally
 * simple (no repeated sub-fields, not natively supported by Moodle's settings API)
 * so that any organisation using this theme can add its own links (legal notices,
 * privacy policy, social media...) without touching any code.
 *
 * @return array List of ['label' => string, 'url' => string]
 */
function theme_obin_get_footer_links() {
    $raw = get_config('theme_obin', 'footerlinks');
    if (empty($raw)) {
        return [];
    }

    $links = [];
    foreach (preg_split('/\r\n|\r|\n/', $raw) as $line) {
        $line = trim($line);
        if ($line === '' || strpos($line, '|') === false) {
            continue;
        }
        [$label, $url] = array_map('trim', explode('|', $line, 2));
        if ($label === '' || $url === '') {
            continue;
        }
        $links[] = [
            'label' => format_string($label),
            'url' => clean_param($url, PARAM_URL),
        ];
    }

    return $links;
}

/**
 * Cleans and formats a free-text content block from the front page (settings
 * "frontblock1"/"frontblock2", see settings.php): basic HTML formatting
 * (paragraphs, links...), not a full rich editor — sufficient for a short
 * description, a link, or a key figure.
 *
 * @param string|null $raw
 * @return string
 */
function theme_obin_format_frontblock($raw) {
    if (empty($raw)) {
        return '';
    }

    return format_text($raw, FORMAT_MARKDOWN, ['context' => \context_system::instance()]);
}

/**
 * Returns whether self-registration (account creation) is actually available
 * on this site: uses the same check as Moodle core on the login page
 * (authentication method configured in $CFG->registerauth that supports
 * self-registration).
 *
 * @return bool
 */
function theme_obin_signup_available() {
    global $CFG;

    if (empty($CFG->registerauth)) {
        return false;
    }

    $authplugin = get_auth_plugin($CFG->registerauth);

    return $authplugin->can_signup();
}
