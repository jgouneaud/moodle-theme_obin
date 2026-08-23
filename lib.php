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

    $content = '
/*
 * Official teal → blue gradient (direction reversed per OBIN's brand guidelines).
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
    object-fit: contain;
    filter: brightness(0) invert(1);
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
    color: #fff;
}
.navbar.fixed-top .nav-link:hover,
.navbar.fixed-top a:hover {
    color: #fff;
    opacity: .85;
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
.navbar.fixed-top .nav-link.active:hover,
.navbar.fixed-top .nav-link.active:focus {
    background-color: rgba(255, 255, 255, .18);
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
    background: #fff;
    box-shadow: 0 0 8px rgba(255, 255, 255, .8);
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
body.obin-hero-active .page-context-header {
    display: none;
}
body.obin-hero-active #topofscroll.main-inner {
    padding-top: 0;
    margin-top: 0;
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
    background: linear-gradient(135deg, rgba(61, 143, 232, .85) 0%, rgba(100, 214, 168, .78) 100%);
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
    box-shadow: inset 0 0 0 3px rgba(255, 255, 255, .9);
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
    color: #3D90E7;
}
a:hover,
a:focus {
    color: #1B70D2;
}

/*
 * Couleur des menus à onglets (ex. onglets "Général / Utilisateurs / Cours..."
 * d\'Administration du site, ".nav-tabs" Bootstrap).
 */
.nav-tabs .nav-link {
    color: #1B70D2;
}
.nav-tabs .nav-link.active {
    color: #111827;
    border-bottom-color: #1B70D2;
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
 * Emoji sur les principales pages d\'Administration du site : ciblage par
 * ancre (#linkxxx, stable, générée par Moodle core pour chaque catégorie de
 * réglages) plutôt que par le texte des chaînes de langue - évite de devoir
 * surcharger les chaînes core (mécanisme plus lourd, Admin > Langue >
 * Personnalisation) pour un simple ajout visuel.
 */
.nav-tabs .nav-link[href$="#linkroot"]::before { content: "⚙️ "; }
.nav-tabs .nav-link[href$="#linkusers"]::before { content: "👤 "; }
.nav-tabs .nav-link[href$="#linkcourses"]::before { content: "📚 "; }
.nav-tabs .nav-link[href$="#linkgrades"]::before { content: "📊 "; }
.nav-tabs .nav-link[href$="#linkmodules"]::before { content: "🧩 "; }
.nav-tabs .nav-link[href$="#linkappearance"]::before { content: "🎨 "; }
.nav-tabs .nav-link[href$="#linkserver"]::before { content: "🖥️ "; }
.nav-tabs .nav-link[href$="#linkreports"]::before { content: "📈 "; }
.nav-tabs .nav-link[href$="#linkdevelopment"]::before { content: "🛠️ "; }

/*
 * Emoji sur le menu secondaire de la page de cours (Accueil / Paramètres /
 * Participants / Rapports / Banque de questions / Plus). Ciblage par motif
 * dans l\'URL plutôt que par fragment #link (ce menu n\'en a pas) - chaque
 * lien pointe vers un script Moodle différent et identifiable.
 */
.nav-tabs .nav-link[href*="course/view.php"]::before { content: "🏠 "; }
.nav-tabs .nav-link[href*="section=frontpagesettings"]::before,
.nav-tabs .nav-link[href*="course/edit.php"]::before { content: "⚙️ "; }
.nav-tabs .nav-link[href*="user/index.php"]::before { content: "👥 "; }
.nav-tabs .nav-link[href*="report/view.php"]::before { content: "📈 "; }
.nav-tabs .nav-link[href*="question/edit.php"]::before { content: "❓ "; }
#site-news-forum h2::before { content: "📢 "; }

/*
 * Emoji sur le lien d\'abonnement au forum et sur le sous-menu "Plus" de la
 * page de cours (rapports, journaux, règles de surveillance...). Ciblage
 * par motif dans l\'URL, comme ci-dessus.
 */
a[href*="mod/forum/subscribe.php"]::before { content: "🔔 "; }
a[href*="report/competency/index.php"]::before { content: "🧠 "; }
a[href*="report/log/index.php"]::before { content: "📜 "; }
a[href*="report/loglive/index.php"]::before { content: "🔴 "; }
a[href*="report/outline/index.php"]::before { content: "📋 "; }
a[href*="report/participation/index.php"]::before { content: "🙋 "; }
a[href*="tool/monitor/managerules.php"]::before { content: "🚨 "; }

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
    background: #27507B;
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
';

    if (!empty($theme->settings->scss)) {
        $content .= $theme->settings->scss;
    }

    return $content;
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
