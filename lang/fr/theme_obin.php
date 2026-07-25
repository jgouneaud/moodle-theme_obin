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
 * Theme OBIN - chaînes de langue françaises.
 *
 * @package    theme_obin
 * @copyright  2026 Citoyenneté et Inclusion Numérique (OBIN) <contact@obin-asso.fr>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'OBIN';
$string['choosereadme'] = 'OBIN est un thème enfant de Boost, réalisé pour l\'association loi 1901 "Citoyenneté et Inclusion Numérique" (OBIN), engagée pour l\'inclusion numérique. Il applique la charte graphique officielle de l\'association (couleurs, typographie) par-dessus les gabarits de Boost, sans les modifier.';
$string['configtitle'] = 'Réglages OBIN';
$string['region-side-pre'] = 'Droite';
$string['brandcolor'] = 'Couleur primaire';
$string['brandcolor_desc'] = 'Couleur de marque principale (par défaut : Bleu OBIN #3D8FE8, charte graphique officielle).';
$string['secondarycolor'] = 'Couleur secondaire';
$string['secondarycolor_desc'] = 'Couleur de marque secondaire (par défaut : Vert-Teal #64D6A8, charte graphique officielle).';
$string['scsspre'] = 'SCSS brut initial';
$string['scsspre_desc'] = 'Ce code SCSS est injecté avant tout le reste : il permet de redéfinir n\'importe quelle variable Bootstrap/Boost.';
$string['scss'] = 'SCSS brut';
$string['scss_desc'] = 'Ce code SCSS est injecté à la fin de la feuille de style compilée.';
$string['heroimage'] = 'Photo de bannière (page d\'accueil)';
$string['heroimage_desc'] = 'Photo affichée en bandeau sur la page d\'accueil, pour les visiteurs non connectés uniquement. Si aucune photo n\'est déposée ici, une photo par défaut fournie avec le thème est utilisée. Taille recommandée : au moins 1600 × 500 px (ratio large, environ 3:1), format JPG/PNG/WebP, poids conseillé sous 500 Ko pour un chargement rapide. La photo est recadrée automatiquement en "cover" (remplit toute la largeur, hauteur fixe) : évitez les sujets importants tout en haut ou en bas de l\'image.';
$string['heroheading'] = 'Titre de la bannière';
$string['heroheading_desc'] = 'Titre affiché en grand sur la photo de bannière de la page d\'accueil.';
$string['herosubheading'] = 'Sous-titre / slogan de la bannière';
$string['herosubheading_desc'] = 'Texte affiché sous le titre de la bannière (par défaut, le slogan officiel de la charte graphique).';
$string['footertagline'] = 'Accroche du pied de page';
$string['footertagline_desc'] = 'Courte phrase affichée sous le nom du site dans le pied de page (facultatif).';
$string['footeremail'] = 'E-mail de contact (pied de page)';
$string['footeremail_desc'] = 'Adresse e-mail affichée en lien cliquable dans le pied de page (facultatif).';
$string['footerlinks'] = 'Liens du pied de page';
$string['footerlinks_desc'] = 'Un lien par ligne, au format "Libellé|URL" (ex. : Mentions légales|https://exemple.fr/mentions-legales). Permet d\'ajouter des liens comme les mentions légales, la politique de confidentialité ou les réseaux sociaux, sans toucher au code.';
$string['frontblock1'] = 'Bloc de contenu 1 (page d\'accueil)';
$string['frontblock1_desc'] = 'Contenu affiché en colonne gauche sous la liste des cours disponibles, pour les visiteurs non connectés uniquement (ex. : présentation de la structure, mission, chiffres-clés...). Mise en forme simple : texte, paragraphes (ligne vide = nouveau paragraphe), liens en Markdown [texte](url). Vide par défaut.';
$string['frontblock2'] = 'Bloc de contenu 2 (page d\'accueil)';
$string['frontblock2_desc'] = 'Identique au bloc 1, affiché en colonne droite. Vide par défaut.';
$string['privacy:metadata'] = 'Le thème OBIN ne stocke aucune donnée personnelle.';
