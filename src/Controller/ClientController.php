<?php



namespace App\Controller;

use App\Entity\Client;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Form\ClientType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface; // Import the UserPasswordEncoderInterface
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
class ClientController extends AbstractController
{
    private $passwordHasher; // Rename the parameter accordingly

    public function __construct(UserPasswordHasherInterface $passwordHasher) // Rename the parameter accordingly
    {
        $this->passwordHasher = $passwordHasher; // Rename the parameter accordingly
    }

    #[Route("/inscription", name: "inscription")]
    public function inscription(Request $request): Response
    {
        // Create a new Client object for the form
        $client = new Client();

        // Create a form for registration
        $form = $this->createForm(ClientType::class, $client);

        // Handle form submission
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Get form data
            $client = $form->getData();

            // Handle password
            $plainPassword = $form->get('Motdepasse')->getData();
            $encodedPassword = $this->passwordHasher->hashPassword($client, $plainPassword);
            $client->setMotdepasse($encodedPassword);

            // Save the user to the database
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($client);
            $entityManager->flush();

            // Redirect the user to a success or login page
            return $this->redirectToRoute('login');
        }

        // Display the registration form
        return $this->render('/client/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }
  
     #[Route("/login", name: "login")]
   public function login(Request $request, AuthenticationUtils $authenticationUtils)
    {
        // Vérifier si l'utilisateur est déjà authentifié
        if ($this->getUser()) {
            // Rediriger l'utilisateur vers la vue client/home.html.twig
            return $this->redirectToRoute('client/home.html.twig');
        }

        // Récupérer l'erreur de connexion s'il y en a une
        $error = $authenticationUtils->getLastAuthenticationError();

        // Récupérer le dernier nom d'utilisateur saisi par l'utilisateur
        $lastUsername = $authenticationUtils->getLastUsername();

        // Créer un formulaire de connexion
        $form = $this->createForm(ClientType::class);

        // Vérifier si le formulaire a été soumis
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer les données saisies par le client
            $formData = $form->getData();
            $email = $formData->getMail();
            $password = $formData->getMotdepasse();

            // Vérifier si les données existent dans la base de données
            $client = $this->getDoctrine()->getRepository(Client::class)->findOneBy(['Mail' => $email]);

            if (!$client) {
                // Les données saisies n'existent pas dans la base de données
                // Afficher un message d'erreur (pop-up) indiquant au client de créer un nouveau compte
                throw new CustomUserMessageAuthenticationException('Adresse e-mail ou mot de passe incorrect. Veuillez créer un nouveau compte.');
            }

            // Vérifier le mot de passe
            $isPasswordValid = $this->passwordHasher->isPasswordValid($client, $password);

            if (!$isPasswordValid) {
                // Le mot de passe saisi est incorrect
                // Afficher un message d'erreur (pop-up) indiquant au client de vérifier ses informations de connexion
                throw new CustomUserMessageAuthenticationException('Adresse e-mail ou mot de passe incorrect.');
            }

         

            // Rediriger le client vers la page d'accueil
            return $this->redirectToRoute('home.html.twig');
        }

        // Afficher la page de connexion avec le formulaire
        return $this->render('client/login.html.twig', [
            'form' => $form->createView(),
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    
  
}