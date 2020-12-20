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
class profile_helper {

    private $name = 'profile';

    /**
     * Get string name
     * 
     * @param type $name
     * @param type $a
     * @return type
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


}
