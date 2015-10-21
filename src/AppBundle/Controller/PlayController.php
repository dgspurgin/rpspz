<?php
namespace AppBundle\Controller;

use AppBundle\Entity\Player;
use AppBundle\Entity\Choice;
use AppBundle\Entity\PlayedGame;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class PlayController extends Controller
{



    /**
     * @Route("/play", name="play")
     */
    public function playAction(Request $request)
    {

		# Hardwire players for now
		$p1ID = 1; 		# Human
		$p2ID = 2; 		# Computer

		$p1Choice = null;
		$p2Choice = null;
		$p1WinLose = "--";
		$p2WinLose = "--";
		$stats = "";
		$p1ChoiceName = "";
		$p2ChoiceName = "";

		$data = array();
    	$form = $this->createFormBuilder($data)
			->add('save_choice3', 'submit', array('label' => 'Rock'))
			->add('save_choice2', 'submit', array('label' => 'Paper'))
			->add('save_choice1', 'submit', array('label' => 'Scissors'))
			->add('save_choice4', 'submit', array('label' => 'Plants'))
			->add('save_choice5', 'submit', array('label' => 'Zombies'))
			->getForm();
	    $form->handleRequest($request);


		if ($form->isValid()) {
			$p1Choice = 0;
			if ($form->get('save_choice1')->isClicked()) {
				$p1Choice = 1;
			}
			elseif ($form->get('save_choice2')->isClicked()) {
				$p1Choice = 2;
			}
			elseif ($form->get('save_choice3')->isClicked()) {
				$p1Choice = 3;
			}
			elseif ($form->get('save_choice4')->isClicked()) {
				$p1Choice = 4;
			}
			elseif ($form->get('save_choice5')->isClicked()) {
				$p1Choice = 5;
			}

			if ($p1Choice) {

				# Top secret algorithm to generate computer's choice :P
				$p2Choice = self::generateGameChoice();

				$em = $this->getDoctrine()->getManager();
				$p1ChoiceName = $em->getRepository('AppBundle:Choice')->choiceName($p1Choice);
				$p2ChoiceName = $em->getRepository('AppBundle:Choice')->choiceName($p2Choice);

				$winningPlayer = self::determineWinner($p1Choice, $p2Choice);

				$winningPlayerID = 0;
				$p1WinLose = "Tie";
				$p2WinLose = "Tie";
				if ($winningPlayer == 1) {
					$winningPlayerID = $p1ID;
					$p1WinLose = "Winner";
					$p2WinLose = "Loser";
				}
				elseif ($winningPlayer == 2) {
					$winningPlayerID = $p2ID;
					$p1WinLose = "Loser";
					$p2WinLose = "Winner";
				}

				try
				{
					# Save game to db
					$playedGame = new PlayedGame();
					$playedGame->setP1ID($p1ID);
					$playedGame->setP2ID($p2ID);
					$playedGame->setP1Choice($p1Choice);
					$playedGame->setP2Choice($p2Choice);
					$playedGame->setWinningPlayerID($winningPlayerID);

					$em = $this->getDoctrine()->getManager();
					$em->persist($playedGame);
					$em->flush();

				} catch (Exception $e) {
					# Log $e->getMessage()
				}
			}
		}

		$stats_controller = $this->get('stats_controller');
		$stats = $stats_controller->statsDisplay($p1ID, $p2ID);

		return $this->render('default/Play/gameboard.html.twig', array(
		        'form' => $form->createView(),
		        'p1WinLose' => $p1WinLose,
		        'p1ChoiceName' => $p1ChoiceName,
				'stats' => $stats,
		        'p2WinLose' => $p2WinLose,
		        'p2ChoiceName' => $p2ChoiceName,

	    ));

    }

    protected function determineWinner($p1Choice, $p2Choice)
    {

    	# In this sample project rely on caller validation

     	$winningPlayer = 0;

    	if ($p1Choice != $p2Choice)
    	{
			$slot1Away = ($p1Choice % 5) + 1;
			$slot3Away = (($p1Choice + 2) % 5) + 1;
			if ($p2Choice == $slot1Away  || $p2Choice == $slot3Away)
			{
				$winningPlayer = 1;
			}
			else {
				$winningPlayer = 2;
			}
		}

		return $winningPlayer;
    }

    protected function generateGameChoice()
    {
		return mt_rand (1,5);
    }

}