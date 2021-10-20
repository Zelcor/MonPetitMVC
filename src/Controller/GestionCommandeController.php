<?php

namespace APP\Controller;

use APP\Model\GestionClientModel;
use ReflectionClass;

class GestionCommandeController {
    
    public function chercheUne($params){
        //appel de la méthode find($id) de la classe Model adequate
        $modele = new GestionClientModel();
        $id = filter_var(intval($params["id"]),FILTER_VALIDATE_INT);
        $uneCommande = $modele->find($id);
        if($uneCommande){
            $r = new ReflectionClass($this);
            include_once PATH_VIEW . str_replace('Controller', 'View', $r->getShortName()) . "\unClient.php";
        } else {
            throw new Exception('Commande ' . $id . ' inconnu');
        }
    }
    public function chercheTous() {
        //appel de la méthode findAll() de la classe model adequate
        $modele = new GestionClientModel();
        $commandes = $modele->findAll();
        if($commandes) {
            $r = new ReflectionClass($this);
            include_once PATH_VIEW . str_replace('Controller', 'View', $r->getShortName()) . "/plusieursClients.php";
        } else {
            throw new Exception("Aucune commande à afficher");
        }
    }
}
