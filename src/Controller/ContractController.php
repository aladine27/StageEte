<?php

// src/Controller/ContractController.php

namespace App\Controller;

use App\Entity\Contrat;
use App\Form\ContratSearchType;
use App\Form\ContratAssignmentType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContractController extends AbstractController
{
    #[Route("/assigncontract", name: "assigncontract")] 
    public function assignContract(Request $request): Response
    {
        // Create a new Contrat object to hold the form data
        $contract = new Contrat();

        // Create the form
        $form = $this->createForm(ContratAssignmentType::class, $contract);

        // Handle the form submission
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Get the selected client from the form
            $client = $contract->getClient();

            // Associate the contract with the client
            $client->addContrat($contract);

            // Persist the contract and client in the database
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($contract);
            $entityManager->persist($client);
            $entityManager->flush();

            // Redirect to a success page or show a success message
             // Add a success flash message
        $this->addFlash('success', 'Contract successfully assigned!');
            // For example, you can use $this->addFlash() to show a success message and then redirect to another page.
        }

        // Render the form template
        return $this->render('contract/assign_contract.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/contracts', name: 'contracts')]
public function displayContracts(): Response
{
    $entityManager = $this->getDoctrine()->getManager();
    $contracts = $entityManager->getRepository(Contrat::class)->findAll();

    return $this->render('contract/display_contracts.html.twig', [
        'contracts' => $contracts,
    ]);
}
#[Route('/edit-contract/{id}', name: 'edit_contract')]
public function editContract(Request $request, Contrat $contract): Response
{
    $form = $this->createForm(ContratAssignmentType::class, $contract);

    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();

        $this->addFlash('success', 'Contract updated successfully.');
        return $this->redirectToRoute('contracts');
    }

    return $this->render('contract/edit_contract.html.twig', [
        'form' => $form->createView(),
    ]);
}
#[Route('/delete-contract/{id}', name: 'delete_contract')]
public function deleteContract(Contrat $contract): Response
{
    $entityManager = $this->getDoctrine()->getManager();
    $entityManager->remove($contract);
    $entityManager->flush();

    $this->addFlash('success', 'Contract deleted successfully.');
    return $this->redirectToRoute('contracts');
}
#[Route('/fetch-contract', name: 'fetch_contract')]
public function fetchContract(Request $request): Response
{
    $contractNumber = $request->request->get('contract_number'); // Récupérer le numéro de contrat du formulaire

    $entityManager = $this->getDoctrine()->getManager();
    $contractRepository = $entityManager->getRepository(Contrat::class);

    $contractsToShow = $this->get('session')->get('contracts_to_show', []);

    if ($contractNumber) {
        $contrat = $contractRepository->findOneBy(['Num_Contrat' => $contractNumber]);

        if ($contrat) {
            // Ajoutez le contrat à la liste des contrats à afficher
            $contractsToShow[] = $contrat;
            // Stockez la liste mise à jour dans la session
            $this->get('session')->set('contracts_to_show', $contractsToShow);
        }
    }

    return $this->render('client/home.html.twig', [
        'contractsToShow' => $contractsToShow,
    ]);
}
#[Route('/remove-contract/{index}', name: 'remove_contract')]
public function removeContract(int $index, Request $request): Response
{
    $contractsToShow = $this->get('session')->get('contracts_to_show', []);

    if (array_key_exists($index, $contractsToShow)) {
        unset($contractsToShow[$index]);
        $this->get('session')->set('contracts_to_show', $contractsToShow);
    }

    return $this->redirectToRoute('fetch_contract');
}









}