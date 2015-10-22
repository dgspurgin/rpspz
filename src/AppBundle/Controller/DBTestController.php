<?php
namespace AppBundle\Controller;

use AppBundle\Entity\Player;
use AppBundle\Entity\Choice;
use AppBundle\Entity\PlayedGame;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

class DBTestController extends Controller
{

    /**
     * @Route("/dbtest/stats", name="dbTestStats")
     */
    public function statsAction()
    {

		$p1ID = 1; 		# Human
		$p2ID = 2; 		# Computer

		$em = $this->getDoctrine()->getManager();

		# P1Win--Tie--P2Win Totals
		$query = $em->createQuery(
			'SELECT COUNT (pg)
			FROM AppBundle:PlayedGame pg
			WHERE pg.winningPlayerID = :playerID'
		)->setParameter(':playerID', $p1ID);
		$p1Wins = $query->getSingleScalarResult();

		$query = $em->createQuery(
			'SELECT COUNT (pg)
			FROM AppBundle:PlayedGame pg
			WHERE pg.winningPlayerID = :playerID'
		)->setParameter(':playerID', $p2ID);
		$p2Wins = $query->getSingleScalarResult();

		$query = $em->createQuery(
			'SELECT COUNT (pg)
			FROM AppBundle:PlayedGame pg
			WHERE pg.winningPlayerID = 0');
		$ties = $query->getSingleScalarResult();


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

    /**
     * @Route("/dbtest/play/{p1Choice}", name="dbTestPlay")
     */
    public function storeGamePlayedAction($p1Choice=1)
    {



		# Save a played game
		$p1ID = 1; 		# Human
		$p2ID = 2; 		# Computer
		$p2Choice = 2;
		$winningPlayerID = "";

		$slot1Away = "n/a";
		$slot3Away = "n/a";

		if ($p1Choice == $p2Choice) {
			$winningPlayerID = 0;
		}
		else {
			$slot1Away = ($p1Choice % 5) + 1;
			$slot3Away = (($p1Choice + 2) % 5) + 1;
			if ($p2Choice == $slot1Away  || $p2Choice == $slot3Away) {
				$winningPlayerID = $p1ID;
			}
			else {
				$winningPlayerID = $p2ID;
			}
		}

		$playedGame = new PlayedGame();
		$playedGame->setP1ID($p1ID);
		$playedGame->setP2ID($p2ID);
		$playedGame->setP1Choice($p1Choice);
		$playedGame->setP2Choice($p2Choice);
		$playedGame->setWinningPlayerID($winningPlayerID);

		$em = $this->getDoctrine()->getManager();
		$em->persist($playedGame);

		# Save everything to db
		$em->flush();

		$responseString = <<<EOD
Game # = {$playedGame->getPlayedGameID()} <br>
P1 Choice = {$playedGame->getP1Choice()} <br>
slot1 = {$slot1Away}<br>
slot3 = {$slot3Away}<br>
<br>
P2 Choice = {$playedGame->getP2Choice()} <br>
Winner = {$playedGame->getWinningPlayerID()} <br><br>
EOD;

		return new Response($responseString);

    }
}