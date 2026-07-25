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
 * Theme OBIN - configuration.
 *
 * Thème enfant de Boost : on ne redéfinit PAS $THEME->layouts dans son
 * ensemble. Moodle fait hériter automatiquement d'un thème enfant les
 * gabarits (layouts) de son parent quand ils ne sont pas explicitement
 * redéfinis dans son propre config.php (voir theme_config::__construct()
 * dans lib/outputlib.php - la fusion se fait clé par clé) - c'est le
 * fonctionnement standard des thèmes "enfants de Boost" les plus simples,
 * qui ne changent que la palette et la typographie sans toucher à la
 * structure des pages.
 *
 * Seule exception : le layout 'frontpage' (page d'accueil, visiteurs non
 * connectés) est surchargé pour y ajouter une bannière photo + slogan
 * (voir layout/frontpage.php et templates/frontpage.mustache). Toutes les
 * autres pages du site continuent d'utiliser les layouts de Boost tels quels.
 *
 * @package    theme_obin
 * @copyright  2026 Citoyenneté et Inclusion Numérique (OBIN) <contact@obin-asso.fr>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$THEME->name = 'obin';
$THEME->parents = ['boost'];
$THEME->sheets = [];
$THEME->editor_sheets = [];
$THEME->enable_dock = false;
$THEME->yuicssmodules = [];
$THEME->rarrow = '&#x25BA;';
$THEME->larrow = '&#x25C4;';
$THEME->requiredblocks = '';
$THEME->undeletableblocktypes = '';
$THEME->haseditswitch = true;
$THEME->usescourseindex = true;
$THEME->rendererfactory = 'theme_overridden_renderer_factory';

$THEME->layouts = [
    'frontpage' => [
        'file' => 'frontpage.php',
        'regions' => ['side-pre'],
        'defaultregion' => 'side-pre',
        'options' => ['nonavbar' => true],
    ],
];

$THEME->scss = function($theme) {
    return theme_obin_get_main_scss_content($theme);
};
$THEME->prescsscallback = 'theme_obin_get_pre_scss';
$THEME->extrascsscallback = 'theme_obin_get_extra_scss';
