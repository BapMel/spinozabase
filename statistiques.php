<?php
import_request_variables("gp");
$request = stripslashes($request);


/* ****************************************************************
 Fonctions
 **************************************************************** */

function lien ($code) {
  $lien = "<a href=\"ethica.html#$code\">$code</a>";
  return $lien;
}

// Afficher en tableau un résultat SQL
function tableau ($result, $liens) {
  // Usage : tableau(resultat_SQL, colonnes_à_liens)

  $tableau = "<table>";
  $nb_cols =  sqlite_num_fields($result);

  // Titres de colonnes
  $tableau .= "<tr>";
  for ($i = 0; $i < $nb_cols; $i++) {
	$tableau .= "<th>" .  sqlite_field_name($result,$i) . "</th>";
  }
  $tableau .= "</tr>";

  // Remplissage du tableau
  while($val = sqlite_fetch_array($result)) {
    $tableau .= "<tr>";

    for ($i = 0; $i < $nb_cols; $i++) {
      $tableau .= "<td>";	

      // Si l'on a indiqué cette colonne comme devant contenir des
      // liens...
      $tableau .= (in_array($i, $liens)) ? lien($val[$i]) : $val[$i];
      $tableau .= "</td>";
    }
  }
  $tableau .= "</table>";
  return $tableau;
}

function graph_entites($db) {
  include 'Image/Graph.php';

  $entites = array("definitio", "axioma", "propositio", "scholium");

  $Graph =& Image_Graph::factory('graph', array(array('width' => 500,
                                                      'height' => 400,
                                                      'canvas' => 'png')));

  $Graph->add(
              Image_Graph::vertical(
                                    $Plotarea = Image_Graph::factory('plotarea'), 
                                    $Legend = Image_Graph::factory('legend'), 
            90 
), 8
);

  $GridY2 =& $Plotarea->addNew('bar_grid', IMAGE_GRAPH_AXIS_Y_SECONDARY);   
  $GridY2->setFillStyle( 
                        Image_Graph::factory( 
                                             'gradient',  
                                             array(IMAGE_GRAPH_GRAD_VERTICAL, 'white', 'lightgrey') 
                                              ) 
                         );     
 
  $AxisY =& $Plotarea->getAxis(IMAGE_GRAPH_AXIS_Y);
    $AxisY->setAxisIntersection(0);
  $AxisY->forceMaximum(80);

  $Legend =& $Plotarea->add(new Image_Graph_Legend());
    $Legend->setLineColor('');
  //  $Legend->setPlotarea($Plotarea); 
  //  $Plotarea =& $Graph->addNew('plotarea'); 

  foreach ($entites as $key => $val) 
     $Dataset[$key] =& Image_Graph::factory('dataset');

  foreach ($entites as $key => $val) {
    for ($pars = 1; $pars <= 5; $pars++) {
      $result = @sqlite_query($db, 
                              "SELECT count(e.clavis) AS cnt "
                              . "FROM entitas e, inest i "
                              . "WHERE typus='" . $val 
                              . "' AND clavis=i.intra AND i.circa='p" 
                              . $pars . "';");
      $entry = @sqlite_fetch_array($result);
      $Dataset[$key]->addPoint("Pars $pars", $entry{'cnt'});
    }
  }

  foreach ($entites as $key => $val) {
    $Plot[$key] =& $Plotarea->addNew('line', &$Dataset[$key]); 
    $Plot[$key]->setTitle($val);
  $Marker =& $Plot[$key]->addNew('Image_Graph_Marker_Value', IMAGE_GRAPH_VALUE_Y); 
  // create a pin-point marker type 
  $PointingMarker =& $Plot[$key]->addNew('Image_Graph_Marker_Pointing_Angular', array(10, &$Marker)); 
  // and use the marker on the 1st plot 
  $Plot[$key]->setMarker($PointingMarker);     
       }


  // couleurs
  $couleurs = array("red", "blue", "green", "black", "yellow");

  foreach ($entites as $key => $val) 
    $Plot[$key]->setLineColor($couleurs[$key]);

  // $Graph->done(); 
    $Graph->done(array('filename' => './graph_entites.png'));
  $tableau = '<img src="graph_entites.png" alt="Graphique des entités" />';
   return $tableau;

}


// Appel à SQLite
$db = sqlite_open("ethica.db");

