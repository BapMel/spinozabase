<?xml version="1.0" encoding="utf-8" ?>

<xsl:stylesheet version="1.0"
		xmlns:xsl="http://www.w3.org/1999/XSL/Transform" >

  <xsl:output method="xml" version="1.0" encoding="utf-8"
		  omit-xml-declaration="yes"
	      indent="yes"
	      doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"
	      doctype-public="-//W3C//DTD XHTML 1.0 Strict//EN"/>


  <!-- Titulus -->

  <xsl:template match="/">
    <html xml:lang="fr" lang="fr">
      <head>
	<meta http-equiv="Content-Type"
	      content="application/xhtml+xml;charset=UTF-8" />
	<title>Spinoza : Ethica</title>
	<meta http-equiv="Content-Language" content="fr" />
	<link href="ethicadb.css" rel="stylesheet" type="text/css" />
      </head>

      <body>

<!-- <div id="nav"> -->
<!-- <a href="http://baptiste.meles.free.fr/">Baptiste Mélès</a> -->
<!-- <xsl:copy-of select="document('plan.xml')/plan/ul" /> -->
<!-- </div> -->

<!-- <div id="corps"> -->
	<h1>SpinozaBase</h1>

<div id="onglets">
    <ul>
      <li> <a href="index.html"><img src="images/accueil.png" width="32"
      height="32" alt="Accueil" align="middle"/> Accueil</a></li>

      <li class="active"><a href="ethica.html"><img src="images/livre.png"
      width="32" height="32" alt="l'Éthique en latin"
      align="middle"/> <em>Ethica</em> hypertexte</a></li>

      <li> <a href="statistiques.php"><img src="images/loupe.png" width="32"
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
<p>
  Cette édition du <strong>texte latin</strong> de l'<em>Éthique</em> de
  Spinoza contient : </p>

<ul>
<li> Des <strong>liens hypertextes</strong>, qui facilitent la
  navigation dans le texte au moyen des nombreux renvois entre entités
  argumentatives (nous entendons par là l'ensemble des définitions,
  propositions, axiomes, etc.).</li>

<li> Un cadre rouge intitulé « <em>Supra</em> », qui contient la
liste de toutes les entités auxquelles renvoie celle que nous lisons
  (son « <strong>ascendance</strong> »).</li>

<li> Un cadre vert intitulé « <em>Infra</em> », qui contient la liste
des entités qui renverront plus loin à celle-ci (sa
« <strong>descendance</strong> »). </li>

</ul>

<p>
</p>
</div>


	<ol>
	  <xsl:apply-templates mode="index" />
	</ol>

	<xsl:apply-templates />

