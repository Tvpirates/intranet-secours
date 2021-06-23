<?php
use Josantonius\Session\Session;

Flight::route('/dossier-candidat', function() {
  verif_connecter();
  verif_enseignant(); // Vérifie que la personne soit bien un enseignant
  /* Variable récupéré dans le get */
  $identifiant = $_GET['identifiant'];
  /* Variable récupéré dans le get */
  $en_attente = Candidature::getListCandidature(1);
  $refusee = Candidature::getListCandidature(2);
  $accepte = Candidature::getListCandidature(3);
  $personne = NULL;
  $ecole = NULL;
  $vacance = NULL;
  $travail = NULL;
  $concat = NULL;

  if ($identifiant != NULL) {
    $personne = Candidature::getCandidature($identifiant);
    $personne->motivation_lspd = renderHTMLFromMarkdown(htmlspecialchars(strip_tags($personne->motivation_lspd)));
    $ecole = explode('-', $personne->detail_ecole);
    $vacance = explode('-', $personne->detail_vacance);
    $travail = explode('-', $personne->detail_travail);
    $concat = explode('-', $personne->concat);
  }

  Flight::view()->display('ecole/dossier_candidat.twig', array(
    'attente' => $en_attente,
    'refuser' => $refusee,
    'accepter' => $accepte,
    'personne' => $personne,
    'ecole' => $ecole,
    'vacance' => $vacance,
    'travail' => $travail,
    'concat' => $concat
  ));
});

Flight::route('/dossier-candidat/@decision/@num', function($decision, $num) {
  verif_connecter();
  verif_enseignant();

  switch ($decision) {
    case 'accepter':
      editCandidature($num, 3);
      addHistorique(Agent::getInfoAgent()->matricule, "2¤3¤0¤" . $num);
      break;
    case 'refuser':
      editCandidature($num, 2);
      addHistorique(Agent::getInfoAgent()->matricule, "2¤3¤1¤" . $num);
      break;
    default:
      editCandidature($num, 2);
      addHistorique(Agent::getInfoAgent()->matricule, "2¤3¤1¤" . $num);
      break;
  }
  Flight::redirect("/dossier-candidat?identifiant=");
});

?>
