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

namespace theme_obin\output;

/**
 * Renderer surchargé du thème OBIN.
 *
 * Seul ajout par rapport à theme_boost : obin_footer(), qui construit le
 * pied de page visible du site (cf. templates/footer_content.mustache),
 * utilisable depuis n'importe quel template via {{{ output.obin_footer }}}.
 * Ce mécanisme (classe core_renderer dans le namespace theme_<nom>\output)
 * est reconnu automatiquement par la "theme_overridden_renderer_factory"
 * déjà activée dans config.php - aucun câblage supplémentaire nécessaire.
 *
 * @package    theme_obin
 * @copyright  2026 Citoyenneté et Inclusion Numérique (OBIN) <contact@obin-asso.fr>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_renderer extends \theme_boost\output\core_renderer {

    /**
     * Pied de page visible du site : nom du site, accroche, e-mail de
     * contact et liens, tous personnalisables depuis Administration du
     * site > Apparence > OBIN. Affiché sur toutes les pages (cf.
     * templates/theme_boost/footer.mustache, qui surcharge le footer de
     * Boost pour l'ensemble du site).
     *
     * @return string
     */
    public function obin_footer() {
        global $SITE;

        $tagline = get_config('theme_obin', 'footertagline');
        $email = get_config('theme_obin', 'footeremail');

        $links = theme_obin_get_footer_links();

        $context = [
            'sitename' => format_string($SITE->fullname, true, ['context' => \context_system::instance()]),
            'tagline' => $tagline ? format_string($tagline) : '',
            'email' => $email ?: '',
            'links' => $links,
            'haslinks' => !empty($links),
            'year' => date('Y'),
            // Official Moodle logo ("powered by"), resolved via the renderer's
            // image_url rather than a hard-coded path: stays correct regardless
            // soit la structure de dossiers de la version de Moodle (cf.
            // of the "public/" restructure in 5.x, verified without impact here
            // precisely because we go through this mechanism).
            'moodlelogourl' => $this->image_url('moodlelogo', 'core')->out(false),
        ];

        return $this->render_from_template('theme_obin/footer_content', $context);
    }
}