if (!$type_requete && $cur_entite_tab) { $type_requete = "stat_croisees"; }

if ($type_requete) {
  if ($type_requete == "ascendance") {
    if (!$cur_entite) $message="Vous devez spécifier un code d'entité.";
    //       $cur_entite = $asc_entite;

 $result = @sqlite_query($db, $foo);
    // Le @ évite les messages d'erreur intempestifs.
    $result = @sqlite_query($db, 
                            'SELECT \'<a href="ethica.html#\' || clavis || \'">\'  || nomen || \'</a>\' AS \'Ascendance de ' . $cur_entite . '\' FROM patetex, entitas WHERE ex=clavis AND ad=\'' . $cur_entite . "';");
    //$liens = array (0);
    $tableau = @tableau($result, $liens);
  }

  elseif ($type_requete == "descendance") {
    if (!$cur_entite) $message="Vous devez spécifier un code d'entité.";
    //      $cur_entite = $desc_entite;
    $result = @sqlite_query($db, 
                            'SELECT \'<a href="ethica.html#\' || clavis || \'">\'  || nomen || \'</a>\' AS \'Descendance de ' . $cur_entite . '\' FROM patetex, entitas WHERE ad=clavis AND ex=\'' . $cur_entite . "';");
//                             "SELECT ad AS Descendance FROM patetex WHERE ex='" 
//                             . $cur_entite . "';");
//         $liens = array (0);
    $tableau = @tableau($result, $liens);
  }

//   elseif ($type_requete == "asc_large") {
//     if (!$cur_entite) $message="Vous devez spécifier un code d'entité.";
//     //      $cur_entite = $asc_large_entite;
//     $result = @sqlite_query($db, 
//                             "SELECT ex AS Ascendant, ad AS Descendant FROM patetex WHERE ad='" 
//                             . $cur_entite 
//                             . "' OR ad IN (SELECT intra FROM inest WHERE circa='" 
//                             . $cur_entite . "');");
//     $liens = array (0, 1);
//     $tableau = @tableau($result, $liens);
//   }



//   elseif ($type_requete == "desc_large") {
//     if (!$cur_entite) $message="Vous devez spécifier un code d'entité.";
//     $result = @sqlite_query($db, 
//                             "SELECT ex AS Ascendant, ad AS Descendant FROM patetex WHERE ex='" 
//                             . $cur_entite 
//                             . "' OR ex IN (SELECT intra FROM inest WHERE circa='" 
//                             . $cur_entite . "') ORDER BY id ASC;");
//     $liens = array (0, 1);
//     $tableau = @tableau($result, $liens);
//   }



  elseif ($type_requete == "popularite") {
    $result = @sqlite_query($db, 
                            "SELECT ex AS Entité, count(ex) AS Popularité FROM patetex GROUP BY ex ORDER BY Popularité DESC;");
    $liens = array (0);
    $tableau = @tableau($result, $liens);
  }

  elseif ($type_requete == "generosite") {
    $result = @sqlite_query($db, 
                            "SELECT ad AS Entité, count(ad) AS Générosité FROM patetex GROUP BY ad ORDER BY Générosité DESC;");
    $liens = array (0);
    $tableau = @tableau($result, $liens);
  }


  elseif ($type_requete == "nb_propositions") {
    $result = @sqlite_query($db, 
                            "SELECT p1.clavis AS Partie, count(p2.clavis) AS Propositions FROM entitas p1, entitas p2, inest WHERE p1.typus='pars' AND p2.typus='propositio' AND circa=p1.clavis AND intra=p2.clavis GROUP BY p1.clavis ORDER BY p1.id ASC;");
    $liens = array (0);
    $tableau = @tableau($result, $liens);
  }

  elseif ($type_requete == "nb_scolies") {
    $result = @sqlite_query($db, 
                            "SELECT p1.clavis AS Partie, count(p2.clavis) AS Scolies FROM entitas p1, entitas p2, inest WHERE p1.typus='pars' AND p2.typus='scholium' AND circa=p1.clavis AND intra=p2.clavis GROUP BY p1.clavis ORDER BY p1.id ASC;");
    $liens = array (0);
    $tableau = @tableau($result, $liens);
  }

  elseif ($type_requete == "nb_definitions") {
    $result = @sqlite_query($db, 
                            "SELECT p1.clavis AS Partie, count(p2.clavis) AS Définitions FROM entitas p1, entitas p2, inest WHERE p1.typus='pars' AND p2.typus='definitio' AND circa=p1.clavis AND intra=p2.clavis GROUP BY p1.clavis ORDER BY p1.id ASC;");
    $liens = array (0);
    $tableau = @tableau($result, $liens);
  }

  elseif ($type_requete == "nb_axiomes") {
    $result = @sqlite_query($db, 
                            "SELECT p1.clavis AS Partie, count(p2.clavis) AS Axiomes FROM entitas p1, entitas p2, inest WHERE p1.typus='pars' AND p2.typus='axioma' AND circa=p1.clavis AND intra=p2.clavis GROUP BY p1.clavis ORDER BY p1.id ASC;");
    $liens = array (0);
    $tableau = @tableau($result, $liens);
  }

  elseif ($type_requete == "graph_entites") {
   $tableau = graph_entites($db);
  }

  elseif ($type_requete == "stat_croisees") {
    //        print_r($cur_entite_tab);
    if (!$cur_entite_tab) $message="Vous devez spécifier un code d'entité.";
    $nb_entites = sizeof($cur_entite_tab);
    $requete = "SELECT '<a href=\"ethica.html#' || clavis || '\">'  || nomen || '</a>' AS 'Descendance' FROM entitas";
      for ($i=1; $i<=$nb_entites; $i++) {
        $requete .= ", patetex p$i";
      }
    $requete .= " WHERE p1.ad=clavis ";

      for ($i=1; $i<$nb_entites; $i++) {
        $requete .= " AND p" . $i . ".ad = p" . ($i + 1) . ".ad";
      }

      $i = 1;
    foreach($cur_entite_tab as $entite) {
      $requete .= " AND p$i.ex='$entite'";
      $i++;
    }
    //    echo $requete;
    $result = @sqlite_query($db, $requete);
    $tableau = @tableau($result, $liens);

  }


  // Rajouter ici les autres types de requête.
  $message='<a name="result"></a> <h3>Résultat</h3>';
 }


