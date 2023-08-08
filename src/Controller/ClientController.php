<?php
namespace App\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Form\ReclamationType;
use App\Entity\Client;
use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Form\ClientType;
use Symfony\Component\Security\Http\Logout\LogoutHandlerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface; // Import the UserPasswordEncoderInterface
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use App\Entity\Reclamation;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class ClientController extends AbstractController implements LogoutSuccessHandlerInterface

{
    
    private $passwordHasher; // Rename the parameter accordingly

    public function __construct(UserPasswordHasherInterface $passwordHasher) // Rename the parameter accordingly
    {
       ;
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
  


   #[Route("/home", name: "home")]
public function home(Request $request): Response
{
    // Vérifier si l'utilisateur est connecté
    if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
        // Rediriger l'utilisateur vers la page de connexion
        return $this->redirectToRoute('login');
    }

    // Récupérer l'utilisateur connecté
    $client = $this->getUser();

    // Utilisez la nouvelle vue "portal.html.twig" comme gabarit de base
    return $this->render('client/home.html.twig', [
        'client' => $client, // Passer le client à la vue
    ]);
}

#[Route("/login", name: "login")]
public function login(Request $request, AuthenticationUtils $authenticationUtils): Response
{
    // Vérifier si l'utilisateur est déjà authentifié
    if ($this->getUser()) {
        // Rediriger l'utilisateur vers la vue client/home.html.twig
        return $this->redirectToRoute('home');
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

        // Authentifier l'utilisateur
        $token = new UsernamePasswordToken($client, null, 'main', $client->getRoles());
        $this->get('security.token_storage')->setToken($token);
        $this->get('session')->set('_security_main', serialize($token));

        // Rediriger le client vers la page d'accueil
        $this->addFlash('success', 'Connexion réussie !');
        return $this->redirectToRoute('home');
    }

    // Afficher la page de connexion avec le formulaire
    return $this->render('client/login.html.twig', [
        'form' => $form->createView(),
        'last_username' => $lastUsername,
        'error' => $error,
    ]);
}

   #[Route("/modifiercompte", name: "modifiercompte")]
public function modifierCompte(Request $request): Response
{
    // Récupérer le client connecté
    $client = $this->getUser();

    // Créer le formulaire de modification de compte avec les données du client
    $form = $this->createForm(ClientType::class, $client, [
        'mapped' => false, // Exclude the 'Motdepasse' field from the form
    ]);
    // Gérer la soumission du formulaire
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
        // Mise à jour des informations du client dans la base de données
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();

        // Rediriger vers une page de succès ou afficher un message de confirmation
        // par exemple, en utilisant la méthode $this->addFlash() et en redirigeant vers une autre page.
    }

    // Afficher la page de modification de compte avec le formulaire
    return $this->render('client/modifiercompte.html.twig', [
        'form' => $form->createView(),
    ]);
}
#[Route("/createreclamation", name: "createreclamation")]
public function createReclamation(Request $request): Response
{
   
    // Créer une nouvelle instance de Reclamation
    $reclamation = new Reclamation();
      $form = $this->createForm(ReclamationType::class, $reclamation);
    // Traiter la soumission du formulaire
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Récupérer l'utilisateur connecté depuis le SecurityContext (ou le composant Security s'il est utilisé)
        $user = $this->getUser();

        // Définir les valeurs de la réclamation
        $reclamation->setNomUtilisateur($user->getNomprenom());

        // Sauvegarder la réclamation dans la base de données
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($reclamation);
        $entityManager->flush();

        // Rediriger vers une autre page après la soumission réussie du formulaire
        return $this->redirectToRoute('home');
    }

    // Afficher le formulaire dans le template
    return $this->render('client/reclamation.html.twig', [
        'form' => $form->createView(),
    ]);
}
#[Route("/logout", name: "logout")]
    public function logout(Request $request): Response
    {
        // Déconnecter l'utilisateur en supprimant son token d'authentification
        $this->get('security.token_storage')->setToken(null);
        $request->getSession()->invalidate();

        // Rediriger l'utilisateur vers la page de connexion
        return $this->redirectToRoute('login');
    }

    /**
     * @inheritDoc
     */
    public function onLogoutSuccess(Request $request): RedirectResponse
    {
        // Cette méthode est appelée après la déconnexion réussie.
        // Vous pouvez personnaliser la redirection après déconnexion ici.

        // Par exemple, rediriger l'utilisateur vers la page d'accueil :
        return $this->redirectToRoute('home');
    }

