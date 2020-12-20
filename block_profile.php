<?php

defined('MOODLE_INTERNAL') || die();

/**
 *
 * @package    block
 * @subpackage profile
 * @copyright  2020 Alberto Ortiz
 * @author     Alberto Ortiz <aortizsm@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
include_once realpath(dirname(__FILE__)) . '/classes/profile_helper.php';

class block_profile extends block_base {

    private $helper;

    public function init() {
        $this->helper = new profile_helper();
        $this->title = $this->helper->get_string('title');
    }

    public function get_content() {

        global $USER, $COURSE, $OUTPUT, $CFG;
        $context = context_course::instance($COURSE->id);


        if ($this->content !== NULL) {
            return $this->content;
        }

        if (!isloggedin() or isguestuser()) {
            return '';
        }

        if (!empty($this->config->title)) {
            $this->title = $this->config->title;
        } else {
            $this->title = $this->helper->get_string('title');
        }

        $this->content = new stdClass();
        $this->content->text = '';

        //obtenemos el curso
        $course = $this->page->course;

        switch ($course->groupmode) {
            case '0': //No esta el modo grupo activo
                $counter = 0;
                $users = enrol_get_course_users($COURSE->id);

                foreach ($users as $user) {
                    $roles = get_user_roles($context, $user->id, true);
                    foreach ($roles as $role) {
                        if ($role->roleid === $this->config->role) {
                            $this->content->text .= '<div class="profileitem picture">';
                            $this->content->text .= $OUTPUT->user_picture($user, array('courseid' => $COURSE->id, 'size' => '100%', 'class' => 'profilepicture'));
                            $this->content->text .= '</div>';
                            $this->content->text .= '<div class="profileitem fullname">' . fullname($user) . '</div>';
                            // Enviar mensaje
                            $this->content->text .= '<div class="profileitem message">';
                            $this->content->text .= $OUTPUT->pix_icon('i/email', get_string('email'));
                            $this->content->text .= '<span><a href="' . $CFG->wwwroot . '/message/index.php?id=' . $user->id . '" target="_blank">' . $this->helper->get_string('sendmessage') . '</a></span>';
                            $this->content->text .= '</div>';
                            // email
                            $this->content->text .= '<div class="profileitem email">';
                            $this->content->text .= $OUTPUT->pix_icon('i/email', get_string('email')) . obfuscate_mailto($user->email, '');
                            $this->content->text .= '</div>';
                            // ultimo acceso
                            $this->content->text .= '<div class="lastaccess">';
                            $this->content->text .= '<strong>' . get_string('lastaccess') . '</strong><span> ' . format_time($user->lastaccess) . '<span>';
                            $this->content->text .= '</div>';
                            $counter++;
                        }
                    }
                }
                break;
            case '1': case '2':  //Tenemos el modo grupo activo
                //obtenemos los grupos
                $groupsid = groups_get_user_groups($COURSE->id, $USER->id);
                $groupid = key($groupsid);
                $ids = $groupsid[$groupid];
                $counter = 0;

                if (empty($groupsid[0])) {
                    //el usuario logeuado no tiene grupo
                    $this->content->text = $this->no_user_found();
                } else {
                    //el usuario logeuado tiene grupo
                    foreach ($ids as $id) {
                        $users = groups_get_members($id);
                        foreach ($users as $user) {
                            $roles = get_user_roles($context, $user->id, true);
                            foreach ($roles as $role) {
                                if ($role->roleid === $this->config->role) {
                                    //encontramos usuarios
                                    $this->content->text .= '<div class="profileitem picture">';
                                    $this->content->text .= $OUTPUT->user_picture($user, array('courseid' => $COURSE->id, 'size' => '100%', 'class' => 'profilepicture'));
                                    $this->content->text .= '</div>';
                                    $this->content->text .= '<div class="profileitem fullname">' . fullname($user) . '</div>';
                                    // Enviar mensaje
                                    $this->content->text .= '<div class="profileitem message">';
                                    $this->content->text .= $OUTPUT->pix_icon('i/email', get_string('email'));
                                    $this->content->text .= '<span><a href="' . $CFG->wwwroot . '/message/index.php?id=' . $user->id . '" target="_blank">' . $this->helper->get_string('sendmessage') . '</a></span>';
                                    $this->content->text .= '</div>';
                                    // email
                                    $this->content->text .= '<div class="profileitem email">';
                                    $this->content->text .= $OUTPUT->pix_icon('i/email', get_string('email')) . obfuscate_mailto($user->email, '');
                                    $this->content->text .= '</div>';
                                    // ultimo acceso
                                    $this->content->text .= '<div class="lastaccess">';
                                    $this->content->text .= '<strong>' . get_string('lastaccess') . '</strong><span> ' . format_time($user->lastaccess) . '<span>';
                                    $this->content->text .= '</div>';
                                    $counter++;
                                }
                            }
                        }
                    }

                    if ($counter == 0) {
                        $this->content->text = $this->no_user_found();
                    }
                }
                break;
            default: //cualquier otro
                $this->content->text = $this->no_user_found();
                break;
        }
        return $this->content;
    }

    public function no_user_found() {

        $text = '<div class="profileitem">';
        $text .= $this->config->noteacher;
        $text .= '</div>';
        return $text;
    }

    function _self_test() {
        return true;
    }

    /**
     * allow the block to have a configuration page
     *
     * @return boolean
     */
    public function has_config() {
        return false;
    }

    /**
     * allow more than one instance of the block on a page
     *
     * @return boolean
     */
    public function instance_allow_multiple() {
        //allow more than one instance on a page
        return false;
    }

    /**
     * instance specialisations (must have instance allow config true)
     *
     */
    public function specialization() {
        
    }

    /**
     * locations where block can be displayed
     *
     * @return array
     */
    public function applicable_formats() {
        return array('all' => true);
    }

    /**
     * post install configurations
     *
     */
    public function after_install() {
        
    }

    /**
     * post delete configurations
     *
     */
    public function before_delete() {
        
    }

}
