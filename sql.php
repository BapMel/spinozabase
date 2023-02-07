<?php
import_request_variables("gp");
$request = stripslashes($request);

function tableau ($result) {
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
    for ($i = 0; $i < $nb_cols; $i++) 
      $tableau .= "<td>" . $val[$i] . "</td>";
  }
  $tableau .= "</table>";
  return $tableau;
}

  // Appel à SQLite
$db = sqlite_open("ethica.db");

    if ($request) {
      $result = @sqlite_query($db, $request) or $message = "Requête invalide.";	
      $tableau = @tableau($result);
    }


/*
$liste_entites = "";
$liste_sql = @sqlite_query($db, "SELECT clavis FROM entitas;");
while($entite = sqlite_fetch_array($liste_sql)) {
  $liste_entites .= '<option value="' . $entite{'clavis'} . '" ';
  $liste_entites .= ($entite{'clavis'} == $cur_entite) ? 
    'selected="selected" ' 
    : "";
  $liste_entites .= '>' . $entite{'clavis'} . "</option>";
 }
*/

sqlite_close($db);


    // Construction du HTML
$title = "SpinozaBase";

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

      <li> <a href="statistiques.php"><img src="images/loupe.png" width="32"
      height="32" alt="Statistiques simples" align="middle" />
      Statistiques simples</a></li>

  <li class="active"> <a href="sql.php"><img src="images/outils.png" width="32"
  height="32" alt="Statistiques avancées" align="middle" />
  Statistiques avancées</a></li>

      <li> <a href="technologie.html"><img src="images/aide.png"
      width="32" height="32" alt="Manuel technique" align="middle" /> À
      propos</a> </li>

    </ul>
</div>



<div class="vert">
  <p> Cette page fournit une interface pour construire des statistiques avancées sur l'<em>Éthique</em>.</p></div>

<ol>

  <li> <strong>Utilisateurs débutants :</strong> Nous vous recommandons
    d'utiliser la page de <a href="statistiques.php">statistiques
    simples</a>. </li>

  <li> <strong>Utilisateurs avancés :</strong> Vous pouvez taper
directement vos requêtes en langage SQL dans le cadre ci-dessous.  Vous
trouverez de l'aide dans le <a href="technologie.html">manuel
technique</a> ; un cours plus pédagogique, agrémenté d'exemples et
d'exercices, est en cours de rédaction. </li>

</ol>
 


EOF;

$html_tail = <<< EOF
<p><a href="#haut">Haut de page</a></p>

</div>
</body>
</html>

EOF;

$default_request = "";

if (!$request) $request = $default_request;

echo $html_head;


echo <<< EOF

<!-- <a name="exemples"></a> <h3>Exemples</h3> <p> Voici quelques exemples de -->
<!-- requêtes que vous pouvez observer et imiter. Vous êtes cordialement -->
<!-- invités à suggérer toutes les extensions qui vous paraîtraient utiles -->
<!-- aux études spinozistes, <a -->
<!-- href="mailto:baptiste&#46;meles&#64;normalesup&#46;org">en nous -->
<!-- contactant</a>. -->

<!-- <table> -->

<!-- <tr> -->
<!-- <th>Description en langue naturelle</th> -->
<!-- <th>Requête SQL</th> -->
<!-- </tr> -->

<!-- <tr><td> Le palmarès, dans l'ordre, des entités les plus « populaires », c'est-à-dire les plus référencées, avec le nombre de citations de chacune -->
<!--  </td> <td> <a href="sql.php#result?type_requete=libre&request=SELECT ex, -->
<!--  count(ex) AS cnt FROM patetex GROUP BY ex ORDER BY cnt DESC;">SELECT -->
<!--  ex, count(ex) AS cnt <br/>FROM patetex <br/>GROUP BY ex <br/>ORDER BY -->
<!--  cnt DESC;</a> </td></tr> -->

<!-- <tr><td> Le palmarès, dans l'ordre, des entités les plus « généreuses », c'est-à-dire qui renvoient le plus à d'autres, avec le nombre de références </td> <td> <a href="sql.php#result?type_requete=libre&request=SELECT ad, count(ad) AS cnt FROM patetex GROUP BY ad ORDER BY cnt DESC;">SELECT ad, count(ad) AS cnt <br/>FROM patetex <br/>GROUP BY ad <br/>ORDER BY cnt DESC;</a> -->
<!--  </td></tr> -->

<!-- <tr><td> Les preuves autoréférentes (qui renvoient à elles-mêmes !) </td> <td> <a href="sql.php#result?type_requete=libre&request=SELECT ex AS Autoréférence FROM patetex WHERE ex=ad;">SELECT ex AS Autoréférence <br/>FROM patetex <br/>WHERE ex=ad;</a> -->
<!--  </td></tr> -->


