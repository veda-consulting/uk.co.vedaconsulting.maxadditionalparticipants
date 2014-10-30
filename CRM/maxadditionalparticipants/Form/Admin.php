<?php

class CRM_maxadditionalparticipants_Form_Admin extends CRM_Core_Form {
  const EVENT_SETTING_GROUP = 'Event Preferences';
  protected $_numStrings = 1;
  protected $_stringName = NULL;
  protected $_defaults = NULL;
  protected $_getreturn = NULL;

  function preProcess() {
    $this->_soInstance = CRM_Utils_Array::value('instance', $_GET);
    $this->assign('soInstance', $this->_soInstance);
    $breadCrumbUrl = CRM_Utils_System::url('civicrm/participant/config', "reset=1");
    $breadCrumb = array(array('title' => ts('Max Participants Setting '),
                                'url' => $breadCrumbUrl,
    ));
    $this->_getreturn = CRM_Core_BAO_Setting::getItem(self::EVENT_SETTING_GROUP,
        'max_participants', NULL, FALSE
    );
    CRM_Utils_System::appendBreadCrumb($breadCrumb);
  }

  function buildQuickForm() {

    $events = CRM_Event_BAO_Event::getEvents(0);
    $additionalOptions = array(
                      1 => '1', 2 => '2', 3 => '3', 4 => '4', 5 => '5',
                      6 => '6', 7 => '7', 8 => '8', 9 => '9', 10 => '10',
    );
    if($this->_soInstance) {

      $soInstances = (array)($this->_soInstance);
      $numString   = $this->_soInstance;
      $aEvents = array();
      foreach($events as $eventId => $eventVal ){
          if(!array_key_exists($eventId, $this->_getreturn)){
              $aEvents[$eventId] = $eventVal;
          }
      }
     $events      = $aEvents;
    }elseif(!empty($this->_getreturn)){
      $soInstances  = range ( 1, count($this->_getreturn), 1 );
      $numString    = 2;
    }else{
      $soInstances = array(1);
      $numString = 1;
    }
    $allEvents = CRM_Event_BAO_Event::getEvents(0);
    if($soInstances[0] <= count($allEvents)) {
      foreach( $soInstances as $instance){
          $this->addElement('select', "event_id_{$instance}", ts('Event Name'), array('' => ts('- select -')) + $events  );
          $this->addElement('select', "additional_participants_{$instance}",ts('Max_Participants'),$additionalOptions);
      }
    }
    $this->assign('numStrings', $numString);
    $cancelURL   = CRM_Utils_System::url('civicrm/participant/config', 'reset=1');
    $this->assign('elementNames', $this->getRenderableElementNames());
    if ($this->_soInstance) {
        return;
    }  
    $cancelURL   = CRM_Utils_System::url('civicrm/participant/config', 'reset=1');
    $this->addButtons(array(
        array(
            'type'      => 'submit',
            'name'      => ts('Save'),
            'isDefault' => TRUE,
        ),
        array(
            'type' => 'cancel',
            'name' => ts('Cancel'),
            'js'   => array( 'onclick' => "location.href='{$cancelURL}'; return false;" ),
        ),
    ));
    $this->addFormRule(array('CRM_maxadditionalparticipants_Form_Admin', 'formRule'), $this);
  }

  static function formRule($params, $files, $self) {
    $errors = array();
    
    foreach( $params as $key => $value ){
      $eventKey = explode('event_id_', $key);
      if( isset($eventKey[1]) && !empty($value) ){
           $eventId[] = $value;
      }
    }
    // check duplicate event ids
    if(!empty($eventId)){
      if(count($eventId) !== count(array_unique($eventId))){
        $errors['_qf_default'] = ts('Matching same events found');
      }
    }
    if (!empty($errors)) {
        return $errors;
    }
    return TRUE;
  }

  function postProcess() {
    $params = $this->_submitValues;

    $details = array();
    foreach( $params as $key => $value ){
      $eventKey = explode('event_id_', $key);
      if( isset($eventKey[1]) ){
          $eventId = $value;
      }
      $participantKey = explode('additional_participants_', $key);
      if( isset($participantKey[1]) ){
          $participantId = $value;
      }
      if(!empty($eventId) AND !empty($participantId)){
          $details[$eventId] = $participantId;
      }
    }
    CRM_Core_BAO_Setting::setItem($details,
        CRM_maxadditionalparticipants_Form_Admin::EVENT_SETTING_GROUP, 'max_participants'
    );
    CRM_Utils_System::redirect(CRM_Utils_System::url( 'civicrm/participant/config', 'reset=1'));
  }

  public function setDefaultValues() {
    $defaults = $eventDetails = array();
    if (!empty($this->_getreturn)) {
      $i = 1;
      foreach ($this->_getreturn as $eid => $maxparticipant) {
          $defaults['event_id_'.$i] = $eid;
          $defaults['additional_participants_'.$i] = $maxparticipant;
          $i++;
      }
    }
    return $defaults;
  }
  /**
   * Get the fields/elements defined in this form.
   *
   * @return array (string)
   */
  function getRenderableElementNames() {
    // The _elements list includes some items which should not be
    // auto-rendered in the loop -- such as "qfKey" and "buttons".  These
    // items don't have labels.  We'll identify renderable by filtering on
    // the 'label'.
    $elementNames = array();
    foreach ($this->_elements as $element) {
        $label = $element->getLabel();
        if (!empty($label)) {
          $elmentIds = explode( '_', $element->getName());
          $elementNames[$elmentIds[2]][] = $element->getName();
        }
    }
    return $elementNames;
  }
  
}