<!-- </div> -->
      </body>
    </html>
  </xsl:template>





  <!-- How to display each "part" (pars) of the book -->
  <xsl:template match="entitas[@typus='pars']">
    <h2>

      <a name="{@id}"> </a>

      <xsl:value-of select="concat('Pars ', @numerus, ' : ', @titulus)"/>

    </h2>

    <!-- Display an index at the beginning of each part --> 
    <xsl:apply-templates mode="index-propositiones" select="."/>
    <xsl:apply-templates/>
  </xsl:template>

  <!-- List of all elements contained in a part (propositions,
       definitions, etc.) -->
  <xsl:template match="//entitas[@typus='pars']" mode="index-propositiones">

    <ul>

      <xsl:for-each select="entitas">

	<xsl:call-template name="index-element" />
      </xsl:for-each>

    </ul>

  </xsl:template>

  <!-- How to display each one of those elements -->  
  <xsl:template name="index-element">
    <li>
      <a>
	<xsl:attribute name="href">
	  <xsl:value-of select="concat('#', @id)" />
	</xsl:attribute>
	<xsl:call-template  name="quae_entitas" />
      </a>

      <xsl:if test="@typus='propositio'">
	<xsl:text> - </xsl:text>
	<xsl:value-of select="verba" />
      </xsl:if>
    </li>
  </xsl:template>

  <!-- Propositiones, praefatio, appendix... -->
  <xsl:template match="entitas[@typus='pars']/entitas">
    <div class="entitas">

      <a>
        <xsl:attribute name="name">
          <xsl:value-of select="@id"/>
        </xsl:attribute>

        <xsl:attribute name="id">
          <xsl:value-of select="concat('id', @id)"/>
        </xsl:attribute>
      </a> 

      <h3>
	<xsl:call-template  name="quae_entitas" />
      </h3>

      <xsl:call-template  name="familia" />
      <xsl:apply-templates/>
    </div>
  </xsl:template>


  <!-- Propositiones -->
  <xsl:template match="//entitas/verba/p">
    <p>
      <xsl:choose>
	<xsl:when test="../../@typus='propositio' or
			../../@typus='definitio' or
			../../@typus='axioma' or 
			../../@typus='postulatum'">
	  <em>
	    <xsl:apply-templates/>
	  </em>   
	</xsl:when>

	<xsl:otherwise>
	  <xsl:apply-templates/>
	</xsl:otherwise>
      </xsl:choose>
    </p>
  </xsl:template>

  <!-- Corollaria, scolia, demonstrationes, etc. -->
  <xsl:template match="/ethica/entitas/entitas//entitas">

    <div class="subentitas">
      <a>
        <xsl:attribute name="name">
          <xsl:value-of select="@id"/>
        </xsl:attribute>

        <xsl:attribute name="id">
          <xsl:value-of select="concat('id', @id)"/>
        </xsl:attribute>
      </a> 

      <h4>
	<xsl:call-template  name="quae_entitas" />
      </h4>

      <xsl:call-template  name="familia"/>
      <xsl:apply-templates/>
    </div>
  </xsl:template>



  <!-- Verba propositionum, etc. -->

  <xsl:template match="verba">
    <xsl:apply-templates />
  </xsl:template>



  <!-- Patet ex ... -->

  <xsl:template match="patet-ex">
    <xsl:element name="a">
      <xsl:attribute name="href">
	<xsl:text>#</xsl:text> <xsl:value-of select="@id" />
      </xsl:attribute>
      <xsl:value-of select="." />
    </xsl:element>
  </xsl:template>



  <!-- <p> -->

  <xsl:template match="p">
    <p>
      <xsl:apply-templates/>
    </p>
  </xsl:template>


  <xsl:template match="/ethica/entitas[@typus='pars']" mode="index">
    <li>
      <a>
	<xsl:attribute name="href"> 
	  <xsl:text>#</xsl:text>
	  <xsl:value-of select="@id" />
	</xsl:attribute>

	<xsl:value-of select="@titulus" />
      </a>
    </li>
  </xsl:template>


  <xsl:template name="quae_entitas">
    <xsl:choose>
      <xsl:when test="@typus='definitio'">
	<xsl:text>Definitio</xsl:text> 
	<xsl:text> </xsl:text> 
	<xsl:value-of select="@numerus" />
      </xsl:when>

      <xsl:when test="@typus='affectuum_definitiones'">
	<xsl:text>Affectuum definitiones</xsl:text> 
	<xsl:text> </xsl:text> 
	<xsl:value-of select="@numerus" />
      </xsl:when>

      <xsl:when test="@typus='affectuum_generalis_definitio'">
	<xsl:text>Affectuum generalis definitio</xsl:text> 
	<xsl:text> </xsl:text> 
	<xsl:value-of select="@numerus" />
      </xsl:when>

      <xsl:when test="@typus='definitiones'">
	<xsl:text>Definitiones</xsl:text> 
      </xsl:when>

      <xsl:when test="@typus='propositio'">
	<xsl:text>Propositio</xsl:text>
	<xsl:text> </xsl:text>
	<xsl:value-of select="@numerus" />
      </xsl:when>

      <xsl:when test="@typus='appendix'">
	<xsl:text>Appendix</xsl:text>
      </xsl:when>

      <xsl:when test="@typus='explicatio'">
	<xsl:text>Explicatio</xsl:text>
      </xsl:when>

      <xsl:when test="@typus='demonstratio'">
	<xsl:text>Demonstratio</xsl:text>
      </xsl:when>

      <xsl:when test="@typus='scholium'">
	<xsl:text>Scholium</xsl:text>
	<xsl:text> </xsl:text>
	<xsl:value-of select="@numerus" />
      </xsl:when>

      <xsl:when test="@typus='corollarium'">
	<xsl:text>Corollarium</xsl:text>
	<xsl:text> </xsl:text>
	<xsl:value-of select="@numerus" />
      </xsl:when>

      <xsl:when test="@typus='aliter'">
	<xsl:text>Aliter</xsl:text>
	<xsl:text> </xsl:text>
	<xsl:value-of select="@numerus" />
      </xsl:when>

      <xsl:when test="@typus='axioma'">
	<xsl:text>Axioma</xsl:text>
	<xsl:text> </xsl:text>
	<xsl:value-of select="@numerus" />
      </xsl:when>

      <xsl:when test="@typus='axiomata'">
	<xsl:text>Axiomata</xsl:text>
      </xsl:when>

      <xsl:when test="@typus='lemma'">
	<xsl:text>Lemma</xsl:text>
	<xsl:text> </xsl:text>
	<xsl:value-of select="@numerus" />
      </xsl:when>

      <xsl:when test="@typus='postulatum'">
	<xsl:text>Postulatum</xsl:text>
	<xsl:text> </xsl:text>
	<xsl:value-of select="@numerus" />
      </xsl:when>

      <xsl:when test="@typus='postulata'">
	<xsl:text>Postulata</xsl:text>
      </xsl:when>

      <xsl:when test="@typus='praefatio'">
	<xsl:text>Praefatio</xsl:text>
      </xsl:when>

      <xsl:when test="@typus='caput'">
	<xsl:text>Caput</xsl:text>
	<xsl:text> </xsl:text>
	<xsl:value-of select="@numerus" />
      </xsl:when>

      <xsl:otherwise>
	<xsl:text>XXXXXXXXXX Type d'entité inconnu !!! XXXXXXXXXX</xsl:text>
      </xsl:otherwise>

    </xsl:choose>
  </xsl:template>

  <xsl:template  name="familia">
    <div class="ad">
      <p>Infra :</p>
      <xsl:variable name="id" select="@id" /> 
    <!--  <ul> -->

	<xsl:for-each select="//patet-ex"> 
	  <xsl:variable name="cid" select="@id" />

	  <xsl:if test="$cid = $id">

	   <!-- <li> -->

	      <a>

		<xsl:attribute name="href">
		  <xsl:text>#</xsl:text>
		  <xsl:choose>
		    <xsl:when test="../../../@typus='demonstratio'">
		      <xsl:value-of select="../../../../@id" />
		    </xsl:when>

		    <xsl:otherwise>
		      <xsl:value-of select="../../../@id" />
		    </xsl:otherwise>
		  </xsl:choose>
		</xsl:attribute>

		<xsl:choose>
		  <xsl:when test="../../../@typus='demonstratio'">
		    <xsl:value-of select="../../../../@id" />
		  </xsl:when>

		  <xsl:otherwise>
		    <xsl:value-of select="../../../@id" />
		  </xsl:otherwise>
		</xsl:choose>

	      </a>
