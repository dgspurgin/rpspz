<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

class RPSPZController extends Controller
{
    /**
     * @Route("/", name="home")
     */
    public function homeAction()
    {

        // replace this example code with whatever you need
        return $this->render('default/index.html.twig');
    }


    public function numberAction()
    {
        $number = rand(0, 100);

        return new Response(
            '<html><body>Lucky number: '.$number.'</body></html>'
        );
    }
}