<?php

namespace Drupal\distribute_number\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;
use Drupal\Core\Url;
use Drupal\Core\Routing;
use Drupal\Core\Link;

/**
 * Provides the list of number row.
 */
class NumberTableForm extends FormBase {
	
	 /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'distribute_number_table_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state,$pageNo = NULL) {
    
   
   //$pageNo = 2;
    $header = [
      'id' => $this->t('Id'),
      'name' => $this->t('Name'),
	    'dnumber'=> $this->t('Share'),	  
	    'distribute_share' => $this->t('Distribute Share'),
      'opt' =>$this->t('Delete Record')
    ];

    if($pageNo != ''){
    $form['table'] = [
      '#type' => 'table',
      '#header' => $header,
      '#rows' => $this->get_disnumber($pageNo),
      '#empty' => $this->t('No records found'),
    ];
   }else{
	    $form['table'] = [
      '#type' => 'table',
      '#header' => $header,
      '#rows' => $this->get_disnumber("All"),
      '#empty' => $this->t('No records found'),
    ];
   }
  $form['#attached']['library'][] = 'core/drupal.dialog.ajax';
	$form['#attached']['library'][] = 'distribute_number/global_styles';
	
     $form['#theme'] = 'number_form';
	   $form['#prefix'] = '<div class="result_message">';
	   $form['#suffix'] = '</div>';
	   $form['#cache'] = [
      'max-age' => 0
    ];
    return $form;

  }

  
  

  public function validateForm(array &$form, FormStateInterface $form_state) {
     
	 //$field = $form_state->getValues();
	
	 
  }
  /**
   * {@inheritdoc}
   */
  public function submitForm(array & $form, FormStateInterface $form_state) {	  
	   
  }
  
  function get_disnumber($opt) {
	$res = array();
	//$opt = 2;
 if($opt == "All"){

  $results = \Drupal::database()->select('distribute', 'st');
 
  $results->fields('st');
  $results->range(0, 15);
  $results->orderBy('st.id','ASC');
  $res = $results->execute()->fetchAll();
  $ret = [];
 }else{
	 $query = \Drupal::database()->select('distribute', 'st');
  
  $query->fields('st');
  $query->range($opt*15, 15);
  $query->orderBy('st.id','ASC');
  $res = $query->execute()->fetchAll();
  $ret = [];
 }
    foreach ($res as $row) {

      
	  $edit = Url::fromUserInput('/ajax/distribute_number/distribute/edit/' . $row->id);
	  $delete = Url::fromUserInput('/del/distribute_number/distribute/delete/' . $row->id,array('attributes' => array('onclick' => "return confirm('Are you Sure')")));
      
	  $edit_link = Link::fromTextAndUrl(t('Edit'), $edit);
	  $delete_link = Link::fromTextAndUrl(t('Delete'), $delete);
	  $edit_link = $edit_link->toRenderable();
    $delete_link  = $delete_link->toRenderable();
	  $edit_link['#attributes'] = ['class'=>'use-ajax'];
	  $delete_link['#attributes'] = ['class'=>'use-ajax'];
	 
       
      $mainLink = t('@linkApprove  @linkReject', array('@linkApprove' => $edit_link, '@linkReject' => $delete_link));
      
       $ret[] = [
        'id' => $row->id,
        'name' => $row->name,
		    'dnumber' => $row->dnumber,
        'distribute_share' => render($edit_link),
        'opt' => render($delete_link),
		   ];
    }
    return $ret;
  }
	
}