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
 * Theme OBIN - version information.
 *
 * Thème enfant de Boost pour l'association "Citoyenneté et Inclusion
 * Numérique" (OBIN), association loi 1901 engagée pour l'inclusion
 * numérique. Ce thème n'ajoute aucune fonctionnalité : il applique la
 * charte graphique officielle de l'association (couleurs, typographie)
 * par-dessus le thème Boost, sans modifier ses gabarits.
 *
 * @package    theme_obin
 * @copyright  2026 Citoyenneté et Inclusion Numérique (OBIN) <contact@obin-asso.fr>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$plugin->component = 'theme_obin';
$plugin->version   = 2026072601;
// Compatibilité réellement vérifiée (installations de test dédiées,
// thème activé, tableau de bord + administration + page d'accueil
// contrôlés sans erreur) : Moodle 4.3, 4.5 et 5.2 - aucune adaptation de
// code n'a été nécessaire entre ces trois versions. Les versions
// intermédiaires (4.4, 5.0, 5.1) n'ont pas été testées mais sont
// probablement compatibles, aucun changement cassant observé sur
// l'intervalle couvert. Point notable pour Moodle 5.x : la séparation
// du code servi dans un dossier "public/" (cf. UPGRADING.md de Moodle) ne
// concerne que la configuration du serveur web, pas le code du thème
// lui-même - theme_obin n'a besoin d'aucune modification pour ça.
$plugin->requires  = 2023100900; // Moodle 4.3 (plus ancienne version testée).
$plugin->maturity  = MATURITY_BETA;
$plugin->release   = '1.0.0';
$plugin->dependencies = [
    'theme_boost' => 2023100900,
];
