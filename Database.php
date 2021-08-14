<?php

class Database{

    private $hostname = "localhost";
    private $username = "root";
    private $password ="";
    private $dbname;
    private $dblink; //veza sa bazom
    private $result; //pamtimo rezultate iz baze
    private $records; //broj redova koji su vraceni iz baze
    private $affected; //broj svih izmenjenih redova nakon izvrsenog upita

    function __construct($par_dbname){ //konstruktor

        $this->dbname = $par_dbname;
        $this->Connect();  //konekcija
    }

    function Connect(){
        $this->dblink = new mysqli($this->hostname,$this->username,$this->password,$this->dbname);
        //vraca niz objekata , bitno nam je da li se desila greska
        if($this->dblink->connect_errno){
            printf("Konekcija neuspesna: %s \n", $this->dblink->connect_error);
            exit();
        }
        $this->dblink->set_charset("utf8");
    }

    //funkcija za izvrsavanje query-ja
    function ExecuteQuery($query){
        $this->result = $this->dblink->query($query); //postavljamo rezultate na ono sto vraca query (a to je objekat)
        if($this->result){  // ako je popunjen tj. nije null ulazi u if i uzimamo dalje informacije iz njega
            if(isset($this->result->num_rows)){
                $this->records = $this->result->num_rows;  //upisujemo u rekorde koliko je popunjeno redova
            }
            if(isset($this->result->affected_rows)){
                $this->affected=$this->result->affected_rows; //upisujemo u rekorde koliko je izmenjeno redova
            }

            return true;

        }else{
            return false;
        }
    }

    //funkcija za dobijanje rezultata
    function getResult(){
        return $this->result; //samo vracamo rezultat
    }

    //funkcija select // treba nam koja je tabela , koji su redovi
    function select($table="novosti",$rows="*",$join_table="kategorije",$join_key1="kategorija_id",$join_key2="id",$where=null,$order=null){
        $q = 'SELECT '.$rows.' FROM '.$table; //  . spaja stringoce kao npr. concat
        // SELECT * FROM novosti;
        if($join_table!=null){
            $q.=' JOIN '.$join_table.' ON '.$table.'.'.$join_key1.'='.$join_table.'.'.$join_key2;
            //SELECT * FROM novosti JOIN kategorije ON novosti.kategorija_id=kategorije.id
          }
          if($where!=null){
              $q.=' WHERE '.$where;
          }
          if($order!=null){
              $q.=' ORDER BY '.$order; 
          }
          $this->ExecuteQuery($q);
    }

    //funkcija insert
    function insert(){}

    //funkcija update
    function update(){}

    //funkcija delete
    function delete(){}

}


?>