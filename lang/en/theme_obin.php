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
 * Theme OBIN - English language strings.
 *
 * @package    theme_obin
 * @copyright  2026 Citoyenneté et Inclusion Numérique (OBIN) <contact@obin-asso.fr>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'OBIN';
$string['choosereadme'] = 'OBIN is a Boost child theme built for "Citoyenneté et Inclusion Numérique" (OBIN), a French non-profit association working on digital inclusion. It applies the association\'s official graphic charter (colours, typography) on top of Boost\'s layouts, without modifying them.';
$string['configtitle'] = 'OBIN settings';
$string['region-side-pre'] = 'Right';
$string['brandcolor'] = 'Primary colour';
$string['brandcolor_desc'] = 'Main brand colour (default: OBIN blue #3D8FE8, from the official graphic charter).';
$string['secondarycolor'] = 'Secondary colour';
$string['secondarycolor_desc'] = 'Secondary brand colour (default: OBIN teal #64D6A8, from the official graphic charter).';
$string['scsspre'] = 'Raw initial SCSS';
$string['scsspre_desc'] = 'This SCSS code is injected before everything else, so it can override any Bootstrap/Boost default variable.';
$string['scss'] = 'Raw SCSS';
$string['scss_desc'] = 'This SCSS code is injected at the end of the compiled stylesheet.';
$string['heroimage'] = 'Banner photo (front page)';
$string['heroimage_desc'] = 'Photo shown as a banner on the front page, for logged-out visitors only. If no photo is uploaded here, a default photo bundled with the theme is used. Recommended size: at least 1600 × 500 px (wide ratio, about 3:1), JPG/PNG/WebP, ideally under 500 KB for fast loading. The photo is auto-cropped ("cover": fills the full width, fixed height) - avoid important subjects right at the top or bottom of the image.';
$string['heroheading'] = 'Banner title';
$string['heroheading_desc'] = 'Large title shown on top of the front page banner photo.';
$string['herosubheading'] = 'Banner subtitle / tagline';
$string['herosubheading_desc'] = 'Text shown below the banner title (defaults to the official tagline from the graphic charter).';
$string['footertagline'] = 'Footer tagline';
$string['footertagline_desc'] = 'Short sentence shown under the site name in the footer (optional).';
$string['footeremail'] = 'Contact email (footer)';
$string['footeremail_desc'] = 'Email address shown as a clickable link in the footer (optional).';
$string['footerlinks'] = 'Footer links';
$string['footerlinks_desc'] = 'One link per line, formatted as "Label|URL" (e.g. Privacy policy|https://example.com/privacy). Use this to add links such as legal notices, privacy policy, or social media, without touching any code.';
$string['frontblock1'] = 'Content block 1 (front page)';
$string['frontblock1_desc'] = 'Content shown in the left column below the available courses list, for logged-out visitors only (e.g. organisation presentation, mission, key figures...). Simple formatting: plain text, paragraphs (blank line = new paragraph), Markdown links [text](url). Empty by default.';
$string['frontblock2'] = 'Content block 2 (front page)';
$string['frontblock2_desc'] = 'Same as block 1, shown in the right column. Empty by default.';
$string['privacy:metadata'] = 'The OBIN theme does not store any personal data.';
