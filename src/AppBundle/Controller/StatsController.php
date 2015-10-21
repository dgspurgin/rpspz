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

	public function statsDisplay($p1ID = 1, $p2ID = 2)
	{
		$stats = self::statsFetch($p1ID, $p2ID);

		$responseString = "";
		$totalChoices = 0;


		$responseString .= <<<EOD
		<div class='player_history_title'>Wins/Tie/Loss History</div>
		<table class='players_win_tie_loss_totals'>
			<tr><td>Player 1 Wins</td><td>{$stats['winTieLoss']['p1']['winsAgainstAnother']}</td></tr>
			<tr><td>Ties</td><td>{$stats['winTieLoss']['p1']['tiesAgainstAnother']}</td></tr>
			<tr><td>Player 2 Wins</td><td>{$stats['winTieLoss']['p2']['winsAgainstAnother']}</td></tr>
		</table>
EOD;


		# Display choices in order humans expect (rock, paper, scissors, ...)
		$conventionalChoiceOrder = array(3, 2, 1, 4, 5);

		$totalChoices = 0;
		$responseString .= <<<EOD
		<div class='player_history'>
			<div class='player_history_title'>Player 1 Choice History:</div>
			<table class='player_history_choices'>
				<tr>
					<th>Choice</th><th>Times Selected</td>
				</tr>

EOD;

		foreach ($conventionalChoiceOrder as $choiceID) {
			$row = $stats['choiceHistories']['p1'][$choiceID];
			$responseString .= <<<EOD
				<tr>
					<td>{$row["choiceName"]}</td><td>{$row["timesChosen"]}</td>
				</tr>

EOD;
				$totalChoices += $row["timesChosen"];
		}
		$responseString .= <<<EOD
				<tr>
					<td class='choice_total'>Total Choices</td><td>{$totalChoices}</td>
				</tr>
			</table>
		</div>

EOD;


		$totalChoices = 0;
		$responseString .= <<<EOD
		<div class='player_history'>
			<div class='player_history_title'>Player 2 Choice History:</div>
			<table class='player_history_choices'>
				<tr>
					<th>Choice</th><th>Times Selected</td>
				</tr>

EOD;
		foreach ($conventionalChoiceOrder as $choiceID) {
			$row = $stats['choiceHistories']['p2'][$choiceID];
			$responseString .= <<<EOD
				<tr>
					<td>{$row["choiceName"]}</td><td>{$row["timesChosen"]}</td>
				</tr>

EOD;
				$totalChoices += $row["timesChosen"];
		}
		$responseString .= <<<EOD
				<tr>
					<td class='choice_total'>Total Choices</td><td>{$totalChoices}</td>
				</tr>

			</table>
		</div>


EOD;


		return $responseString;


	}


}