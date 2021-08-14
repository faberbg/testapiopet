<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test REST Api</title>

    <script
    src="https://code.jquery.com/jquery-3.5.1.min.js"
    integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0="
    crossorigin="anonymous"></script>
</head>
<?php
    
    include "obrada.php";

?>
<body>
    <h1>Forma za manipulaciju sa API-em</h1>

    <!-- Radio button grupa za odabir tipa tabele iz baze koji želimo da menjamo -->
    <form action="" method="post">
        <div id="odabir_tabele">
            <input type="radio" name="odabir_tabele" id="radio_kategorija" value="kategorije">
            <label for="radio_kategorija">kategorija</label>
            <input type="radio" name="odabir_tabele" id="radio_novosti" value="novosti">
            <label for="radio_novosti">novosti</label>
        </div>


    <!-- Radio button grupa za odabir tipa HTTP zahteva koji želimo da pošaljemo -->
    
        <div id="http_zahtev">
            <input type="radio" name="http_zahtev" id="get" value="get">
            <label for="get">GET</label>
            <input type="radio" name="http_zahtev" id="post" value="post">
            <label for="post">POST</label>
            <input type="radio" name="http_zahtev" id="put" value="put">
            <label for="put">PUT</label>
            <input type="radio" name="http_zahtev" id="delete" value="delete">
            <label for="delete">DELETE</label>
        </div>

        <!-- Div sekcija za prikaz odgovora za GET zahtev sa servera
        HTML tag <pre></pre> nam omogućava da ispisuje prethodno formatiran tekst, što nam je potrebno za pretty prikaz JSON-a -->
        
        <pre id="get_odgovor"></pre>

        <!-- Div sekcija za POST formu za novosti -->

        <div id="novosti_post">
            <input type="text" name="naslov_novosti" placeholder="Unesite naslov novosti">
            <br>
            <textarea name="tekst_novosti" id="tekst_novosti" cols="30" rows="10" placeholder="Unesite tekst novosti"></textarea>
            <br>

            <label for="kategorija_odabir">Kategorija:</label>
            <select name="kategorija_odabir" id="kategorija_odabir">
            <?php
                $mydb->select("kategorije","*",null,null,null);
                while($red=$mydb->getResult()->fetch_object()):
            ?>
                <option value="<?php echo $red->id;?>"><?php echo $red->kategorija;?></option>
                <?php endwhile;?>        
            </select>
        </div>

        <!-- Div sekcija za POST formu za kategorije -->

        <div id="kategorije_post">
            <input type="text" name="kategorija_naziv" id="kategorija_naziv" placeholder="Unesite naziv nove kategorije">
        </div>

        <!-- Div sekcija za DELETE formu za novosti i kategorije -->

        <div id="brisanje_reda">
            <input type="text" name="brisanje" id="brisanje" placeholder="Unesite id koji želite da obrišete">
        </div>

        <!-- Div sekcija za PUT formu za kategorije -->

        <div id="kategorije_put">
            <input type="text" name="kategorija_id" id="kategorija_id" placeholder="Unesite ID kategorije">
            <br>
            <input type="text" name="kategorija_naziv_put" id="kategorija_naziv_put" placeholder="Unesite novi naziv kategorije">
        </div>

        <!-- Div sekcija za PUT formu za novosti -->

        <div id="novosti_put">
            <input type="text" name="novosti_id" id="novosti_id" placeholder="Unesite ID novosti">
            <br>
            <input type="text" name="naslov_novosti_put" placeholder="Unesite novi naslov novosti">
            <br>
            <textarea name="tekst_novosti_put" id="tekst_novosti_put" cols="30" rows="10" placeholder="Unesite novi tekst novosti"></textarea>
            <br>

            <label for="kategorija_odabir_put">Odaberite novu kategoriju:</label>
            <select name="kategorija_odabir_put" id="kategorija_odabir_put">
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
            </select>
            
        </div>

        <!-- Div sekcija za ispisivanje grešaka u slučaju pogrešne selekcije radio button-a -->

        <div id="greska">

        <!-- Div sekcija za za dugme preko kojeg će se slati zahtevi -->

        </div>
        <div id="submit">
            <input type="submit" name="posalji" id="posalji" value="Posalji zahtev">
        </div>
    </form>
    
</body>

