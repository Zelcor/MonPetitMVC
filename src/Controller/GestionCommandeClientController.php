<?php

namespace APP\Controller;

use APP\Model\GestionCommandeClientModel;
use ReflectionClass;
use Exception;
use Tools\MyTwig;
use APP\Entity\Client;
use Tools\Repository;

class GestionCommandeClientController {

public function chercheCommandes() {
        //appel de la méthode findAll() de la classe model adequate
        $modele = new GestionCommandeClientModel();
        $commandes = $modele->findAll();
        if($commandes) {
            $r = new ReflectionClass($this);
            $vue = str_replace('Controller', 'View', $r->getShortName()) . "/CommandeClient.html.twig";
            MyTwig::afficheVue($vue, array('clients' => $commandes));
        } else {
            throw new Exception("Aucune commande à afficher");
        }
    }
}