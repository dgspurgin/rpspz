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

		$responseString = self::stats($p1ID, $p2ID);

		return new Response($responseString);


	}

    public function stats($p1ID = 1, $p2ID = 2)
    {

		#-------------------------
		# Get data from repository

		$em = $this->getDoctrine()->getManager();

		# P1Win--Tie--P2Win Totals
		$p1Wins = $em->getRepository('AppBundle:PlayedGame')->playerWinTotal($p1ID);
		$p2Wins = $em->getRepository('AppBundle:PlayedGame')->playerWinTotal($p2ID);
		$ties = $em->getRepository('AppBundle:PlayedGame')->playerWinTotal(0);

		# Player 1 Choice Histories
		$p1ChoiceHistoryIndexedByChoiceID = $em->getRepository('AppBundle:PlayedGame')->playerChoiceHistoryIndexedByChoiceID($p1ID);

		# Player 2 Choice Histories
		$p2ChoiceHistoryIndexedByChoiceID = $em->getRepository('AppBundle:PlayedGame')->playerChoiceHistoryIndexedByChoiceID($p2ID);


		#-------------------------
		# Display data
		$responseString = "";
		$totalChoices = 0;


		# Display choices in order humans expect (rock, paper, scissors, ...)
		$conventionalChoiceOrder = array(3, 2, 1, 4, 5);


		$totalChoices = 0;
		$responseString .= <<<EOD
		Player 1 Choice History:

EOD;
		foreach ($conventionalChoiceOrder as $choiceID) {
			$row = $p1ChoiceHistoryIndexedByChoiceID[$choiceID];
			$responseString .= <<<EOD
				choice = {$row["choiceName"]},  total = {$row["timesChosen"]}

EOD;
				$totalChoices += $row["timesChosen"];
		}
		$responseString .= <<<EOD
		Total Choices = {$totalChoices}


EOD;


		$totalChoices = 0;
		$responseString .= <<<EOD
		Player 2 Choice History:

EOD;
		foreach ($conventionalChoiceOrder as $choiceID) {
			$row = $p2ChoiceHistoryIndexedByChoiceID[$choiceID];
			$responseString .= <<<EOD
				choice = {$row["choiceName"]},  total = {$row["timesChosen"]}

EOD;
				$totalChoices += $row["timesChosen"];
		}
		$responseString .= <<<EOD
		Total Choices = {$totalChoices}


EOD;




		$responseString .= <<<EOD
Human Wins = {$p1Wins}
Ties = {$ties}
Computer Wins = {$p2Wins}
EOD;


		return $responseString;


	}


}