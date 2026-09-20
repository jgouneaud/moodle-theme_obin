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
 * Theme OBIN - script d'installation.
 *
 * @package    theme_obin
 * @copyright  2026 Citoyenneté et Inclusion Numérique (OBIN) <contact@obin-asso.fr>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Dépose le logo OBIN par défaut (Administration du site > Apparence >
 * Logos) à la première installation du thème, UNIQUEMENT si aucun logo
 * n'est déjà configuré (ne remplace jamais un logo existant, y compris
 * lors d'une réinstallation/mise à jour). Utilise les mêmes réglages coeur
 * "core_admin/logo" et "core_admin/logocompact" que n'importe quel logo
 * déposé manuellement dans Apparence > Logos - une structure qui dépose le
 * sien via cette page remplace donc simplement celui-ci normalement.
 */
function xmldb_theme_obin_install() {
    global $CFG;

    $fs = get_file_storage();
    $syscontext = context_system::instance();

    $logopath = $CFG->dirroot . '/theme/obin/pix/logo.png';
    if (!is_readable($logopath)) {
        return true;
    }

    $areas = [
        'logo' => get_config('core_admin', 'logo'),
        'logocompact' => get_config('core_admin', 'logocompact'),
    ];

    foreach ($areas as $filearea => $currentvalue) {
        if (!empty($currentvalue)) {
            // A logo is already configured (uploaded manually or by a
            // previous upgrade): leave it untouched.
            continue;
        }

        $fs->delete_area_files($syscontext->id, 'core_admin', $filearea, 0);

        $filerecord = [
            'contextid' => $syscontext->id,
            'component' => 'core_admin',
            'filearea'  => $filearea,
            'itemid'    => 0,
            'filepath'  => '/',
            'filename'  => 'logo.png',
        ];
        $fs->create_file_from_pathname($filerecord, $logopath);

        set_config($filearea, '/logo.png', 'core_admin');
    }

    return true;
}
