<?php

namespace Tools;

use Tools\Connexion;
use PDO;

class Repository {
    
    private $classeNameLong;
    private $classeNamespace;
    private $table;
    private $connexion;
    
    public  function __construct(string $entity) {
        $tablo = explode("\\", $entity);
        $this->table = array_pop($tablo);
        $this->classeNamespace = implode("\\", $tablo);
        $this->classeNameLong = $entity;
        $this->connexion= Connexion::getConnexion();
    }
    
    public static function getRepository($entity) {
        $repositoryName = str_replace("Entity", "Repository", $entity) . "Repository";
        $repository = new $repositoryName($entity);
        return $repository;
    }
    
    public function find($id) {
        $sql = "select * from " . $this->table . " where id=:id";
        $ligne = $this->connexion->prepare($sql);
        $ligne->bindvalue(':id',$id,PDO::PARAM_INT);
        $ligne->execute();
        return $ligne->fetchObject($this->classeNameLong);
        return self::pdo_debugStrParams($ligne);
    }
    
    public function findAll() {
        $sql = "select * from " . $this->table;
        $lignes = $this->connexion->query($sql);
        $lignes->setFetchMode(PDO::FETCH_CLASS, $this->classeNameLong, null);
        return $lignes->fetchAll();
    }
    
    public function findIds(){
        $sql = "select id from " . $this->table;
        $lignes = $this->connexion->query($sql);
        $ids = $lignes->fetchAll(PDO::FETCH_ASSOC);
        return $ids;
    }
        
    public function insert($objet) {
        $attributs= (array) $objet;
        array_shift($attributs);
        $colonnes = "(";
        $colonnesParams = "(";
        $parametres = array();
        foreach ($attributs as $cle => $valeur){
            $cle = str_replace("\O", "", $cle);
            $c = str_replace($this->classeNameLong, "", $cle);
            $p = ":" . $c;
            if ($c != "id") {
                $colonnes .= $c . " ,";
                $colonnesParams .= " ? ,";
                $parametres[] = $valeur;
            }
        }
        $colonnes = substr($colonnes, 0, -1);
        $colonnesParams = substr($colonnesParams, 0, -1);
        $sql = "insert into " . $this->table . "" . $colonnes . ") values " . $colonnesParams . ")";
        $unObjetPDO = Connexion::getConnexion();
        $req = $unObjetPDO->prepare($sql);
        $req->execute($parametres);
    }
    
    public function countRows(){
        $sql = "select count(*) as nb from " . $this->table;
        $ligne = $this->connexion->query($sql);
        $ligne->execute();
        return $ligne->fetchColumn()['nb'];
    }
    
    public function __call($methode,$params){
        if(preg_match("#^findBy#", $methode)) {
            return $this->traiteFindBy($methode, array_values($params[0]));
        }
    }
    
    private function traiteFindBy($methode, $params) {
        $criteres = str_replace("findBy", "", $methode);
        $criteres = explode("_and_", $criteres);
        if (count($criteres) > 0){
            $sql = 'select * from ' . $this->table . " where ";
            $pasPremier = false;
            foreach ($criteres as $critere){
                if($pasPremier){
                    $sql .= ' and ';
                }
                $sql .= $critere . " = ? ";
                $pasPremier = true;
            }
            $lignes = $this->connexion->prepare($sql);
            $lignes->execute($params);
            $lignes->setFetchMode(PDO::FETCH_CLASS, $this->classeNameLong, null);
            return $lignes->fetchAll();
        }
    }

    public function findColumnDistinctValues($colonne) {
        $sql = "select distinct " . $colonne . " libelle from " . $this->table . " order by 1;";
        return $this->connexion->query($sql)->fetchAll(PDO::FETCH_COLUMN);
    }

    public function findBy($params) {
        $element = "Choisir...";
        while(in_array($element, $params)){
            unset($params[array_search($element,$params)]);
        }
        $cles = array_keys($params);
        $methode = "findBy";
        for($i = 0; $i < count($cles); $i++){
            if($i > 0){
                $methode .= "_and_";
            }
            $methode .= $cles[$i];
        }
        return $this->traiteFindBy($methode, array_values($params));
    }
    
    public function modifieTable($objet){
        $tobjet = $this->object2Array($objet);
        $parametres = array();
        $sql = "update " . $this->table . " set ";
        foreach ($tobjet as $cle => $valeur) {
            var_dump($cle);
            if ($cle != "id"){
                if($this->gereNull($valeur)) {
                    $sql .= $cle . "= null,";
                } else {
                    $sql .= $cle . "= :" . $cle . ' ,';
                    $parametres[$cle] = $valeur;
                }
            }
        }
        $sql = substr($sql, 0, -1) . " where id =" . $tobjet['id'];
        $unObjetPDO = Connexion::getConnexion();
        $req = $unObjetPDO->prepare($sql);
        $req->execute($parametres);
    }
    
    public function object2array($objet) {
        $tObjet = (array) $objet;
        $tabloRetour = array();
        foreach($tObjet as $cle => $valeur) {
            $cle = str_replace("\0", "", $cle);
            $cle = str_replace($this->classeNameLong, "", $cle);
            $tabloRetour[$cle] = $valeur;
        }
        return $tabloRetour;
    }
    
    private function gereNull($variable) {
        $retour = false;
        if ($variable == '_null_' || $variable == '0') {
            $retour = true;
        }
        return $retour;
    }
}