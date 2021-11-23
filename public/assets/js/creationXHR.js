function creationXHR(){
    var resultat=null;
    try{
        //test pour les navigateurs : Mozilla, Opéra, ...
        resultat= new XMLHttpRequest();
    }
    catch (Erreur){
        try{
            //test pour les navigateurs Internet Explorer > 5.0
            resultat= new ActiveObjet("Msxml2.XMLHTTP");
        }
        catch (Erreur){
            resultat=null;
        }
    }
    return resultat;
}