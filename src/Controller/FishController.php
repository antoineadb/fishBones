<?php

namespace App\Controller;

use App\Form\ContactType;
use App\Form\FindFileType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Users;
use App\Form\FishType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Finder\Finder;


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

    /**
     * @Route("/readMail", name="readmail")
     */
    public function readMail()
    {
        $server = "{localhost:993/imap/ssl}INBOX";

       // $server =  '{localhost:993/imap/ssl}INBOX';
        $username = 'antoineadb@gmail.com';
        $password = 'Oceane2002';
        //$mailbox = imap_open($server, $username, $password);
        $mailbox = imap_open($server, $username, $password) or die('Cannot connect to Gmail: ' . imap_last_error());

        $mails = FALSE;
        if (FALSE === $mailbox) {
            $err = 'La connexion a échoué. Vérifiez vos paramètres!';
        } else {
            $info = imap_check($mailbox);
            if (FALSE !== $info) {
            // le nombre de messages affichés est entre 1 et 50
            // libre à vous de modifier ce paramètre
                $nbMessages = min(50, $info->Nmsgs);
                $mails = imap_fetch_overview($mailbox, '1:' . $nbMessages, 0);
            } else {
                $err = 'Impossible de lire le contenu de la boite mail';
            }
        }
        if (FALSE === $mails) {
            echo $err;
        } else {
            $informationboite = 'La boite aux lettres contient ' . $info->Nmsgs . ' message(s) dont ' .
                $info->Recent . ' recent(s)';
            foreach ($mails as $mail) {
                echo 'Objet : ' . (iconv_mime_decode($mail->subject, 0, "ISO-8859-1")) . ' Date de r;&eacutception : ' . $mail->date . '';
            }
        }
    }

    /**
     * @Route("/contact", name="contact")
     * @param Request $request
     * @param \Swift_Mailer $mailer
     * @return Response
     */
    public function contact(Request $request,\Swift_Mailer $mailer)
    {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $contact = $form->getData();
            $message = (new \Swift_Message('Nouveau Message'))
            ->setFrom($contact['email'])
            ->setTo('antoineadb@gmail.com')
            ->setBody(
                $this->renderView(
                    'emails/contactMail.html.twig',compact('contact')

                ),
                'text/html'
            )
        ;

            $mailer->send($message);

            $this->addFlash('message','le mail à bien été envoyé');
            return $this->redirectToRoute('home');
        }

        return $this->render('mail/contact.html.twig', [
            'contact' => $form->createView()
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/findFile", name="findFile")
     */
    public function findDirFromFile(Request $request)
    {
        $form = $this->createForm(FindFileType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $data = $form->getData();
            $finder = $finder = new Finder();

            $finder->name($data['file']);

            $finder->in("D:\\");
            $finder->ignoreUnreadableDirs(true);
            $dir =[];
            if ($finder->hasResults()) {
                foreach ($finder as $file) {
                    $dir[]  = $file->getPath();
                }

                $st = '';
                for($i=0;$i<count($dir);$i++)
                {
                   $st.=$dir[$i].', ';
                }
                $st = substr(trim($st),0,-1);
                $this->addFlash('success',"L'occurence a été trouvé! le (ou les) répertoire(s) sont ".$st);
            }
            else
            {
                $this->addFlash('message',"L'occurence n'a pas été trouvé!");
            }

        }
        return $this->render ( 'findfile/findfile.html.twig', array (
            'findfile' => $form->createView ()
        ));

    }
}
