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
 * Theme OBIN - réglages d'administration.
 *
 * @package    theme_obin
 * @copyright  2026 Citoyenneté et Inclusion Numérique (OBIN) <contact@obin-asso.fr>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {
    $settings = new admin_settingpage('themesettingobin', get_string('configtitle', 'theme_obin'));

    // Couleur primaire (Bleu OBIN par défaut, charte graphique officielle).
    $name = 'theme_obin/brandcolor';
    $title = get_string('brandcolor', 'theme_obin');
    $description = get_string('brandcolor_desc', 'theme_obin');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#3D8FE8');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // Couleur secondaire (Vert-Teal par défaut, charte graphique officielle).
    $name = 'theme_obin/secondarycolor';
    $title = get_string('secondarycolor', 'theme_obin');
    $description = get_string('secondarycolor_desc', 'theme_obin');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#64D6A8');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // SCSS brut avancé (avant compilation) - pour les besoins non couverts
    // par les réglages ci-dessus, mêmes conventions que le thème Boost.
    $name = 'theme_obin/scsspre';
    $title = get_string('scsspre', 'theme_obin');
    $description = get_string('scsspre_desc', 'theme_obin');
    $setting = new admin_setting_configtextarea($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // SCSS brut avancé (après compilation).
    $name = 'theme_obin/scss';
    $title = get_string('scss', 'theme_obin');
    $description = get_string('scss_desc', 'theme_obin');
    $setting = new admin_setting_configtextarea($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // Photo de bannière de la page d'accueil, déposée par l'administrateur.
    // Utilisée par layout/frontpage.php ; à défaut, on retombe sur la photo
    // par défaut fournie avec le thème (pix/hero.jpg).
    $name = 'theme_obin/heroimage';
    $title = get_string('heroimage', 'theme_obin');
    $description = get_string('heroimage_desc', 'theme_obin');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'heroimage', 0, [
        'maxfiles' => 1,
        'accepted_types' => ['.jpg', '.jpeg', '.png', '.webp'],
    ]);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // Titre et slogan affichés sur la bannière (par défaut : nom officiel et
    // slogan de la charte graphique).
    $name = 'theme_obin/heroheading';
    $title = get_string('heroheading', 'theme_obin');
    $description = get_string('heroheading_desc', 'theme_obin');
    $setting = new admin_setting_configtext($name, $title, $description, 'Citoyenneté et Inclusion Numérique', PARAM_TEXT);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    $name = 'theme_obin/herosubheading';
    $title = get_string('herosubheading', 'theme_obin');
    $description = get_string('herosubheading_desc', 'theme_obin');
    $setting = new admin_setting_configtext(
        $name,
        $title,
        $description,
        'Chaque citoyen mérite d\'être acteur de sa vie numérique.',
        PARAM_TEXT
    );
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // Pied de page visible, affiché sur tout le site (cf.
    // templates/theme_boost/footer.mustache et classes/output/core_renderer.php).
    $name = 'theme_obin/footertagline';
    $title = get_string('footertagline', 'theme_obin');
    $description = get_string('footertagline_desc', 'theme_obin');
    $setting = new admin_setting_configtext($name, $title, $description, '', PARAM_TEXT);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    $name = 'theme_obin/footeremail';
    $title = get_string('footeremail', 'theme_obin');
    $description = get_string('footeremail_desc', 'theme_obin');
    $setting = new admin_setting_configtext($name, $title, $description, '', PARAM_NOTAGS);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    $name = 'theme_obin/footerlinks';
    $title = get_string('footerlinks', 'theme_obin');
    $description = get_string('footerlinks_desc', 'theme_obin');
    $setting = new admin_setting_configtextarea($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // Deux blocs de contenu libre en deux colonnes, sous la liste des cours
    // disponibles de la page d'accueil (visiteurs non connectés). Vides par
    // défaut : le thème est un template réutilisable par n'importe quelle
    // structure, le contenu ne doit rien présumer d'une association ou d'un
    // organisme en particulier.
    $name = 'theme_obin/frontblock1';
    $title = get_string('frontblock1', 'theme_obin');
    $description = get_string('frontblock1_desc', 'theme_obin');
    $setting = new admin_setting_configtextarea($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    $name = 'theme_obin/frontblock2';
    $title = get_string('frontblock2', 'theme_obin');
    $description = get_string('frontblock2_desc', 'theme_obin');
    $setting = new admin_setting_configtextarea($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);
}
