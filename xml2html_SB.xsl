<xsl:stylesheet version="1.0"
                xmlns:xsl="http://www.w3.org/1999/XSL/Transform" >

  <xsl:output method="xml" version="1.0" encoding="utf-8"
              omit-xml-declaration="yes"
              indent="yes"
              doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"
              doctype-public="-//W3C//DTD XHTML 1.0 Strict//EN"/>

  <xsl:template match="/" >

    <html xml:lang="fr" lang="fr">
      <head>
		<xsl:copy-of select="page/title"/>
        <meta name="author" content="Baptiste Mélès" />
        <meta name="keywords" lang="fr" content="" />
        <meta name="description" content="Site personnel de Baptiste Mélès" />
        <meta http-equiv="Content-Type" content="text/html;charset=UTF-8"
              />
        <meta http-equiv="Content-Language" content="fr" />

        <link rel="stylesheet" href="ethica.css" type="text/css" media="screen"
              title="Simple" />
		<link rel="alternate" type="application/rss+xml" title="RSS" href="http://baptiste.meles.free.fr/meles.rss"/>
      </head>

      <body>
        <div id="tout">

<!-- 		  <div id="corps"> -->
		    <xsl:copy-of select="/page/html/*" />
<!-- 		  </div> -->
        </div>
      </body>
    </html>

  </xsl:template>

</xsl:stylesheet>