// Construction de la liste des entités (utile pour les champs
// ascendance, etc.)
$liste_entites = "";
$liste_sql = @sqlite_query($db, "SELECT clavis, nomen FROM entitas;");
while($entite = sqlite_fetch_array($liste_sql)) {
  $liste_entites .= '<option value="' . $entite{'clavis'} . '" ';
  if ($cur_entite == $entite{'clavis'}) {
    $liste_entites .= 'selected="selected" ';
  }
  $liste_entites .= '>' . $entite{'nomen'} . "</option>";
  }

$liste_entites_tab = "";
$liste_sql = @sqlite_query($db, "SELECT clavis, nomen FROM entitas;");
while($entite = sqlite_fetch_array($liste_sql)) {
  $liste_entites_tab .= '<option value="' . $entite{'clavis'} . '" ';
  if ($cur_entite_tab && in_array($entite{'clavis'}, $cur_entite_tab)) {
    $liste_entites_tab .= 'selected="selected" ';
  }
  $liste_entites_tab .= '>' . $entite{'nomen'} . "</option>";
  }
 

// $liste_entites_tab = "";
// $liste_sql = @sqlite_query($db, "SELECT clavis, nomen FROM entitas;");
// while($entite = sqlite_fetch_array($liste_sql)) {
//   $liste_entites_tab .= '<option value="' . $entite{'clavis'} . '" ';
//   $liste_entites_tab .= in_array($entite{'clavis'}, $cur_entite) ? 'selected="selected" ' : "";
//   // :  "";
//   $liste_entites_tab .= '>' . $entite{'nomen'} . "</option>";
//  }


sqlite_close($db);


// Construction du HTML
$title = "SpinozaBase";

// Inclusion de ma barre de navigation
// if (file_exists('plan.xml')) {
//   $nav = '<div id="nav">';
//   $xml = simplexml_load_file('plan.xml');
//   $plan = $xml->xpath('/plan/*');
//   while (list( , $node) = each($plan)) { $nav .= $node->asXML(); }
//   $nav .= '</div>';
//  }

$meta_keywords = 
  '<meta name="keywords" lang="fr" content="Spinoza, Ethica, statistiques"/>'
  . '<meta name="description" content="Statistiques sur Spinoza" />'	
  . '<meta http-equiv="Content-Language" content="fr" />';	
	
