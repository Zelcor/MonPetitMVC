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
        $ligne = $this->connexion->prepare($sql);
        $ligne->execute();
        return $ligne->fetch()['nb'];
    }
}