<!--   <tr><td> Afficher la totalité de la -->
<!--     table des entités </td> <td> <a href="sql.php#result?type_requete=libre&request=SELECT * FROM entitas;">SELECT * <br/>FROM -->
<!--     entitas;</a> </td></tr> -\-> -->

<!--     <tr><td> Afficher la liste des définitions </td> <td> <a href="sql.php#result?type_requete=libre&request=SELECT clavis FROM entitas WHERE typus='definitio';">SELECT clavis<br/> FROM entitas<br/> WHERE typus='definitio';</a> </td></tr> -->

<!-- <tr><td> La liste des entités citant le scolie de la proposition 13 de la troisième partie </td> <td> <a href="sql.php#result?type_requete=libre&request=SELECT ad FROM patetex WHERE ex='313sc';">SELECT ad <br/>FROM patetex <br/>WHERE ex='313sc';</a> -->
<!--  </td></tr> -->


<!-- <tr><td> La liste des entités citant  la proposition 11 de la première partie </td> <td> <a href="sql.php#result?type_requete=libre&request=SELECT ad FROM patetex WHERE ex='111';">SELECT ad <br/>FROM patetex <br/>WHERE ex='111';</a> -->
<!--  </td></tr> -->

<!-- <tr><td> La liste des entités citant  la proposition 6 de la première partie <strong>ou ses sous-entités</strong> (corollaire, scolie...) </td> <td> <a href="sql.php#result?type_requete=libre&request=SELECT ex, ad FROM patetex WHERE ex='106' OR ex IN (SELECT intra FROM inest  WHERE circa='106');">SELECT ex, ad<br/> FROM patetex<br/> WHERE ex='106'<br/> OR ex IN (<br/>SELECT intra<br/> FROM inest<br/>  WHERE circa='106'<br/>);</a> -->
<!--  </td></tr> -->



<!-- <tr><td> La liste des <strong>types</strong> d'entités (partie, définition, axiome, proposition...) </td> <td> <a href="sql.php#result?type_requete=libre&request=SELECT DISTINCT typus FROM entitas;">SELECT DISTINCT typus <br/>FROM entitas;</a> -->
<!--  </td></tr> -\-> -->



<!--   <tr><td> Connaître le nombre total de propositions dans l'ouvrage </td> <td> <a href="sql.php#result?type_requete=libre&request=SELECT count(id) FROM entitas WHERE typus='propositio';">SELECT count(id) <br/>FROM entitas <br/>WHERE typus='propositio';</a> -->
<!--  </td></tr> -->


<!--   <tr><td> Connaître le nombre total de propositions dans la première partie </td> <td> <a href="sql.php#result?type_requete=libre&request=SELECT count(e.clavis) FROM entitas e, inest i WHERE typus='propositio' AND clavis=i.intra AND i.circa='p1';">SELECT count(e.clavis) <br/>FROM entitas e, inest i <br/>WHERE typus='propositio' AND clavis=i.intra AND i.circa='p1';</a> -->
<!--  </td></tr> -->

<!--   <tr><td> Connaître le nombre total de propositions dans chacune des parties </td> <td> <a href="sql.php#result?type_requete=libre&request=SELECT p1.clavis, count(p2.clavis) FROM entitas p1, entitas p2, inest WHERE p1.typus='pars' AND p2.typus='propositio' AND circa=p1.clavis AND intra=p2.clavis GROUP BY p1.clavis ORDER BY p1.id ASC; -->
<!-- ">SELECT p1.clavis, count(p2.clavis)<br/> FROM entitas p1, entitas p2, inest<br/> WHERE p1.typus='pars'<br/> AND p2.typus='propositio'<br/> AND circa=p1.clavis<br/> AND intra=p2.clavis<br/> GROUP BY p1.clavis<br/> ORDER BY p1.id ASC; -->
<!-- </a> -->
<!--  </td></tr> -->


<!--  <tr><td> Les définitions qui ne servent à rien </td> <td> <a href="sql.php#result?type_requete=libre&request=SELECT clavis from entitas WHERE typus='definitio' AND clavis NOT IN (SELECT ex from patetex);">SELECT clavis <br/>FROM -->
<!--   entitas <br/>WHERE typus='definitio' AND clavis NOT IN (SELECT ex from -->
<!--   patetex);</a> </td></tr> -\-> -->

</table>

<a name="result"></a>
<h3> Choix de la requête</h3>

<form method="post" action="sql.php" name="form">

<p><textarea cols="80" rows="10" name="request">$request</textarea></p>
</label></input>

<p><input type="submit" value="Soumettre" />
<input type="reset" value="Annuler" /></p>

</form>


EOF;

if ($request) {
echo <<<EOF
<h3>Résultat</h3>
<p> $message </p>
$tableau
EOF;
}

echo $html_tail;

?>
