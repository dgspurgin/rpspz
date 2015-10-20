<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use AppBundle\Entity\Player;
use AppBundle\Entity\Choice;
use AppBundle\Entity\PlayedGame;


class LoadRPSPZData implements FixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $em)
    {

		# Command line to load this fixture:
		# php app/console doctrine:fixtures:load --purge-with-truncate
		# (purge-with-truncate resets auto increments - important utilizing specific id values)

    	# Players
    	$player = new Player();
		$player->setPlayerName("Human");
		$em->persist($player);

		$player = new Player();
		$player->setPlayerName("Computer");
		$em->persist($player);


		# Choices
		$choice = new Choice();
		$choice->setChoiceName("Scissors");
		$em->persist($choice);

		$choice = new Choice();
		$choice->setChoiceName("Paper");
		$em->persist($choice);

		$choice = new Choice();
		$choice->setChoiceName("Rock");
		$em->persist($choice);

		$choice = new Choice();
		$choice->setChoiceName("Plants");
		$em->persist($choice);

		$choice = new Choice();
		$choice->setChoiceName("Zombies");
		$em->persist($choice);


		# 2 Human Wins, Tie, 2 Computer Wins
		$playedGame = new PlayedGame();
		$playedGame->setP1ID(1);
		$playedGame->setP2ID(2);
		$playedGame->setP1Choice(1);
		$playedGame->setP2Choice(2);
		$playedGame->setWinningPlayerID(1);
		$em->persist($playedGame);

		$playedGame = new PlayedGame();
		$playedGame->setP1ID(1);
		$playedGame->setP2ID(2);
		$playedGame->setP1Choice(2);
		$playedGame->setP2Choice(2);
		$playedGame->setWinningPlayerID(0);
		$em->persist($playedGame);

		$playedGame = new PlayedGame();
		$playedGame->setP1ID(1);
		$playedGame->setP2ID(2);
		$playedGame->setP1Choice(3);
		$playedGame->setP2Choice(2);
		$playedGame->setWinningPlayerID(2);
		$em->persist($playedGame);

		$playedGame = new PlayedGame();
		$playedGame->setP1ID(1);
		$playedGame->setP2ID(2);
		$playedGame->setP1Choice(4);
		$playedGame->setP2Choice(2);
		$playedGame->setWinningPlayerID(1);
		$em->persist($playedGame);

		$playedGame = new PlayedGame();
		$playedGame->setP1ID(1);
		$playedGame->setP2ID(2);
		$playedGame->setP1Choice(5);
		$playedGame->setP2Choice(2);
		$playedGame->setWinningPlayerID(2);
		$em->persist($playedGame);


        $em->flush();
    }
}