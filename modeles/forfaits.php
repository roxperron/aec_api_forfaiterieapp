
<?php

require_once "./include/config.php";

class modele_lodging {
    public $name;
    public $description;
    public $address;
    public $city;
    public $postalcode;
    public $phonenumber;
    public $email;
    public $website;

    public function __construct($lodging_name, $lodging_description, $lodging_address, $lodging_city, $lodging_postalcode, $lodging_phonenumber, $lodging_email, $lodging_website){
        $this->name= $lodging_name;
        $this->description= $lodging_description;
        $this->address= $lodging_address;
        $this->city= $lodging_city;
        $this->postalcode= $lodging_postalcode;
        $this->phonenumber= $lodging_phonenumber;
        $this->email= $lodging_email;
        $this->website= $lodging_website;
    }

}

class modele_forfait {
    public $id; 
    public $code; 
    public $name;
    public $description;
   /*  public $lodging_name;
    public $lodging_description;
    public $lodging_address;
    public $lodging_city;
    public $lodging_postalcode;
    public $lodging_phonenumber;
    public $lodging_email;
    public $lodging_website; */
    public $startdate;
    public $enddate;
    public $price;
    public $newprice;
    public $prenium;

    public function __construct($id, $code, $name, $description, $lodging_name, $lodging_description, $lodging_address, $lodging_city, $lodging_postalcode, $lodging_phonenumber, $lodging_email, $lodging_website, $dateStart, $dateEnd, $regular_price, $promotion_price, $premium) {
        $this->id = $id;
        $this->code = $code;
        $this->name = $name;
        $this->description = $description;
        $this->startdate = $dateStart;
        $this->enddate = $dateEnd;
        $this->price = $regular_price;
        $this->newprice = $promotion_price;
        $this->prenium = boolval($premium);
        $this->lodging = new modele_lodging($lodging_name, $lodging_description, $lodging_address, $lodging_city, $lodging_postalcode, $lodging_phonenumber, $lodging_email, $lodging_website);
    }

 
    static function connecter() {
        
        $mysqli = new mysqli(Db::$host, Db::$username, Db::$password, Db::$database);

        if ($mysqli -> connect_errno) {
            echo "Échec de connexion à la base de données MySQL: " . $mysqli -> connect_error;   
            exit();
        } 

        return $mysqli;
    }

 
    public static function ObtenirTous() {
        $liste = [];
        $mysqli = self::connecter();

        $resultatRequete = $mysqli->query("SELECT * FROM packages ORDER BY id");

        foreach ($resultatRequete as $enregistrement) {
            $liste[] = new modele_forfait($enregistrement['id'], $enregistrement['code'], $enregistrement['name'], $enregistrement['description'],  $enregistrement['lodging_name'],  $enregistrement['lodging_description'],  $enregistrement['lodging_address'],  $enregistrement['lodging_city'],  $enregistrement['lodging_postalcode'],  $enregistrement['lodging_phonenumber'],  $enregistrement['lodging_email'],  $enregistrement['lodging_website'],  $enregistrement['dateStart'],  $enregistrement['dateEnd'],  $enregistrement['regular_price'],  $enregistrement['promotion_price'],  $enregistrement['premium']);
        }

        return $liste ;
    }

