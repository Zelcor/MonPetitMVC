<?php
namespace APP\Model;

use \PDO;
use APP\Entity\Commande;
use Tools\Connexion;

class GestionCommandeClientModel {
    public function findCommandes($id) {
        $unObjetPdo = Connexion::getConnexion();
        $sql = "select * from COMMANDE where idclient=:id";
        $lignes = $unObjetPdo->prepare($sql);
        $lignes-> bindValue(':id', $id, PDO::PARAM_INT);
        $lignes-> execute();
        return $lignes->fetchAll(PDO::FETCH_CLASS, Commande::class);
    }
}