$html_head = <<<EOF
  <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

  <html xml:lang="fr" lang="fr" xmlns="http://www.w3.org/1999/xhtml">

  <head>
  <meta http-equiv="Content-Type" content="application/xhtml+xml;charset=UTF-8"
  />
  <title>$title</title>
  <meta name="author" content="Baptiste Mélès" />
  $meta_keywords
  <link rel="stylesheet" href="ethica.css" type="text/css"  media="screen" title
  ="Simple" />
  </head>

  <body onload="document.form.src_begin.focus();">
  <div id="tout">
  $nav

  <a name="haut"></a>
  <h1>$title</h1>

<div id="onglets">
    <ul>
      <li> <a href="index.html"><img src="images/accueil.png" width="32"
      height="32" alt="Accueil" align="middle"/> Accueil</a></li>

      <li> <a href="ethica.html"><img src="images/livre.png" width="32"
      height="32" alt="l'Éthique en latin"
      align="middle"/> <em>Ethica</em> hypertexte</a></li>

  <li class="active"> <a href="statistiques.php"><img src="images/loupe.png" width="32"
  height="32" alt="Statistiques simples" align="middle" />
  Statistiques simples</a></li>

      <li> <a href="sql.php"><img src="images/outils.png" width="32"
      height="32" alt="Statistiques avancées" align="middle" />
      Statistiques avancées</a></li>

      <li> <a href="technologie.html"><img src="images/aide.png"
      width="32" height="32" alt="Manuel technique" align="middle" /> À
      propos</a> </li>

    </ul>
</div>

<div class="vert">
  <p> Cette page fournit une interface pour construire des statistiques simples sur l'<em>Éthique</em> de Spinoza. </p></div>

<div class="important"> <strong>Attention</strong> : Par le terme générique d'<strong>entité</strong>, nous entendrons ci-dessous tout élément argumentatif de l'ouvrage : partie, définition, axiome, proposition, lemme, postulat,
scolie, corollaire, « autrement », appendice, préface...  </div>

EOF;

// <div class="encadre">
// <p>
// <strong>Mode d'emploi</strong> : </p>

//   <ol>
//   <li> Sélectionnez le <strong>type de statistiques</strong> que vous souhaitez élaborer, par exemple en cliquant sur « Ascendance de l'entité » ; </li>

// <li> Le cas échéant, sélectionnez </sélectionnez>l'<strong>entité</strong> (<em>i.e</em> la définition, l'axiome, la proposition, etc.) qui vous intéresse, par exemple 111 pour la proposition 11 de la première partie, ou 111d pour sa démonstration ; </li>

// <li> <strong>Envoyez</strong> la requête en cliquant sur le bouton
// « Soumettre ». </li> </ol>

// </div>

//EOF;

$html_tail = <<< EOF

<p> <a href="#haut">Haut de page</a> </p>

</div>
</body>
</html>

EOF;

$default_request = "";

if (!$request) $request = $default_request;

echo $html_head;



$radio_asc = '<input type="radio" name="type_requete" value="ascendance" id="ascendance" ';
$radio_asc .= ($type_requete == "ascendance") ? 'checked="checked" ' : "";
$radio_asc .= '/> <label for="ascendance">son <strong>ascendance</strong> (les entités auxquelles elle fait directement référence) </label>';

$radio_desc = '<input type="radio" name="type_requete" value="descendance" id="descendance" ';
$radio_desc .= ($type_requete == "descendance") ? 'checked="checked" ' : "";
$radio_desc .= '> <label for="descendance">sa <strong>descendance</strong> (les entités qui font directement référence à elle)</label>';

// $asc_large = '<input type="radio" name="type_requete" value="asc_large" id="asc_large" ';
// $asc_large .= ($type_requete == "asc_large") ? 'checked="checked" ' : "";
// $asc_large .= '/> <label for="asc_large"> son <strong>ascendance large</strong> (les entités auxquelles elle, ou l\'une de ses sous-entités, fait référence)</label>';

// $desc_large = '<input type="radio" name="type_requete" value="desc_large" id="desc_large" ';
// $desc_large .= ($type_requete == "desc_large") ? 'checked="checked" ' : "";
// $desc_large .= '/> <label for="desc_large">sa <strong>descendance large</strong> (les entités qui font référence à elle, ou à l\'une de ses sous-entités)</label>';