<script>
    var nizBlokova = ["get_odgovor", "novosti_post", "kategorije_post", "brisanje_reda", "kategorije_put", "novosti_put", "greska"];

    //na samom početku želimo da sakrijemo sve blokove, dok korisnik ne odabere tip tabele i HTTP zahteva
    function skloniBlokove(){
        //prolazimo kroz niz blokova
        for(const blok of nizBlokova){
            //i vrednost display atributa u okviru css-a postavljamo na none, kako se ne bi prikazivali
            document.getElementById(blok).style.display="none";
        }
    };
    //pozivamo funkciju da se izvrši
    skloniBlokove();

    //prikaziBlok funkcija koristeći switch prolazi kroz sve tipove zahteva koji mogu biti odabrani
    //$("input[name=http_zahtev]:checked")[0].id je jQuery funkcija koja nam omogućava da 
    // dođemo do svih čekiranih input polja čiji je name http_zahtev 
    // i da pristupimo njegovom id-u jer su kao id postavljene vrednosti get post put delete 
    function prikaziBlok(){
        switch($("input[name=http_zahtev]:checked")[0].id){
            case "get":
            // u slučaju da odaberemo get, sakrićemo sve prethodno prikazane div-ove
                skloniBlokove();
                //obrisaćemo unutrašnji HTML get_odgovor bloka 
                document.getElementById("get_odgovor").innerHTML="";
                // i prikazati ga da bude vidljiv, promenom atributa display sa none na block
                document.getElementById(nizBlokova[0]).style.display="block";
                break;
            case "post":
            // u slučaju da odaberemo post, sakrićemo sve prethodno prikazane div-ove
                skloniBlokove();
                //proverićemo da li je odabrana tabela novosti ili kategorije
                if($("input[name=odabir_tabele]:checked").length==0){
                    //ako nije, želimo da se prikaže div blok za grešku i ispiše poruka da mora biti obeležena greška tabela 
                    document.getElementById(nizBlokova[6]).innerHTML="Morate odabrati tabelu za manipulaciju";
                    document.getElementById(nizBlokova[6]).style.display="block";
                }else{
                    //ako jeste odabrana tabela, odnosno length nije 0
                    //uzećemo koja je to tabela, odnosno id tog radio button-a
                    var tabela = $("input[name=odabir_tabele]:checked")[0].id;
                    if(tabela=="radio_kategorija"){
                        //i u slučaju da je u pitanju tabela kategorije
                        //prikazaćemo post formu za kategorije
                        document.getElementById(nizBlokova[2]).style.display="block";
                    }else if(tabela=="radio_novosti"){
                        //u suprotnom prikazaćemo post formu za novosti
                        document.getElementById(nizBlokova[1]).style.display="block";
                    }
                }
                
                break;
            case "put":
            // u slučaju da odaberemo put, sakrićemo sve prethodno prikazane div-ove
                skloniBlokove();
                //proverićemo da li je odabrana tabela novosti ili kategorije
                if($("input[name=odabir_tabele]:checked").length==0){
                    //ako nije, želimo da se prikaže div blok za grešku i ispiše poruka da mora biti obeležena greška tabela 
                    document.getElementById(nizBlokova[6]).innerHTML="Morate odabrati tabelu za manipulaciju";
                    document.getElementById(nizBlokova[6]).style.display="block";
                }else{
                    //ako jeste odabrana tabela, odnosno length nije 0
                    //uzećemo koja je to tabela, odnosno id tog radio button-a
                    var tabela = $("input[name=odabir_tabele]:checked")[0].id;
                    if(tabela=="radio_kategorija"){
                        //i u slučaju da je u pitanju tabela kategorije
                        //prikazaćemo put formu za kategorije
                        document.getElementById(nizBlokova[4]).style.display="block";
                    }else if(tabela=="radio_novosti"){
                        //u suprotnom prikazaćemo put formu za novosti
                        document.getElementById(nizBlokova[5]).style.display="block";
                    }
                }
                break;
            case "delete":
            //poslednja opcija nam je prikaz bloka za brisanje elemenata iz određene tabele
                skloniBlokove();
                //proverićemo da li je odabrana tabela novosti ili kategorije
                if($("input[name=odabir_tabele]:checked").length==0){
                    //ako nije, želimo da se prikaže div blok za grešku i ispiše poruka da mora biti obeležena greška tabela 
                    document.getElementById(nizBlokova[6]).innerHTML="Morate odabrati tabelu za manipulaciju";
                    document.getElementById(nizBlokova[6]).style.display="block";
                }else{
                     //ako jeste odabrana tabela, odnosno length nije 0
                    //prikazaćemo put formu za kategorije
                    var tabela = $("input[name=odabir_tabele]:checked")[0].id;
                    document.getElementById(nizBlokova[3]).style.display="block";
                }
                break;        
            default:
                break;
        }
    }

        //funkcija resetHTTP nam samo resetuje odabrane HTTP zahteve nakon promene odabrane tabele  
    function resetHTTP(){
        skloniBlokove();
        $("input[name=http_zahtev]").prop('checked', false);
    }

    function posaljiZahtev(){
        if($("input[name=odabir_tabele]:checked").length!=0 && $("input[name=http_zahtev]:checked").length!=0 ){
            var tabela = $("input[name=odabir_tabele]:checked")[0].id;

            switch($("input[name=http_zahtev]:checked")[0].id){
                case "get":
                    if(tabela=="radio_novosti"){
                        $.getJSON("http://localhost:8080/rest/api/novosti", function(podaci){
                            document.getElementById("get_odgovor").innerHTML = JSON.stringify(podaci, null, 2);
                        });
                    }else{
                        $.getJSON("http://localhost:8080/rest/api/kategorije", function(data){
                            document.getElementById("get_odgovor").innerHTML = JSON.stringify(data, null, 2);
                        });
                    }
                    break;
                case "post":
                    if(tabela=="radio_novosti"){
                        var values={
                            "naslov": $("input[name=naslov_novosti]").val() ,
                            "tekst": $("#tekst_novosti").val(),
                            "kategorija_id": parseInt($("#kategorija_odabir").val())
                        };
                        console.log(values);
                        $.post("http://localhost:8080/rest/api/novosti",JSON.stringify(values), function(data){
                            alert("Odgovor od servera> "+data["poruka"]);
                        } );
                    }else{
                        var values={
                            "kategorija": $("input[name=kategorija_naziv]").val() 
                        };
                        console.log(values);
                        $.post("http://localhost:8080/rest/api/kategorije",JSON.stringify(values), function(data){
                            alert("Odgovor od servera> "+data["poruka"]);
                        } );
                    }
                    break;
                case "put":
                    if(tabela=="radio_novosti"){
                        var values={
                            "naslov": $("input[name=naslov_novosti_put]").val() ,
                            "tekst": $("#tekst_novosti_put").val(),
                            "kategorija_id": parseInt($("#kategorija_odabir_put").val())
                        };
                        $.ajax({
                            url:"http://localhost:8080/rest/api/novosti/"+parseInt($("input[name=novosti_id]").val()),
                            type:"PUT",
                            data:JSON.stringify(values)
                        }).done(function(data){
                            alert("Odgovor sa servera> "+data["poruka"]);
                        });
                    }else{
                        var values={
                            "kategorija": $("input[name=kategorija_naziv_put").val() 
                        };
                        $.ajax({
                            url:"http://localhost:8080/rest/api/kategorije/"+parseInt($("input[name=kategorija_id]").val()),
                            type:"PUT",
                            data:JSON.stringify(values)
                        }).done(function(data){
                            alert("Odgovor sa servera> "+data["poruka"]);
                        });
                    }
                    break;
                case "delete":
                    if(tabela=="radio_novosti"){
                        $.ajax({
                            url:"http://localhost:8080/rest/api/novosti/"+parseInt($("input[name=brisanje]").val()),
                            type:"DELETE",
                            data:JSON.stringify(values)
                        }).done(function(data){
                            alert("Odgovor sa servera> "+data["poruka"]);
                        });
                    }else{
                        $.ajax({
                            url:"http://localhost:8080/rest/api/kategorije/"+parseInt($("input[name=brisanje]").val()),
                            type:"DELETE",
                            data:JSON.stringify(values)
                        }).done(function(data){
                            alert("Odgovor sa servera> "+data["poruka"]);
                        });
                    }
                    break;
                default:
                    console.log("zahtev nije prosao");
            }
        }
    }

    $("input[name=http_zahtev]").on('click',prikaziBlok);
    $("input[name=odabir_tabele]").on('click',resetHTTP);
    // $("button").on('click', posaljiZahtev);



</script>
</html>