<br/>
	   <!-- </li> -->

	  </xsl:if>

	</xsl:for-each>

     <!-- </ul> -->

    </div>

    <div class="ex">

      <p>Supra :

<!--      <ul> -->

	<xsl:variable name="c" select="."/>

	<xsl:for-each select="/ethica//entitas">
	  <xsl:variable name="eid" select="@id"/>

	  <xsl:for-each select="$c/verba/p/patet-ex">
	    <xsl:if test="@id = $eid">

<!--	      <li> -->

		<a>
		  <xsl:attribute name="href">
		    <xsl:text>#</xsl:text>
		    <xsl:value-of select="@id" />
		  </xsl:attribute>

		  <!-- 		  <xsl:call-template name="natural_name"/> -->

<!--    <xsl:for-each select="ancestor::*"> -->
<!--           <xsl:if test="ancestor::*"> -->
<!--             <xsl:value-of select="concat(@typus, ' ', @numerus, ', ')"/> -->
<!--           </xsl:if> -->
<!--         </xsl:for-each> -->

<!--         <xsl:value-of select="concat(@typus, ' ', @numerus)"/> -->

		  
		  <!-- Solution de repli -->
		      <xsl:value-of select="@id" />
		</a>

<br/>
<!--	      </li> -->

	    </xsl:if>
	  </xsl:for-each>
	</xsl:for-each>

     <!-- </ul> -->
</p>
    </div>

  </xsl:template>

  <xsl:template match="br">

    <br/>

  </xsl:template>

  <xsl:template match="p/em">
    <em>
      <xsl:apply-templates/>
    </em>
  </xsl:template>


  <xsl:template name="natural_name">

  </xsl:template>


</xsl:stylesheet>
