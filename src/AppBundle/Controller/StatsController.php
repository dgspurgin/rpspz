<?php
namespace AppBundle\Controller;

use AppBundle\Entity\Player;
use AppBundle\Entity\Choice;
use AppBundle\Entity\PlayedGame;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

class StatsController extends Controller
{

    /**
     * @Route("/stats/{p1ID}/{p2ID}", name="stats")
     */
    public function statsAction($p1ID = 1, $p2ID = 2)
    {

		$em = $this->getDoctrine()->getManager();

		# P1Win--Tie--P2Win Totals
		$p1Wins = $em->getRepository('AppBundle:PlayedGame')->playerWinTotal($p1ID);
		$p2Wins = $em->getRepository('AppBundle:PlayedGame')->playerWinTotal($p2ID);
		$ties = $em->getRepository('AppBundle:PlayedGame')->playerWinTotal(0);


		# Player 1 Choice Histories
		$query = $em->createQuery(
			'SELECT c.choiceID, c.choiceName, COUNT (pg.p1Choice) AS timesChosen
			FROM AppBundle:Choice c
			LEFT JOIN AppBundle:PlayedGame pg
			WHERE c.choiceID = pg.p1Choice AND pg.p1ID = :playerID
			GROUP BY c.choiceID'
		)->setParameter(':playerID', $p1ID);
		$p1ChoiceHistory = $query->getResult();
		foreach ($p1ChoiceHistory as $index => $row) {
			$choiceID = $row["choiceID"];
			$p1ChoiceHistoryIndexedByChoiceID[$choiceID] = $row;
		}


		# Player 2 Choice Histories
		$query = $em->createQuery(
			'SELECT c.choiceID, c.choiceName, COUNT (pg.p2Choice) AS timesChosen
			FROM AppBundle:Choice c
			LEFT JOIN AppBundle:PlayedGame pg
			WHERE c.choiceID = pg.p2Choice AND pg.p2ID = :playerID
			GROUP BY c.choiceID'
		)->setParameter(':playerID', $p2ID);
		$p2ChoiceHistory = $query->getResult();
		foreach ($p2ChoiceHistory as $index => $row) {
			$choiceID = $row["choiceID"];
			$p2ChoiceHistoryIndexedByChoiceID[$choiceID] = $row;
		}



		# Display choices in conventional order
		$conventionalChoiceOrder = array(3, 2, 1, 4, 5);



		# Display Stats
		$responseString = "";


		$totalChoices = 0;
		$responseString .= <<<EOD
		Player 1 Choice History:<br>
EOD;
		foreach ($conventionalChoiceOrder as $choiceID) {
			$row = $p1ChoiceHistoryIndexedByChoiceID[$choiceID];
			$responseString .= <<<EOD
				choice = {$row["choiceName"]},  total = {$row["timesChosen"]} <br>
EOD;
				$totalChoices += $row["timesChosen"];
		}
		$responseString .= <<<EOD
		Total Choices = {$totalChoices}<br>
		<br>
EOD;



		$totalChoices = 0;
		$responseString .= <<<EOD
		Player 2 Choice History:<br>
EOD;
		foreach ($conventionalChoiceOrder as $choiceID) {
			$row = $p2ChoiceHistoryIndexedByChoiceID[$choiceID];
			$responseString .= <<<EOD
				choice = {$row["choiceName"]},  total = {$row["timesChosen"]} <br>
EOD;
				$totalChoices += $row["timesChosen"];
		}
		$responseString .= <<<EOD
		Total Choices = {$totalChoices}<br>
		<br>

EOD;




		$responseString .= <<<EOD
Human Wins = {$p1Wins} <br>
Ties = {$ties} <br>
Computer Wins = {$p2Wins}<br>
EOD;

		return new Response($responseString);


	}

}