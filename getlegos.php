<?php

require_once('vendor/autoload.php');
$console = new League\CLImate\CLImate;
$formats = ['V29','V.29','V 29','V. 29'];

function recupListe($nom_fichier)
{
    $fichier = fopen($nom_fichier,'r');
    while ($ligne = fgetcsv($fichier)) {
        $legos[$ligne[0]] = array('id' => $ligne[0], 'desc_fr' => $ligne[1]);
    }
    fclose($fichier);
    return $legos;
}

function url2json($url)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $json = curl_exec($ch);
    curl_close($ch);
    return json_decode($json);
}

function recupInfos($id)
{
    global $console;

    if (file_exists(__DIR__ . '/img/'.$id.'.photo.jpg') or file_exists(__DIR__ . '/img/'.$id.'.photo.png')) {
        $console->yellow(' EXISTE DÉJÀ');
        return array();
    }

    $url_rech = 'https://www.lego.com//service/biservice/getCompletionList?count=10&locale=fr-FR&onlyAlternatives=false&prefixText='.$id;
    $rech = url2json($url_rech);
    $url_pdf = 'https://www.lego.com//service/biservice/search?fromIndex=0&locale=fr-FR&onlyAlternatives=false&prefixText='.urlencode($rech[0]);
    $data = url2json($url_pdf);

    if ($data->count > 1) {
        echo "STOP ! ".$id;
        exit;
    }

    $produit = $data->products;

    // Sauvegarde photo
    $infos_photo = 'img/' . $produit[0]->productId.'.photo.'.substr($produit[0]->productImage,-3);
    exec('wget -q '.$produit[0]->productImage.' -O ' . __DIR__ . '/'.$infos_photo);

    foreach ($produit[0]->buildingInstructions as $bi) {
        $description = str_replace(['V39','V.39','V 39','V. 39'],'V39',strtoupper($bi->description));
        if (strpos(substr($description,-4),'/') !== false) {
            $num_complet = explode('/',substr($description,-4));
            $num = intval($num_complet[0]);
            $max = intval($num_complet[1]);
        }
        else {
            $num++;
            $max = count($produit[0]->buildingInstructions);
        }
        if (strpos($description,'V39') === false) {
            // Sauvegarde photos pdf
            $urlphoto = 'img/' . $produit[0]->productId.'.'.$num.'_'.$max.'.pdf.'.substr($bi->frontpageInfo,-3);
            $urlpdf = 'pdf/' . $produit[0]->productId.'.'.$num.'_'.$max.'.pdf';
            exec('wget -q '.$bi->frontpageInfo.' -O ' . __DIR__ . '/'.$urlphoto);
            exec('wget -q '.$bi->pdfLocation.' -O ' . __DIR__ . '/'.$urlpdf);

            $console->inline('.');

            $plans[] = array(
                'description' => $description,
                'taille' => $bi->downloadSize,
                'num' => $num,
                'max' => $max,
                'photo' => $urlphoto,
                'pdf' => $urlpdf
            );
        }
    }

    return array(
        'id'    => $produit[0]->productId,
        'nom'   => $produit[0]->productName,
        'theme' => $produit[0]->themeName,
        'annee' => $produit[0]->launchYear,
        'photo' => $infos_photo,
        'plans' => $plans
    );
}

function recCSV($infos)
{
    $out_csv = fopen(__DIR__ . '/out.csv','a');
    foreach ($infos['plans'] as $plan) {
        $csv = array($infos['id'],$infos['nom'],$infos['theme'],$infos['annee'],$infos['photo'],str_replace(['"',','],' ',$plan['description']),$plan['taille'],$plan['num'],$plan['max'],$plan['photo'],$plan['pdf']);
        fputcsv($out_csv,$csv);
    }
    fclose($out_csv);
}

function moulinette($id)
{
    global $console;
    $console->white()->bold()->inline($id.'');
    $infos = recupInfos($id);
    if (!empty($infos['id'])) {
        $console->green()->bold()->inline(' OK ');
        $console->out($infos['nom'].' / '.$infos['theme'].' ('.$infos['annee'].') ');
        recCSV($infos);
    }
}

if (is_numeric($argv[1])) {
    moulinette($argv[1]);
}
elseif (!empty($argv[1])) {
    // Format de fichier CSV où la première colonne est le numéro de la boîte Légo
    $legos = recupListe($argv[1]);
    foreach ($legos as $id => $lego) {
        moulinette($id);
    }
}
else {
    $console->red()->inline('ERREUR: ');
    $console->out('Syntaxe :
    Récupérer la boîte n°7939    : php getlegos.php 7939
    Récupérer une liste de légos : php getlegos.php listedelegos.csv (la première colonne étant le numéro de chaque boîte)

Tout est ensuite téléchargé dans img/ et pdf/ par numéro et index de notice s\'il existe (sinon il est deviné autant que possible)

');
    exit;
}
