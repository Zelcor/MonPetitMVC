<?php

namespace APP\Controller;

use APP\Model\GestionClientModel;
use ReflectionClass;
use Exception;
use Tools\MyTwig;
use APP\Entity\Client;
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
        $nbClients = $repository->countRows();
        if($clients) {
            $r = new ReflectionClass($this);
            $vue = str_replace('Controller', 'View', $r->getShortName()) . "/tousClients.html.twig";
            MyTwig::afficheVue($vue, array('clients' => $clients));
            //to do : finir
        }
        
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
    
    public function nbClients($params){
        $repository = Repository::getRepository("APP\Entity\Client");
        $nbClients= $repository->countRows();
        return "nombre de clients : " . $nbClients;
    }
}
