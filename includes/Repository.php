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
    
    public function findAll() {
        $sql = "select * from " . $this->table;
        $lignes = $this->connexion->query($sql);
        $lignes->setFetchMode(PDO::FETCH_CLASS, $this->classeNameLong, null);
        return $lignes->fetchAll();
    }
}