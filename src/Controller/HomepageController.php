<?php
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Users;
class HomepageController extends AbstractController
{
	/**
	* @Route("/", name="homepage")
	*/
	public function homepage()
	{
		return $this->render('openPages/homepage.html.twig', [
			'title' => 'Homepage',
		]);
	}
}
