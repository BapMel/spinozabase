<?xml version="1.0" encoding="UTF-8" ?>
<xsl:stylesheet version="1.0"
                xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output method="text" encoding="UTF-8"/>
  <xsl:template match="/">

CREATE TABLE entitas (id INTEGER PRIMARY KEY NOT NULL, clavis TEXT NOT
NULL, typus INTEGER NOT NULL, numerus INTEGER, titulus TEXT, nomen TEXT);

CREATE TABLE patetex (id INTEGER PRIMARY KEY NOT NULL, ex INTEGER NOT
NULL, ad INTEGER NOT NULL);

CREATE TABLE inest (id INTEGER PRIMARY KEY NOT NULL, circa INTEGER NOT
NULL, intra INTEGER NOT NULL);

    <xsl:for-each select="//entitas">
      <xsl:variable name="typus"><xsl:value-of select="@typus"/></xsl:variable>
      <xsl:variable name="numerus"><xsl:value-of select="@numerus"/></xsl:variable>
      <xsl:variable name="titulus"><xsl:value-of select="@titulus"/></xsl:variable>
      <xsl:variable name="clavis"><xsl:value-of select="@id"/></xsl:variable>

      <xsl:variable name="nomen">

        <xsl:for-each select="ancestor::*">
          <xsl:if test="ancestor::*">
            <xsl:value-of select="concat(@typus, ' ', @numerus, ', ')"/>
          </xsl:if>
        </xsl:for-each>

        <xsl:value-of select="concat(@typus, ' ', @numerus)"/>

      </xsl:variable>

INSERT INTO entitas (typus, numerus, titulus, clavis, nomen)
VALUES ('<xsl:value-of select="$typus"/>',
      '<xsl:value-of select="$numerus"/>',
      '<xsl:value-of select="$titulus"/>',
      '<xsl:value-of select="$clavis"/>',
      '<xsl:value-of select="$nomen"/>');

      <xsl:for-each select="verba/p/patet-ex">
      <xsl:variable name="ex"><xsl:value-of select="@id"/></xsl:variable>
INSERT INTO patetex(ex, ad) 
VALUES ('<xsl:value-of select="$ex"/>', '<xsl:value-of select="$clavis"/>');
    </xsl:for-each>

      <xsl:for-each select=".//entitas">
      <xsl:variable name="intra"><xsl:value-of select="@id"/></xsl:variable>
INSERT INTO inest(circa, intra) 
VALUES ('<xsl:value-of select="$clavis"/>', '<xsl:value-of select="$intra"/>');
      </xsl:for-each>

    </xsl:for-each>

  </xsl:template>
</xsl:stylesheet>