#[Route("/forgotpassword", name: "forgotpassword")]
public function forgotPassword(Request $request, UserPasswordEncoderInterface $passwordEncoder, TokenGeneratorInterface $tokenGenerator, \Swift_Mailer $mailer): Response
{
    $form = $this->createFormBuilder()
        ->add('email')
        ->getForm();

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $formData = $form->getData();
        $email = $formData['email'];

        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository(Client::class)->findOneBy(['Mail' => $email]);

        if (!$user) {
            throw new CustomUserMessageAuthenticationException('Aucun utilisateur n\'est associé à cette adresse e-mail.');
        }

        // Generate a token for password reset link
        $token = $tokenGenerator->generateToken();

        // Set the token and expiration date in the user entity
        $user->setResetToken($token);
        $user->setResetTokenExpiresAt(new \DateTime('+1 hour'));
        $entityManager->flush();

        // Send the password reset email
        $resetUrl = $this->generateUrl(
            'resetpassword',
            ['token' => $token],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $message = (new \Swift_Message('Réinitialisation de mot de passe'))
            ->setFrom('alaedineibrahim@gmail.com') // Replace with your email address
            ->setTo($email)
            ->setBody(
                $this->renderView('emails/reset_password.html.twig', [
                    'resetUrl' => $resetUrl,
                    'user' => $user,
                ]),
                'text/html'
            );

        $mailer->send($message);

        // Display success message and redirect to login page
        $this->addFlash('success', 'Un lien de réinitialisation de mot de passe a été envoyé à votre adresse e-mail.');
        return $this->redirectToRoute('login');
    }

    // Display the "Forgot Password" form
    return $this->render('client/forgotpassword.html.twig', [
        'form' => $form->createView(),
    ]);
}
#[Route("/ongoing-reclamations", name: "ongoing_reclamations", methods: ["GET"])]
    public function getOngoingReclamations(): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $reclamations = $entityManager->getRepository(Reclamation::class)->findBy(['Etat' => 'encours']);

        // Convert reclamations to an array to be returned as JSON
        $reclamationsArray = [];
        foreach ($reclamations as $reclamation) {
            $reclamationsArray[] = [
                'typeReclamation' => $reclamation->getTypeReclamation(),
                'branchement' => $reclamation->getBranchement(),
                'commentaire' => $reclamation->getCommentaire(),
            ];
        }

        // Return reclamations as JSON
        return $this->json($reclamationsArray);
    }

    #[Route("/ongoing-reclamations-count", name: "ongoing_reclamations_count", methods: ["GET"])]
    public function getOngoingReclamationsCount(): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $count = $entityManager->getRepository(Reclamation::class)->count(['Etat' => 'encours']);

        // Return count as JSON
        return $this->json($count);
    }

   
   #[Route("/list_ongoing_reclamations", name: "list_ongoing_reclamations")]
public function listOngoingReclamations(): Response
{
    // Récupérer les réclamations ayant l'état "encours" depuis la base de données
    $entityManager = $this->getDoctrine()->getManager();
    $ongoingReclamations = $entityManager->getRepository(Reclamation::class)->findBy(['Etat' => 'encours']);

    // Rendre le template Twig pour afficher les réclamations en cours
    return $this->render('client/ongoing_reclamations.html.twig', [
        'ongoingReclamations' => $ongoingReclamations,
    ]);
} 
       #[Route("/refus-reclamations", name: "refus_reclamations", methods: ["GET"])]
    public function getRefusReclamations(): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $reclamations = $entityManager->getRepository(Reclamation::class)->findBy(['Etat' => 'refus']);

        // Convert reclamations to an array to be returned as JSON
        $reclamationsArray = [];
        foreach ($reclamations as $reclamation) {
            $reclamationsArray[] = [
                'typeReclamation' => $reclamation->getTypeReclamation(),
                'branchement' => $reclamation->getBranchement(),
                'commentaire' => $reclamation->getCommentaire(),
            ];
        }

        // Return reclamations as JSON
        return $this->json($reclamationsArray);
    }

    #[Route("/refus-reclamations-count", name: "refus_reclamations_count", methods: ["GET"])]
    public function getRefusReclamationsCount(): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $count = $entityManager->getRepository(Reclamation::class)->count(['Etat' => 'rejete']);

        // Return count as JSON
        return $this->json($count);
    }

    
}













    
  
