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
        $modele = new GestionClientModel();
        $ids = $modele->findIds();
        $params['lesId']=$ids;
        if(array_key_exists("id",$params)){
            $id = filter_var(intval($params["id"]),FILTER_VALIDATE_INT);
            $unClient = $modele->find($id);
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
    }

    public function creerClient($params){
        $vue = "GestionClientView\\creerClient.html.twig";
        MyTwig::afficheVue($vue,array());
    }

    public function enregistreClient($params){
        // creation de l'objet client
        $client = new Client($params);
        $modele = new GestionClientModel();
        $modele->enregistreClient($client);
    }
}
