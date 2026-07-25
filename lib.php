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
 * SCSS injecté AVANT la compilation du preset Boost : c'est ici qu'on
 * remplace les variables Bootstrap par défaut par celles de la charte
 * graphique officielle "Citoyenneté et Inclusion Numérique"
 * (Charte_Graphique_OBIN.pdf v1.0, juin 2026). Couleurs et police
 * configurables depuis Administration du site > Apparence > OBIN
 * (voir settings.php), avec ces valeurs de la charte comme défaut.
 *
 * @param theme_config $theme
 * @return string
 */
function theme_obin_get_pre_scss($theme) {
    $scss = '';

    $brandcolor = !empty($theme->settings->brandcolor) ? $theme->settings->brandcolor : '#3D8FE8';
    $secondarycolor = !empty($theme->settings->secondarycolor) ? $theme->settings->secondarycolor : '#64D6A8';

    // Couleurs primaires de la charte.
    $scss .= '$primary: ' . $brandcolor . ";\n";       // Bleu OBIN.
    $scss .= '$success: ' . $secondarycolor . ";\n";   // Vert-Teal.
    $scss .= '$info: #4CBFD1;' . "\n";                 // Cyan Accent.

    // Neutres de la charte : noir texte (jamais #000000 pur), gris clair
    // pour les fonds de section/cartes.
    $scss .= '$body-color: #111827;' . "\n";
    $scss .= '$gray-100: #F3F4F6;' . "\n";
    $scss .= '$gray-700: #374151;' . "\n";
    $scss .= '$gray-600: #9CA3AF;' . "\n";

    // Typographie de la charte : Poppins pour les titres, Lato pour le
    // corps de texte. Chargées depuis Google Fonts (voir extrascss ci-dessous
    // pour l'import @import, requis avant utilisation des font-family).
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
 * SCSS injecté APRÈS la compilation du preset Boost : règles complémentaires
 * qui ne sont pas de simples variables Bootstrap (import de police, dégradé
 * de marque, amélioration d'accessibilité du focus clavier).
 *
 * @param theme_config $theme
 * @return string
 */
function theme_obin_get_extra_scss($theme) {
    $brandcolor = !empty($theme->settings->brandcolor) ? $theme->settings->brandcolor : '#3D8FE8';
    $secondarycolor = !empty($theme->settings->secondarycolor) ? $theme->settings->secondarycolor : '#64D6A8';

    $content = '
/*
 * Dégradé officiel teal -> bleu (sens inversé à la demande, cf. mission
 * OBIN). La charte réserve ce dégradé "au logo et aux éléments graphiques
 * forts" (titres, boutons CTA, accents) - pas d\'usage généralisé. Appliqué
 * donc seulement à la barre de navigation et aux boutons primaires. Le
 * dégradé de la barre est animé légèrement ("effet wouah" demandé sur le
 * menu) via un fond agrandi (200%) dont on fait glisser la position.
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
 * Le logo (Administration du site > Apparence > Logos) est fourni par
 * chaque structure qui installe ce thème : format et proportions varient
 * (logo très large, très haut, carré...). Seule la TAILLE est bornée
 * (objet contenu dans un gabarit fixe, proportions gardées).
 *
 * Dans la navbar, le logo repose sur le dégradé de marque : quelle que
 * soit sa couleur d\'origine (le fichier déposé peut être sombre, coloré...),
 * on le fait passer en silhouette BLANCHE via un filtre CSS
 * (brightness(0) = tout en noir, invert(1) = noir -> blanc), pour qu\'il
 * reste lisible sur n\'importe quelle portion du dégradé animé, cohérent
 * avec le texte de la navbar déjà forcé en blanc plus bas. Même technique
 * déjà utilisée pour le logo Moodle du pied de page (.obin-footer-moodlelogo).
 * Le fond doit être transparent (PNG) pour un rendu propre - un fond blanc
 * opaque deviendrait un simple rectangle blanc plein une fois inversé.
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
 * Page de connexion : fond BLANC (cf. règles plus bas), pas le dégradé - on
 * y garde donc le logo dans ses couleurs d\'origine (le filtre blanc ci-dessus
 * le rendrait invisible sur un fond clair).
 */
body.pagelayout-login #logoimage,
body.pagelayout-login .login-logo img {
    max-height: 120px;
    max-width: 100%;
    width: auto;
    object-fit: contain;
}

/*
 * Contraste sur le dégradé : Boost part du principe d\'une navbar blanche
 * ("navbar-light") et colore ses liens/textes en bleu marque - ce qui les
 * rend illisibles (voire invisibles) une fois la navbar habillée du
 * dégradé de couleur. On force donc un texte blanc dans toute la navbar,
 * quelle que soit la portion du dégradé sur laquelle il se trouve.
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
 * "Mode d\'édition" signalé illisible malgré l\'ombre portée ci-dessus (la
 * portion la plus claire du dégradé animé reste trop proche du blanc pour
 * qu\'une simple ombre suffise). Fond sombre semi-transparent en plus,
 * pour un contraste garanti à tout instant de l\'animation, quelle que soit
 * la couleur exacte du dégradé à cet endroit.
 */
.navbar.fixed-top .editmode-switch-form {
    background-color: rgba(17, 24, 39, .55);
    border-radius: 6px;
    padding: .25rem .6rem;
}

/*
 * Un vieux style coeur Moodle générique (":focus") pose un fond BLANC uni
 * sur n\'importe quel élément au focus (ex. le lien "Connexion", qui n\'est
 * ni ".nav-link" ni ".dropdown-menu a" et n\'était donc couvert par aucune
 * des règles ci-dessus) : combiné à notre texte blanc, ça devient
 * illisible au focus/clic. On neutralise ce fond blanc dans toute la
 * navbar et on retombe sur le même style de focus que les onglets du menu.
 */
.navbar.fixed-top a:focus {
    background-color: rgba(255, 255, 255, .18);
    color: #fff;
    outline: none;
    box-shadow: inset 0 0 0 3px rgba(255, 255, 255, .9);
}

/*
 * Les menus déroulants et popovers (notifications "cloche", messages, menu
 * utilisateur "JG"...) sont imbriqués dans <nav class="navbar fixed-top"> au
 * niveau du DOM, mais s\'affichent en pop-up sur fond BLANC, pas sur le
 * dégradé. Les règles ci-dessus (texte blanc + ombre dans toute la navbar)
 * les rendaient entièrement illisibles : texte blanc sur fond blanc. On leur
 * restitue une couleur de texte normale et on retire l\'ombre, quel que soit
 * l\'état (repos/survol/focus). Deux composants différents à couvrir :
 * ".dropdown-menu" (Bootstrap, ex. menu utilisateur) et
 * ".popover-region-container" (composant Moodle, panneau du popover
 * notifications/messages - PAS ".popover-region" tout court, qui englobe
 * aussi le bouton "cloche"/"messages" lui-même : ce bouton, lui, doit rester
 * blanc puisqu\'il est posé sur le dégradé, pas sur un fond blanc - bug
 * initialement constaté sur l\'icône de la cloche, invisible une fois
 * capturée à tort par cette règle).
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
 * Le survol/focus des onglets du menu (.moremenu) hérite d\'une règle
 * coeur Moodle qui pose un fond gris clair ($gray-100, #F3F4F6) et une
 * bordure active bleue - lisibles sur une navbar blanche, mais illisibles
 * ici : texte blanc sur fond quasi-blanc, et bordure bleue invisible sur
 * la portion bleue du dégradé. On remplace par un survol blanc translucide
 * qui reste cohérent quel que soit l\'endroit du dégradé.
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
 * Pictos de la navbar (langue, bascule menu, etc.) : la règle générale plus
 * bas (".icon" en bleu marque) les rendrait bleus sur fond bleu - illisible.
 * On les garde blancs ici, quelle que soit la portion du dégradé.
 */
.navbar.fixed-top .icon,
.navbar.fixed-top .dropdown-toggle::after {
    color: #fff;
}

/*
 * Effet "wouah" sur le menu principal : léger effet de lévitation au survol
 * et soulignement animé en blanc (plus lisible que le bleu par défaut sur
 * un fond dégradé coloré, notamment sur sa portion bleue).
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
     * Boost pose "color: rgba(0,0,0,.9)" (texte quasi noir) sur l\'onglet
     * actif via ".navbar-light .navbar-nav .nav-link.active" - plus
     * spécifique que nos règles de couleur blanche ci-dessus. !important
     * ici plutôt que d\'empiler encore des classes, ce cas précis étant
     * ponctuel et bien identifié (contrairement au reste de la feuille).
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
 * Sur la page d\'accueil, le titre "Theme Obin" (nom du site, affiché par
 * défaut par Boost via le bandeau de page standard) fait doublon avec le
 * titre de la bannière ci-dessous : on le masque uniquement quand la
 * bannière s\'affiche réellement (classe "obin-hero-active", posée par
 * layout/frontpage.php seulement pour les visiteurs non connectés - donc
 * sans effet sur le reste du site ni sur un éventuel accès connecté à cette
 * même page). On referme aussi l\'espace ainsi libéré en haut de page
 * (".main-inner" a son propre padding/margin-top de 24px chacun, prévus
 * pour aérer sous le titre normalement affiché - un blanc vide subsistait
 * sinon entre la navbar et la bannière).
 */
body.obin-hero-active .page-context-header {
    display: none;
}
body.obin-hero-active #topofscroll.main-inner {
    padding-top: 0;
    margin-top: 0;
}

/*
 * Bannière (hero) de la page d\'accueil pour les visiteurs non connectés :
 * photo + overlay dégradé de marque + slogan officiel de la charte,
 * au-dessus du contenu habituel (résumé du site + cours disponibles) qui
 * reste inchangé en dessous. Pleine largeur de viewport et responsive :
 * la technique margin/width en calc() fait sortir la bannière de toute
 * largeur maximale imposée par les conteneurs parents (".limitedwidth"...),
 * quel que soit leur niveau d\'imbrication.
 * Cf. layout/frontpage.php et templates/frontpage.mustache.
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
 * Mini-formulaire de connexion accessible directement depuis le menu de la
 * page d\'accueil (au lieu de rediriger vers /login/index.php) : panneau
 * masqué par défaut, affiché/masqué en cliquant sur le lien "Connexion" de
 * la navbar (cf. templates/frontpage.mustache pour le script de bascule).
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

/**
 * Injecte les liens Google Fonts dans le <head>.
 *
 * Ne PAS faire cet import via `@import url(...)` dans le SCSS : scssphp
 * (bibliothèque utilisée par ce Moodle) ne reconnaît pas de façon fiable un
 * `@import url("https://...")` ajouté via append_raw_scss() comme un simple
 * passthrough CSS - il tente de le résoudre comme un fichier SCSS local, ce
 * qui fait échouer silencieusement toute la compilation (get_css_content_from_scss()
 * catch l'exception et retombe sur le CSS par défaut de Boost, sans erreur
 * visible). Résultat observé : plus aucune personnalisation du thème
 * (couleurs, dégradé, polices) n'était appliquée. D'où l'injection en HTML.
 *
 * @return string
 */
function theme_obin_before_standard_html_head() {
    return '<link rel="preconnect" href="https://fonts.googleapis.com">'
        . '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>'
        . '<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;700&family=Lato:wght@400;700&display=swap">';
}

/**
 * URL de la photo de bannière de la page d'accueil : celle déposée par
 * l'administrateur (Apparence > OBIN > "Photo de bannière") si elle existe,
 * sinon la photo par défaut d'OBIN fournie avec le thème (pix/hero.jpg) -
 * remplacée dès qu'une structure dépose la sienne.
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
 * Sert le fichier de la photo de bannière déposée par l'administrateur
 * (réglage "heroimage", cf. settings.php), selon le même mécanisme que
 * "backgroundimage" dans theme_boost.
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
 * Liens du pied de page, tels que configurés par l'administrateur (réglage
 * "footerlinks", cf. settings.php) : une ligne par lien, au format
 * "Libellé|URL". Format volontairement simple (pas de sous-champs répétés,
 * non supportés nativement par l'API de réglages Moodle) pour que
 * n'importe quelle structure utilisant ce thème puisse ajouter ses propres
 * liens (mentions légales, politique de confidentialité, réseaux sociaux...)
 * sans toucher au code.
 *
 * @return array Liste de ['label' => string, 'url' => string]
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
 * Nettoie/formate un bloc de contenu libre de la page d'accueil (réglages
 * "frontblock1"/"frontblock2", cf. settings.php) : simple mise en forme
 * HTML (paragraphes, liens...), pas un éditeur riche complet - suffisant
 * pour un texte de présentation, un lien, un chiffre-clé...
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
 * Indique si l\'auto-inscription (création de compte) est réellement
 * disponible sur ce site : même vérification que celle utilisée par le
 * coeur de Moodle sur la page de connexion (méthode d\'authentification
 * configurée dans $CFG->registerauth, et qui supporte l\'auto-inscription).
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