$popularite = '<input type="radio" name="type_requete" value="popularite" id="popularite" ';
$popularite .= ($type_requete == "popularite") ? 'checked="checked" ' : "";
$popularite .= '/> <label for="popularite">Classement des entités par <strong>popularité</strong> (<em>i.e</em> de la plus souvent référencée à la moins souvent référencée)</label>';

$generosite = '<input type="radio" name="type_requete" value="generosite" id="generosite" ';
$generosite .= ($type_requete == "generosite") ? 'checked="checked" ' : "";
$generosite .= '/> <label for="generosite">Classement des entités par <strong>générosité</strong> (<em>i.e</em> celles qui renvoient le plus souvent à d\'autres)</label>';

$nb_propositions = '<input type="radio" name="type_requete" value="nb_propositions" id="nb_propositions" ';
$nb_propositions .= ($type_requete == "nb_propositions") ? 'checked="checked" ' : "";
$nb_propositions .= '/> <label for="nb_propositions">Nombre de propositions par partie</label>';

$nb_scolies = '<input type="radio" name="type_requete" value="nb_scolies" id="nb_scolies" ';
$nb_scolies .= ($type_requete == "nb_scolies") ? 'checked="checked" ' : "";
$nb_scolies .= '/> <label for="nb_scolies">Nombre de scolies par partie</label>';

$nb_definitions = '<input type="radio" name="type_requete" value="nb_definitions" id="nb_definitions" ';
$nb_definitions .= ($type_requete == "nb_definitions") ? 'checked="checked" ' : "";
$nb_definitions .= '/> <label for="nb_definitions">Nombre de définitions par partie</label>';

$nb_axiomes = '<input type="radio" name="type_requete" value="nb_axiomes" id="nb_axiomes" ';
$nb_axiomes .= ($type_requete == "nb_axiomes") ? 'checked="checked" ' : "";
$nb_axiomes .= '/> <label for="nb_axiomes">Nombre d\'axiomes par partie</label>';

$graph_entites = '<input type="radio" name="type_requete" value="graph_entites" id="graph_entites" ';
$graph_entites .= ($type_requete == "graph_entites") ? 'checked="checked" ' : "";
$graph_entites .= '/> <label for="graph_entites">Graphique de synthèse</label>';

$stat_croisees = '<input type="radio" name="type_requete" value="stat_croisees" id="stat_croisees" ';
$stat_croisees .= ($type_requete == "stat_croisees") ? 'checked="checked" ' : "";
$stat_croisees .= '/>';





echo <<< EOF

  <!--<h3> Choix de la requête</h3>-->

<form method="post" action="$PHP_SELF#result" name="form">


<h4>Statistiques globales sur l'<em>Éthique</em></h4>

$popularite <br/>
$generosite <br/>

<p><input type="submit" value="Soumettre" />
<input type="reset" value="Annuler" /></p>


<h4>Statistiques sur une seule proposition, définition, etc.</h4>

<ol>

<li> Sélectionnez une entité  :
  <select name="cur_entite">$liste_entites</select></li> 

<li> Sélectionnez : <br/>
 $radio_asc <br/>
 $radio_desc <!-- <br/>
//  $asc_large <br/>
//  $desc_large -->
</li>

</ol>

<p><input type="submit" value="Soumettre" />
<input type="reset" value="Annuler" /></p>


<h4>Croiser des statistiques de descendance</h4>

<!-- <p> Maintenez la touche Control appuyée pour sélectionner plusieurs entités. </p> -->


$stat_croisees 


 <label for="stat_croisees">Croiser des statistiques (maintenez la
<strong>touche Control</strong> enfoncée pour sélectionner ou
désélectionner plusieurs entités) :

<p>
<select name="cur_entite_tab[]" size="20" multiple="multiple">$liste_entites_tab</select>
</p>
</label>




<p><input type="submit" value="Soumettre" />
<input type="reset" value="Annuler" /></p>




<!--
<h4>Fréquence des types d'entités</h4>

$nb_propositions <br/>
$nb_scolies <br/>
$nb_definitions <br/>
$nb_axiomes <br/>
$graph_entites

<p><input type="submit" value="Soumettre" />
<input type="reset" value="Annuler" /></p>
-->

</form>

$message 
$tableau

EOF;

echo $html_tail;

?>
