<?php

namespace App\Controller;

use App\Entity\Facture;
use App\Form\FactureType;
use App\Repository\FactureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Stripe\Charge;
use Stripe\Stripe;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Stripe\Checkout\Session;



#[Route('/facture')]
class FactureController extends AbstractController
{
    #[Route('/', name: 'app_facture_index', methods: ['GET'])]
    public function index(FactureRepository $factureRepository): Response
    {
        return $this->render('facture/index.html.twig', [
            'factures' => $factureRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_facture_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $facture = new Facture();
        $form = $this->createForm(FactureType::class, $facture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($facture);
            $entityManager->flush();

            return $this->redirectToRoute('app_facture_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('facture/new.html.twig', [
            'facture' => $facture,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_facture_show', methods: ['GET'])]
    public function show(Facture $facture): Response
    {
        return $this->render('facture/show.html.twig', [
            'facture' => $facture,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_facture_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Facture $facture, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(FactureType::class, $facture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_facture_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('facture/edit.html.twig', [
            'facture' => $facture,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_facture_delete', methods: ['POST'])]
    public function delete(Request $request, Facture $facture, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$facture->getId(), $request->request->get('_token'))) {
            $entityManager->remove($facture);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_facture_index', [], Response::HTTP_SEE_OTHER);
    }
#[Route('/factures_impayees', name: 'factures_impayees', methods: ['GET'])]
public function facturesImpayees(FactureRepository $factureRepository): Response
{
    // Récupérer l'utilisateur connecté (client)
    $client = $this->getUser();

    if (!$client) {
        throw new \LogicException('User not authenticated.');
    }

    // Récupérer les identifiants des contrats du client
    $contratIds = $client->getContrats()->map(fn($contrat) => $contrat->getId())->toArray();

    // Récupérer les factures impayées des contrats du client
    $facturesImpayees = $factureRepository->findBy(['contrat' => $contratIds, 'Etat' => 'impaye']);

    return $this->render('facture/factureimpayees.html.twig', [
        'facturesImpayees' => $facturesImpayees,
    ]);

    
}

 #[Route("/factures_impayees_count", name: "factures_impayees_count", methods: ["GET"])]
    public function countFacturesImpayees(EntityManagerInterface $entityManager): JsonResponse
    {
        // Récupérer l'utilisateur connecté (client)
        $client = $this->getUser();

        if (!$client instanceof Client) {
            throw new \LogicException('This should not happen.');
        }

        // Compter le nombre de factures impayées pour le client actuel
        $facturesRepository = $entityManager->getRepository(Facture::class);
        $countFacturesImpayees = $facturesRepository->count(['contrat' => $client->getContrats(), 'Etat' => 'impaye']);

        return $this->json(['factures_impayees_count' => $countFacturesImpayees]);
    }
 
public function payerFacture(Request $request, int $factureId, FactureRepository $factureRepository): Response
{
    $facture = $factureRepository->find($factureId);  // Change $id to $factureId

    if (!$facture) {
        throw $this->createNotFoundException('Facture not found');
    }

    // Initialisation de l'API Stripe
    Stripe::setApiKey('sk_test_51MxeCgJi8fxdcmA2nxH4sVCqk9idQ85jXFgyWhiFIauOqnodabwzLNaOXGhJcIn3uOptBbJJs1MmH9yDkeadeEyF00kRxoP7o8');

    // Création de la session de paiement
    $session = Session::create([
        'payment_method_types' => ['card'],
        'mode' => 'payment',
        'line_items' => [[
            'price_data' => [
                'currency' => 'eur',
                'unit_amount' => $facture->getNetApayer() * 100,
                'product_data' => [
                    'name' => 'Paiement de facture',
                ],
            ],
            'quantity' => 1, // Paiement d'une seule facture
        ]],
        'success_url' => $this->generateUrl('facture_paiement_reussi', ['factureId' => $facture->getId()], UrlGeneratorInterface::ABSOLUTE_URL),
        'cancel_url' => $this->generateUrl('facture_paiement_annule', ['factureId' => $facture->getId()], UrlGeneratorInterface::ABSOLUTE_URL),
    ]);

    // Redirection de l'utilisateur vers la page de paiement Stripe
    return $this->redirect($session->url);
}
#[Route('/paiement/reussi/{factureId}', name: 'facture_paiement_reussi')]
public function paiementReussi(Request $request, int $factureId, EntityManagerInterface $entityManager): Response
{
    // Récupérer la facture depuis la base de données
    $facture = $entityManager->getRepository(Facture::class)->find($factureId);

    if (!$facture) {
        throw $this->createNotFoundException('Facture not found');
    }

    // Mettre à jour l'état de la facture en "payé"
    $facture->setEtat('paye');
    $entityManager->flush();

    // Générer le PDF avec Dompdf
    $options = new Options();
    $options->set('defaultFont', 'Arial');
    $dompdf = new Dompdf($options);

    $html = $this->renderView('facture/recu_paiement.html.twig', [
        'facture' => $facture, // Passer des données à votre template Twig
    ]);

    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    // Envoyer le PDF généré en tant que réponse
    $response = new Response($dompdf->output());
    $response->headers->set('Content-Type', 'application/pdf');

    return $response;
}



#[Route('/paiement/annule/{factureId}', name: 'facture_paiement_annule')]
public function paiementAnnule(Request $request, int $factureId): Response
{
    // Gérer le paiement annulé, par exemple afficher un message d'erreur
    // ...

    return $this->render('facture/paiement_annule.html.twig');
}
    // ...
}











