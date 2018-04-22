<?php
// src/Controller/DefaultController.php
  namespace App\Controller;

  use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
  use Symfony\Component\HttpFoundation\Response; 
  use Symfony\Bundle\FrameworkBundle\Controller\Controller;
  use Symfony\Component\HttpFoundation\Request;
  use Symfony\Component\HttpFoundation\Session\Session;

  use App\Service\EvalueFile;
  use App\Service\SolvePuzzle;  

  use App\Form\PuzzleType;
  use App\Entity\Puzzle;

class PuzzleController extends Controller {

  private $session;
  public function __construct(){ $this->session = new Session(); }

    public function solve(Request $request, EvalueFile $evalueFile, SolvePuzzle $solvePuzzle) {
      $file = null;
      $puzzle = new Puzzle();
      $form = $this->createForm(PuzzleType::class, $puzzle);  
      $form->handleRequest($request);
      if ($form->isSubmitted() && $form->isValid()) {
        $file = $form['file']->getData();
        if( !empty($file) && $file != null ){
          $ext = $file->guessExtension();
          if( $ext == 'txt'){
            $message = ['type'=>'success', 'message' => 'correct extension file'];
          }else{
            $message = ['type'=>'danger', 'message' => 'incorrect extension file'];
          }
          $this->session->getFlashBag()->add('status',$message);
          $array_data = $evalueFile->extractData($file);
          $numberRowsColumns = $evalueFile->numberRowsColumns($array_data);
          if( $numberRowsColumns['rows'] === null || $numberRowsColumns['columns'] === null ){
            $message = ['type'=>'danger', 'message' => 'incorrect format'];
            $this->session->getFlashBag()->add('status',$message);
          }
          $pieces = $solvePuzzle->Pieces($array_data);
        }
        $solvePuzzle->SolvePuzzle($numberRowsColumns, $array_data);
        return $this->redirectToRoute('app_solve');
      }
      if($file != null){
        $puzzle = $evalueFile->extractData($file);
      }else{
        $puzzle = null;
      }
      return $this->render(
        'puzzle/solve.html.twig',
        array(
            'form' => $form->createView(),
            'puzzle'=>$puzzle
        )
      );
    }

}