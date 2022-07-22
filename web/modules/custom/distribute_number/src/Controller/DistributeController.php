<?php

namespace Drupal\distribute_number\Controller;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Database;
use Drupal\Core\Url;
use Drupal\Core\Routing;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Ajax\OpenModalDialogCommand;
use Drupal\Core\Form\FormBuilder;

/**
 * Class DistributeController.
 *
 * @package Drupal\distribute_number\Controller
 */
class DistributeController extends ControllerBase {

/**
   * The form builder.
   *
   * @var \Drupal\Core\Form\FormBuilder
   */
  protected $formBuilder;

  /**
   * The DistributeController constructor.
   *
   * @param \Drupal\Core\Form\FormBuilder $formBuilder
   */
  public function __construct(FormBuilder $formBuilder) {
    $this->formBuilder = $formBuilder;
  }
/**
   * {@inheritdoc}
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *   The Drupal service container.
   *
   * @return static
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('form_builder')
    );
  }
  
  /**
   * {@inheritdoc}
   */
  public function managenumber() {
	$form['form'] = $this->formBuilder()->getForm('Drupal\distribute_number\Form\NumberForm');
	$render_array = $this->formBuilder()->getForm('Drupal\distribute_number\Form\NumberTableForm','All');
	   $form['form1'] = $render_array;
	    $form['form']['#suffix'] = '<div class="pagination">'.getPager().'</div>';
    return $form;
  }
  /**
   * {@inheritdoc}
   * Deletes the given row
   */
  public function deleteNumberAjax($cid) {
     $res = \Drupal::database()->query("delete from distribute where id = :id", array(':id' => $cid)); 
	   $render_array = $this->formBuilder->getForm('Drupal\distribute_number\Form\NumberTableForm','All');
	   $response = new AjaxResponse();
	
	   $response->addCommand(new HtmlCommand('.result_message','' ));
	   $response->addCommand(new \Drupal\Core\Ajax\AppendCommand('.result_message', $render_array));
	   $response->addCommand(new \Drupal\Core\Ajax\InvokeCommand('.pagination-link', 'removeClass', array('active')));
	   $response->addCommand(new \Drupal\Core\Ajax\InvokeCommand('.pagination-link:first', 'addClass', array('active')));
	   
    return $response;

  }
  
   /**
   * {@inheritdoc}
   * update the given row
   */
  public function editNumberAjax($cid) {
    
	  $conn = Database::getConnection();
      $query = $conn->select('distribute', 'st');
      $query->condition('id', $cid)->fields('st');
      $record = $query->execute()->fetchAssoc();
    
	 $render_array = \Drupal::formBuilder()->getForm('Drupal\distribute_number\Form\NumberEditForm',$record);
   $response = new AjaxResponse();
	 $response->addCommand(new OpenModalDialogCommand('Edit Form', $render_array, ['width' => '800']));
	 
    return $response;
  }

/**
   * {@inheritdoc}
   * Deletes the given row
   */
  
  public function tablePaginationAjax($no){
	  $response = new AjaxResponse();
	  $render_array = \Drupal::formBuilder()->getForm('Drupal\distribute_number\Form\NumberTableForm',$no);
	   $response->addCommand(new HtmlCommand('.result_message','' ));
	    $response->addCommand(new \Drupal\Core\Ajax\AppendCommand('.result_message', $render_array));
		
	 
	 return $response;
	  
  }
  
}
