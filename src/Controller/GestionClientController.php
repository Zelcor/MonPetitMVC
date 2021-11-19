<?php

namespace APP\Controller;

use APP\Model\GestionCommandeClientModel;
use APP\Model\GestionClientModel;
use ReflectionClass;
use Exception;
use Tools\MyTwig;
use APP\Entity\Client;
use APP\Entity\Commande;
use Tools\Repository;

class GestionClientController {
    
    public function chercheUn($params){
        //appel de la méthode find($id) de la classe Model adequate
        $repository= Repository::getRepository("APP\Entity\Client");
        $ids=$repository->findIds();
        $params["lesIds"]=$ids;
        if(array_key_exists("id",$params)){
            $id = filter_var(intval($params["id"]),FILTER_VALIDATE_INT);
            $unClient = $repository->find($id);
            $params['unClient']=$unClient;
        }
        $r = new ReflectionClass($this);
        $vue = str_replace('Controller', 'View', $r->getShortName()) . "/unClient.html.twig";
        MyTwig::afficheVue($vue, $params);
    }
    
    public function chercheTous() {
        //appel de la méthode findAll() de la classe model adequate
        $repository= Repository::getRepository("APP\Entity\Client");
        $clients = $repository->findAll();
        if($clients) {
            $r = new ReflectionClass($this);
            $vue = str_replace('Controller', 'View', $r->getShortName()) . "/tousClients.html.twig";
            MyTwig::afficheVue($vue, array('clients' => $clients));
        }
        $nbClients = $this->nbClients();
    }

    public function creerClient($params){
        if (empty($params)) {
            $vue = "GestionClientView\\creerClient.html.twig";
            MyTwig::afficheVue($vue,array());
        } else {
            //création de l'objet client
            $client = new Client($params);
            $repository = Repository::getRepository("APP\Entity\Client");
            $repository->insert($client);
            $this->chercheTous();
        }
    }

    public function enregistreClient($params){
        // creation de l'objet client
        $client = new Client($params);
        $modele = new GestionClientModel();
        $modele->enregistreClient($client);
    }
    
    public function nbClients()
    {
        $repository = Repository::getRepository("APP\Entity\Client");
        $nbClients = $repository->countRows();
        echo "nb clients = " . $nbClients;
    }
    
    public function testFindBy($params) {
        $repository = Repository::getRepository("APP\Entity\Client");
        $params = array("titreCli" => "Monsieur", "villeCli" => "Toulon");
        $clients = $repository->findBytitreCli_and_villeCli($params);
        //$params = array("cpCli" => "14000", "titreCli" => "Madame");
        //$clients = $repository->findBycpCli_and_titreCli($params);
        $r = new ReflectionClass($this);
        $vue = str_replace("Controller", "View", $r->getShortName()) . "/tousClients.html.twig";
        MyTwig::afficheVue($vue, array('clients' => $clients));
    }
        
    public function rechercheClients($params){
        $repository = Repository::getRepository("APP\Entity\Client");
        $titres = $repository->findColumnDistinctValues('titreCli');
        $cps = $repository->findColumnDistinctValues('cpCli');
        $villes = $repository->findColumnDistinctValues('villeCli');
        $params['titres'] = $titres;
        $params['cps'] = $cps;
        $params['villes'] = $villes;
        $vue = "GestionClientView\\filtreClients.html.twig";
        MyTwig::afficheVue($vue, $params);
    }
    
    public function recupereDesClients($params) {
        $repository = Repository::getRepository("APP\Entity\Client");
        $clients = $repository->findBy($params);
        $r = new ReflectionClass($this);
        $vue = str_replace('Controller', 'View', $r->getShortName()) . '/tousClients.html.twig';
        MyTwig::afficheVue($vue, array('clients' => $clients));
    }
    
    public function commandesUnClient($params){
        $repository= Repository::getRepository("APP\Entity\Client");
        $id = $params['id'];
        $client = $repository->find($params);
        $id = (int)$id;
        $modele = new GestionCommandeClientModel();
        $commandes = $modele->findCommandes($id);
        $vue = 'GestionCommandeClientView/CommandeClient.html.twig';
        MyTwig::afficheVue($vue, array('commandes' => $commandes));
        MyTwig::afficheVue($vue, $client);
    }
}
