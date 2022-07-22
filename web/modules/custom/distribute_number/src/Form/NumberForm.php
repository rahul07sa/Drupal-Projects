<?php

namespace Drupal\distribute_number\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;
use Drupal\Core\Url;
use Drupal\Core\Routing;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Ajax\InvokeCommand;
use Drupal\Core\Ajax\AlertCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Ajax\OpenModalDialogCommand;
use Drupal\Core\Form\FormState;
use Drupal\Core\Link;

/**
 * Provides the form for adding countries.
 */
class NumberForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'distribute_number_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state,$record = NULL) {
   
    $form['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Name'),
      '#maxlength' => 25,
	  '#attributes' => array(
       'class' => ['txt-class'],
       ),
      '#default_value' =>'',
	  '#prefix' => '<div id="div-name">',
      '#suffix' => '</div><div id="div-name-message"></div>',
    ];
	
	 $form['dnumber'] = [
      '#type' => 'number',
      '#title' => $this->t('Share'),
      '#maxlength' => 20,
	  '#attributes' => array(
       'class' => ['txt-class'],
       ),
      '#default_value' => '',
    ];
	
	
    $form['actions']['#type'] = 'actions';
    $form['actions']['Save'] = [
      '#type' => 'submit',
      '#button_type' => 'primary',
	  '#ajax' => ['callback' => '::saveDataAjaxCallback'] ,
      '#value' => $this->t('Save') ,
    ];
	 $form['actions']['clear'] = [
      '#type' => 'submit',
      '#ajax' => ['callback' => '::clearForm','wrapper' => 'form-div',] ,
      '#value' => 'Clear',
     ];
	 $render_array['#attached']['library'][] = 'distribute_number/global_styles';
    return $form;

  }
  
   /**
   * {@inheritdoc}
   */
  public function validateForm(array & $form, FormStateInterface $form_state) {
        //print_r($form_state->getValues());exit;
		
  }

 
   public function clearForm(array &$form, FormStateInterface $form_state) {
	   
	   $response = new AjaxResponse();
	   $response->addCommand(new InvokeCommand('.txt-class', 'val', ['']));
	   $response->addCommand(new InvokeCommand('#edit-name','removeAttr',['style']));
	   $response->addCommand(new HtmlCommand('#div-name-message', ''));
	   $response->addCommand(new InvokeCommand('.txt-class', 'val', ['']));
	  
	   return $response;
	   
   }
   
  
   
   /**
    * Custom Ajax responce.
    */
   public function saveDataAjaxCallback(array &$form, FormStateInterface $form_state) {
    
	 $conn = Database::getConnection();

    $field = $form_state->getValues();
	
    $re_url = Url::fromRoute('distribute_number.number');
   
	$fields["name"] = $field['name'];
	$fields["dnumber"] = $field['dnumber'];
	$response = new AjaxResponse();
	//========Field value validation
	if($fields["name"] == ''){
		$css = ['border' => '1px solid red'];
		$text_css = ['color' => 'red'];
        $message = ('Name not valid.');
	
		//$response = new \Drupal\Core\Ajax\AjaxResponse();
		$response->addCommand(new \Drupal\Core\Ajax\CssCommand('#edit-name', $css));
		$response->addCommand(new \Drupal\Core\Ajax\CssCommand('#div-name-message', $text_css));
		$response->addCommand(new \Drupal\Core\Ajax\HtmlCommand('#div-name-message', $message));
		return $response;
	}else{
    
      
      $conn->insert('distribute')
           ->fields($fields)->execute();
     
	$dialogText['#attached']['library'][] = 'core/drupal.dialog.ajax';
	
	$render_array = \Drupal::formBuilder()->getForm('Drupal\distribute_number\Form\NumberTableForm','All');

	 $response->addCommand(new HtmlCommand('.result_message','' ));
	 $response->addCommand(new \Drupal\Core\Ajax\AppendCommand('.result_message', $render_array));
	 $response->addCommand(new HtmlCommand('.pagination','' ));
	 $response->addCommand(new \Drupal\Core\Ajax\AppendCommand('.pagination', getPager()));
	 $response->addCommand(new \Drupal\Core\Ajax\InvokeCommand('.pagination-link', 'removeClass', array('active')));
	 $response->addCommand(new \Drupal\Core\Ajax\InvokeCommand('.pagination-link:first', 'addClass', array('active')));
	 $response->addCommand(new InvokeCommand('.txt-class', 'val', ['']));
	 
	 
     return $response;
   
  }
   
  }

  /**
   * {@inheritdoc}
   */
 public function submitForm(array & $form, FormStateInterface $form_state) {
	  
  }

}
  
