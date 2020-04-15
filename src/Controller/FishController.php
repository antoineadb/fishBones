<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Users;
use App\Form\FishType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FishController extends AbstractController {

    /**
     * @Route("/fish", name="fish")
     */
    public function index() {
        return $this->render('fish/index.html.twig', [
                    'controller_name' => 'FishController',
        ]);
    }

    /**
     * @Route("/login", name="login")
     */
    public function login(Request $request) {
        $users = new Users();
        $form = $this->createForm(FishType::class, $users,
                ['action' => $this->generateUrl('login')]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($users);
            $em->flush();
        }

        return $this->render('fish/login.html.twig', [
                    'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/home", name="home")
     */
    public function home() {
        return $this->render('fish/home.html.twig');
    }

    /**
     * @Route("/fish", name="fish")
     */
    public function fish() {
        return $this->render('fish/fish.html.twig');
    }

    /**
     * @Route("/run", name="run")
     */
    public function returnData(Request $request) {

        if ($request->isXmlHttpRequest()) {
            $number = mt_rand(0, 100);

            return new Response($number);
        }
    }

    /**
     * @Route("/readText", name="readText")
     */
    public function readText() {

        // on ouvre le fichier contenant le résultat de la requête
        $file = "../var/text.txt";
        if ($fid = fopen($file, "r")) {
            while (!feof($fid)) {
                $txt = fgets($fid);
                print_r($txt);
            }
            fclose($fid);
        } else {
            throw new FileException("Cannot open file " . $file . "\n");
        }
        return new Response($txt);
    }

}
