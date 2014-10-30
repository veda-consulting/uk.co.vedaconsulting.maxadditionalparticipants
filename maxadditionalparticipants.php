<?php

require_once 'maxadditionalparticipants.civix.php';

/**
 * Implementation of hook_civicrm_config
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function maxadditionalparticipants_civicrm_config(&$config) {
  _maxadditionalparticipants_civix_civicrm_config($config);
}

/**
 * Implementation of hook_civicrm_xmlMenu
 *
 * @param $files array(string)
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function maxadditionalparticipants_civicrm_xmlMenu(&$files) {
  _maxadditionalparticipants_civix_civicrm_xmlMenu($files);
}

/**
 * Implementation of hook_civicrm_install
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function maxadditionalparticipants_civicrm_install() {
    $parentId = CRM_Core_DAO::getFieldValue('CRM_Core_DAO_Navigation', 'Administer', 'id', 'name');
    $weight   = CRM_Core_DAO::getFieldValue('CRM_Core_DAO_Navigation', 'CiviReport', 'weight', 'name');
    if ($parentId) {
        $maxparticipantMenuTree = 
            array(
              array(
                'label' => ts('Max Participant'),
                'name'  => 'Participant_Settings',
                'url'   => 'civicrm/participant/config?reset=1',
              ),
            );
        foreach ($maxparticipantMenuTree as $key => $menuItems) {
            $menuItems['is_active']   = 1;
            $menuItems['parent_id']   = $parentId;
            $menuItems['weight']      = ++$weight;
            $menuItems['permission']  = 'administer CiviCRM';
            CRM_Core_BAO_Navigation::add($menuItems);
        }
        CRM_Core_BAO_Navigation::resetNavigation();
    } 
    return _maxadditionalparticipants_civix_civicrm_install();
}

/**
 * Implementation of hook_civicrm_uninstall
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function maxadditionalparticipants_civicrm_uninstall() {
 require_once "CRM/Core/DAO.php";
  $maxparticipantMenuItem = array(
      'Participant_Settings', 
  );

  foreach ($maxparticipantMenuItem as $name) {
      $itemId = CRM_Core_DAO::getFieldValue('CRM_Core_DAO_Navigation', $name, 'id', 'name', TRUE);
      if ($itemId) {
        CRM_Core_BAO_Navigation::processDelete($itemId);
      }
  }
  CRM_Core_BAO_Navigation::resetNavigation();
  CRM_Core_DAO::executeQuery("DELETE FROM civicrm_setting WHERE group_name = Event Preferences AND name = max_participants");
  

  return _maxadditionalparticipants_civix_civicrm_uninstall();
}

/**
 * Implementation of hook_civicrm_enable
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function maxadditionalparticipants_civicrm_enable() {
  return _maxadditionalparticipants_civix_civicrm_enable();
}

/**
 * Implementation of hook_civicrm_disable
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function maxadditionalparticipants_civicrm_disable() {
  return _maxadditionalparticipants_civix_civicrm_disable();
}

/**
 * Implementation of hook_civicrm_upgrade
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed  based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function maxadditionalparticipants_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _maxadditionalparticipants_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implementation of hook_civicrm_managed
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function maxadditionalparticipants_civicrm_managed(&$entities) {
  return _maxadditionalparticipants_civix_civicrm_managed($entities);
}

/**
 * Implementation of hook_civicrm_caseTypes
 *
 * Generate a list of case-types
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function maxadditionalparticipants_civicrm_caseTypes(&$caseTypes) {
  _maxadditionalparticipants_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implementation of hook_civicrm_alterSettingsFolders
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function maxadditionalparticipants_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _maxadditionalparticipants_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

function maxadditionalparticipants_civicrm_buildForm( $formName, &$form ) {
  if($formName == 'CRM_Event_Form_Registration_Register') {
    $eid  = $form->getVar('_eventId');
    
    $participantDetails = CRM_Core_BAO_Setting::getItem(CRM_maxadditionalparticipants_Form_Admin::EVENT_SETTING_GROUP, 'max_participants');
    
    if($participantDetails && array_key_exists($eid, $participantDetails)) {
      $maxParticipants  = CRM_Utils_Array::value($eid, $participantDetails);
      
      $additionalOptions = array();
      
      for($i = 1 ; $i <= $maxParticipants ; $i++) {
        $additionalOptions[$i-1] = $i;
      }
      $form->add('select', 'additional_participants',
          ts('How many people are you registering?'),
          $additionalOptions,
          NULL,
          array('onChange' => "allowParticipant()")
        );
      
    }
    
  }
}
