<?php
namespace AppBundle\Controller;

use AppBundle\Entity\Player;
use AppBundle\Entity\Choice;
use AppBundle\Entity\PlayedGame;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;


/**
 * @Route(service="stats_controller")
 */
class StatsController extends Controller
{


    /**
     * @Route("/stats/{p1ID}/{p2ID}", name="stats")
     */
    public function statsAction($p1ID = 1, $p2ID = 2)
    {

		$responseString = self::statsDisplay($p1ID, $p2ID);

		return new Response($responseString);


	}

    public function statsFetch($p1ID = 1, $p2ID = 2)
    {

		#-------------------------
		# Get data from repository

		$em = $this->getDoctrine()->getManager();

		# Player Win/Tie/Loss Totals
		/*
			If a 3rd player is introduced (or a player is allowed to play self)
			total for player and totals against a specific player will differ
		*/
		$p1Wins = $em->getRepository('AppBundle:PlayedGame')->playerWinTotal($p1ID);
		$p1Ties = $em->getRepository('AppBundle:PlayedGame')->playerTieTotal($p1ID);
		$p1Losses = $em->getRepository('AppBundle:PlayedGame')->playerLossTotal($p1ID);
		$p1WinsAgainstAnother = $em->getRepository('AppBundle:PlayedGame')->playerWinTotalAgainstAnother($p1ID, $p2ID);
		$p1TiesAgainstAnother = $em->getRepository('AppBundle:PlayedGame')->playerTieTotalAgainstAnother($p1ID, $p2ID);
		$p1LossesAgainstAnother = $em->getRepository('AppBundle:PlayedGame')->playerLossTotalAgainstAnother($p1ID, $p2ID);

		$stats['winTieLoss']['p1']['wins'] = $p1Wins;
		$stats['winTieLoss']['p1']['ties'] = $p1Ties;
		$stats['winTieLoss']['p1']['losses'] = $p1Losses;
		$stats['winTieLoss']['p1']['winsAgainstAnother'] = $p1WinsAgainstAnother;
		$stats['winTieLoss']['p1']['tiesAgainstAnother'] = $p1TiesAgainstAnother;
		$stats['winTieLoss']['p1']['lossesAgainstAnother'] = $p1LossesAgainstAnother;


		$p2Wins = $em->getRepository('AppBundle:PlayedGame')->playerWinTotal($p2ID);
		$p2Ties = $em->getRepository('AppBundle:PlayedGame')->playerTieTotal($p2ID);
		$p2Losses = $em->getRepository('AppBundle:PlayedGame')->playerLossTotal($p2ID);
		$p2WinsAgainstAnother = $em->getRepository('AppBundle:PlayedGame')->playerWinTotalAgainstAnother($p2ID, $p1ID);
		$p2TiesAgainstAnother = $em->getRepository('AppBundle:PlayedGame')->playerTieTotalAgainstAnother($p2ID, $p1ID);
		$p2LossesAgainstAnother = $em->getRepository('AppBundle:PlayedGame')->playerLossTotalAgainstAnother($p2ID, $p1ID);

		$stats['winTieLoss']['p2']['wins'] = $p2Wins;
		$stats['winTieLoss']['p2']['ties'] = $p2Ties;
		$stats['winTieLoss']['p2']['losses'] = $p2Losses;
		$stats['winTieLoss']['p2']['winsAgainstAnother'] = $p2WinsAgainstAnother;
		$stats['winTieLoss']['p2']['tiesAgainstAnother'] = $p2TiesAgainstAnother;
		$stats['winTieLoss']['p2']['lossesAgainstAnother'] = $p2LossesAgainstAnother;



		# Player Choice Histories
		$p1ChoiceHistoryIndexedByChoiceID = $em->getRepository('AppBundle:PlayedGame')->playerChoiceHistoryIndexedByChoiceID($p1ID);
		$stats['choiceHistories']['p1'] = $p1ChoiceHistoryIndexedByChoiceID;

		$p2ChoiceHistoryIndexedByChoiceID = $em->getRepository('AppBundle:PlayedGame')->playerChoiceHistoryIndexedByChoiceID($p2ID);
		$stats['choiceHistories']['p2'] = $p2ChoiceHistoryIndexedByChoiceID;

		return $stats;
	}

    public function statsPlayerVsPlayerTotals($p1ID = 1, $p2ID = 2)
    {
		$em = $this->getDoctrine()->getManager();

		$player1_vs_player2_totals['p1wins'] = $em->getRepository('AppBundle:PlayedGame')->playerWinTotalAgainstAnother($p1ID, $p2ID);
		$player1_vs_player2_totals['ties'] = $em->getRepository('AppBundle:PlayedGame')->playerTieTotalAgainstAnother($p1ID, $p2ID);
		$player1_vs_player2_totals['p2wins'] = $em->getRepository('AppBundle:PlayedGame')->playerWinTotalAgainstAnother($p2ID, $p1ID);

		return $player1_vs_player2_totals;
	}

    public function statsPlayerChoiceHistory($pID) {
		$em = $this->getDoctrine()->getManager();

		return $em->getRepository('AppBundle:PlayedGame')->playerChoiceHistoryIndexedByChoiceID($pID);
	}

	public function statsDisplay($p1ID = 1, $p2ID = 2)
	{

		$stats = self::statsFetch($p1ID, $p2ID);

		$player1_vs_player2_totals['p1wins'] 	= $stats['winTieLoss']['p1']['winsAgainstAnother'];
		$player1_vs_player2_totals['ties'] 		= $stats['winTieLoss']['p1']['tiesAgainstAnother'];
		$player1_vs_player2_totals['p2wins'] 	= $stats['winTieLoss']['p2']['winsAgainstAnother'];


		$player1_history['choices'] = null;
		$player1_history['number'] = 1;
		$player1_history['totalChoices'] = 0;

		$player1_history['choices'] = null;
		$player2_history['number'] = 2;
		$player2_history['totalChoices'] = 0;


		# Order choices in way humans expect
		# (rock, paper, scissors, ...)
		$conventionalChoiceOrder = array(3, 2, 1, 4, 5);

		foreach ($conventionalChoiceOrder as $choiceID) {
			$p1row = $stats['choiceHistories']['p1'][$choiceID];
			$player1_history['choices'][$choiceID] = $p1row;
			$player1_history['totalChoices'] += $p1row['timesChosen'];

			$p2row = $stats['choiceHistories']['p2'][$choiceID];
			$player2_history['choices'][$choiceID] = $p2row;
			$player2_history['totalChoices'] += $p2row['timesChosen'];

		}

        return $this->renderView(
            'default/Stats/stats.html.twig',
            array('player1_vs_player2_totals' => $player1_vs_player2_totals,
            		'player1_history' => $player1_history,
            		'player2_history' => $player2_history)
        );


	}


}