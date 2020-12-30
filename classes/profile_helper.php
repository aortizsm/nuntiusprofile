<?php

defined('MOODLE_INTERNAL') || die();

/**
 *
 * @package    block
 * @subpackage nuntiusprofile
 * @copyright  2020 Alberto Ortiz
 * @author Alberto Ortiz Acevedo <alberto@aortiz.cl>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class profile_helper {

    private $name = 'nuntiusprofile';

    /**
     * Get string name from lang file
     * 
     * @author Alberto Ortiz Acevedo <alberto@aortiz.cl>
     * @param type $name
     * @param type $a
     * @return string
     */
    public function get_string($name, $a = null) {
        return get_string($name, 'block_' . $this->name, $a);
    }

    /**
     * Nombre del plugin
     * @return string
     */
    public function get_name() {
        return $this->name;
    }

    /**
     * Dir patch
     * @global type $CFG
     * @return type
     */
    public function get_dirpath() {
        global $CFG;
        return $CFG->dirroot . '/blocks/' . $this->name . '/';
    }

    /**
     * Get list of site roles names
     *
     * @param context $context
     * @param array $exclude
     * @return array
     */
    public function get_roles($context, $exclude = array()) {
        $return = array();

        $roles = get_all_roles($context);
        $rolenames = role_fix_names($roles, $context, ROLENAME_ORIGINAL);
        foreach ($rolenames as $role) {
            if (!in_array($role->id, $exclude)) {
                $return[$role->id] = $role->localname;
            }
        }
        return $return;
    }

    /**
     * Get last access in a nice way format when is 0 data
     * 
     * @author Alberto Ortiz Acevedo <alberto@aortiz.cl>
     * @param type $lastaccess
     * @return string
     */
    public function get_lastaccess($lastaccess) {

        $result = format_time($lastaccess);

        if ($lastaccess == 0) {
            $result = $this->get_string('lastaccess');
        }

        return $result;
    }

    /**
     * Render a html body with all data from user to the block
     * 
     * @global object $OUTPUT
     * @global object $COURSE
     * @global object $CFG
     * @param $USER $user
     * @return string
     */
    public function render_html($user) {

        global $OUTPUT, $COURSE, $CFG;

        $text = '<div class="block_profile">';
        $text .= '<div class="profileitem picture">';
        $text .= $OUTPUT->user_picture($user, array('courseid' => $COURSE->id, 'size' => '100%', 'class' => 'profilepicture'));
        $text .= '</div>';
        $text .= '<div class="profileitem fullname">' . fullname($user);
        $text .= '</div>';
        // Enviar mensaje
        $text .= '<div class="profileitem message">';
        $text .= $OUTPUT->pix_icon('i/email', get_string('email'));
        $text .= '<span><a href="' . $CFG->wwwroot . '/message/index.php?id=' . $user->id . '" target="_blank">' . $this->get_string('sendmessage') . '</a></span>';
        $text .= '</div>';
        // email
        $text .= '<div class="profileitem email">';
        $text .= $OUTPUT->pix_icon('i/email', get_string('email')) . obfuscate_mailto($user->email, '');
        $text .= '</div>';
        // ultimo acceso
        $text .= '<div class="lastaccess">';
        $text .= '<strong>' . get_string('lastaccess') . '</strong><span> ' . $this->get_lastaccess($user->lastaccess) . '<span>';
        $text .= '</div>';
        $text .= '</div>';
        return $text;
    }

    /**
     * 
     * @param string $condition
     * @return string
     */
    public function no_user_found($condition) {

        $text = '<div class="profileitem">';

        if (!empty($condition)) {
            $text .= $condition;
        } else {
            $text .= $this->get_string('noteacher_default');
        }
        $text .= '</div>';

        return $text;
    }

}
