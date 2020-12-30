<?php

defined('MOODLE_INTERNAL') || die();

/*
 * @package    block
 * @subpackage nuntius
 * @copyright  2020 Alberto Ortiz
 * @author     Alberto Ortiz <aortizsm@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

include_once realpath(dirname(__FILE__)) . '/classes/profile_helper.php';

class block_nuntius_edit_form extends block_edit_form {

    /**
     * 
     * @global type $COURSE
     * @param type $mform
     */
    protected function specific_definition($mform) {

        //global $COURSE, $CFG;

        $helper = new profile_helper();
        $mform->addElement('header', 'configheader', $helper->get_string('header_settings'));
        $mform->addElement('text', 'config_title', $helper->get_string('pluginname_setting'));
        $mform->setType('config_title', PARAM_MULTILANG);
        $roles = $helper->get_roles($this->page->context);
        $roles = array(0 => get_string('choosedots')) + $roles;
        $mform->addElement('select', 'config_role', $helper->get_string('roles'), $roles);
        $mform->addElement('textarea', 'config_noteacher', $helper->get_string('messagenoteacher'), 'wrap="virtual" rows="20" cols="50"');
    }

}