    public static function ObtenirUn($id) {
        $mysqli = self::connecter();
    
        if ($requete = $mysqli->prepare("SELECT * FROM packages WHERE id=?")) {  
            $requete->bind_param("s", $id); 
    
            $requete->execute(); 
            $result = $requete->get_result(); 
            if($enregistrement = $result->fetch_assoc()) { 
                $forfaits = new modele_forfait($enregistrement['id'], $enregistrement['code'], $enregistrement['name'], $enregistrement['description'],  $enregistrement['lodging_name'],  $enregistrement['lodging_description'],  $enregistrement['lodging_address'],  $enregistrement['lodging_city'],  $enregistrement['lodging_postalcode'],  $enregistrement['lodging_phonenumber'],  $enregistrement['lodging_email'],  $enregistrement['lodging_website'],  $enregistrement['dateStart'],  $enregistrement['dateEnd'],  $enregistrement['regular_price'],  $enregistrement['promotion_price'],  $enregistrement['premium']);
            } else {
              
                return null;
            }   
            
            $requete->close(); 
        } else {
            echo "Une erreur a été détectée dans la requête utilisée : ";   
            echo $mysqli->error;
            return null;
        }
    
        return $forfaits;
    }


    
      public static function ajouter($code, $name, $description, $lodging_name, $lodging_description, $lodging_address, $lodging_city, $lodging_postalcode, $lodging_phonenumber, $lodging_email, $lodging_website, $dateStart, $dateEnd, $regular_price, $promotion_price, $premium) {
        $resultat = new stdClass();

        $mysqli = self::connecter();
        
        if ($requete = $mysqli->prepare("INSERT INTO packages (code, name, description, lodging_name, lodging_description, lodging_address, lodging_city, lodging_postalcode, lodging_phonenumber, lodging_email, lodging_website, dateStart, dateEnd, regular_price, promotion_price, premium) VALUES( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?)")) {      


        $requete->bind_param("sssssssssssssddi", $code, $name, $description, $lodging_name, $lodging_description, $lodging_address, $lodging_city, $lodging_postalcode, $lodging_phonenumber, $lodging_email, $lodging_website, $dateStart, $dateEnd, $regular_price, $promotion_price, $premium);

        if($requete->execute()) { 
            $resultat->message = "Forfait ajouté!";  
        } else {
            http_response_code(500); 
            $resultat->message =  "Une erreur est survenue lors de l'ajout"; 
            $resultat->erreur = $requete->error;
        }

        $requete->close(); 

        } else  {
            http_response_code(500); 
            $resultat->message = "Une erreur a été détectée dans la requête utilisée : ";
            $resultat->erreur = $mysqli->error;
        }

        return $resultat;
      }

      
    public static function modifier($id,$code, $name, $description, $lodging_name, $lodging_description, $lodging_address, $lodging_city, $lodging_postalcode, $lodging_phonenumber, $lodging_email, $lodging_website, $dateStart, $dateEnd, $regular_price, $promotion_price, $premium) {
        $resultat = new stdClass();

        $mysqli = self::connecter();
        
        if ($requete = $mysqli->prepare("UPDATE packages SET code=?, name=?, description=?, lodging_name=?, lodging_description=?, lodging_address=?, lodging_city=?, lodging_postalcode=?, lodging_phonenumber=?, lodging_email=?, lodging_website=?, dateStart=?, dateEnd=?, regular_price=?, promotion_price=?, premium=? WHERE id=?")) {      


        $requete->bind_param("sssssssssssssddii",$code, $name, $description, $lodging_name, $lodging_description, $lodging_address, $lodging_city, $lodging_postalcode, $lodging_phonenumber, $lodging_email, $lodging_website, $dateStart, $dateEnd, $regular_price, $promotion_price, $premium, $id);

        if($requete->execute()) { 
            $resultat->message = "Forfait modifié!";  
        } else {
            http_response_code(500); 
            $resultat->message =  "Une erreur est survenue lors de l'édition: "; 
            $resultat->erreur = $requete->error;
        }

        $requete->close(); 
        } else  {
            http_response_code(500);
            $resultat->message = "Une erreur a été détectée dans la requête utilisée : ";
            $resultat->erreur = $mysqli->error;
        }

        return $resultat;
      }


      public static function supprimer($id) {
        $resultat = new stdClass();

        $mysqli = self::connecter();
        
       
        if ($requete = $mysqli->prepare("DELETE FROM packages WHERE id=?")) {      

       

        $requete->bind_param("i", $id);

        if($requete->execute()) { 
            $resultat->message = "Forfait supprimé!";  
        } else {
            http_response_code(500); 
            $resultat->message = "Une erreur est survenue lors de la suppression: ";  
            $resultat->erreur = $requete->error;
        }

        $requete->close(); 

        } else  {
            http_response_code(500); 
            $resultat->message = "Une erreur a été détectée dans la requête utilisée : ";
            $resultat->erreur = $mysqli->error;
        }

        return $resultat;
    }



      





}

 
    



?>
