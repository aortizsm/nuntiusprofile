<?php

defined('MOODLE_INTERNAL') || die();

/**
 *
 * @package    block
 * @subpackage profile
 * @copyright  2020 Alberto Ortiz
 * @author Alberto Ortiz Acevedo <alberto@aortiz.cl>
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

        global $USER, $COURSE;

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
        var_dump($this->config->noteacher);
        

        switch ($course->groupmode) {
            case '0': //Group mode not active
                $users = enrol_get_course_users($COURSE->id);
                foreach ($users as $user) {
                    $roles = get_user_roles($context, $user->id, true);
                    foreach ($roles as $role) {
                        if ($role->roleid === $this->config->role) {
                            $this->content->text .= $this->helper->render_html($user);
                        } else {
                            //not match in role
                            $this->content->text = $this->helper->no_user_found($this->config->noteacher);
                            
                        }
                    }
                }
                break;
            case '1': case '2':  //Group mode active
                //we got all groups in context course
                $groupsid = groups_get_user_groups($COURSE->id, $USER->id);
                $groupid = key($groupsid);
                $ids = $groupsid[$groupid];
                $counter = 0;

                if (empty($groupsid[0]) || is_null($groupsid)) {
                    //userlogged doesn't any group
                    $this->content->text = $this->helper->no_user_found($this->config->noteacher);
                } else {
                    foreach ($ids as $id) {
                        $users = groups_get_members($id);
                        foreach ($users as $user) {
                            $roles = get_user_roles($context, $user->id, true);
                            foreach ($roles as $role) {
                                if ($role->roleid === $this->config->role) {
                                    $this->content->text = $this->helper->render_html($user);
                                    $counter++;
                                } else {
                                    $this->content->text = $this->helper->no_user_found($this->config->noteacher);
                                }
                            }
                        }
                    }

                    if ($counter == 0) {
                        $this->content->text = $this->helper->no_user_found($this->config->noteacher);
                    }
                }
                break;
            default: 
                $this->content->text = $this->helper->no_user_found($this->config->noteacher);
                break;
        }
        return $this->content;
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
