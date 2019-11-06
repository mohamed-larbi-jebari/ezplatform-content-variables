<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet
        xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
        xmlns:docbook="http://docbook.org/ns/docbook"
        xmlns:xlink="http://www.w3.org/1999/xlink"
        xmlns:ezxhtml="http://ez.no/xmlns/ezpublish/docbook/xhtml"
        xmlns:ezcustom="http://ez.no/xmlns/ezpublish/docbook/custom"
        exclude-result-prefixes="docbook xlink ezxhtml ezcustom"
        version="1.0">
  <xsl:output indent="yes" encoding="UTF-8"/>
  <xsl:variable name="outputNamespace" select="''"/>

  <xsl:template name="ezattribute">
    <xsl:if test="./docbook:ezattribute">
      <xsl:for-each select="./docbook:ezattribute/docbook:ezvalue">
        <xsl:attribute name="{concat('data-ezattribute-', @key)}">
          <xsl:value-of select="text()"/>
        </xsl:attribute>
      </xsl:for-each>
    </xsl:if>
  </xsl:template>

  <xsl:template match="docbook:eztemplateinline[@name='content-variable']">
    <xsl:element name="span" namespace="{$outputNamespace}">
      <xsl:call-template name="ezattribute"/>
      <xsl:value-of select="'content-variable'"/>
    </xsl:element>
  </xsl:template>

</xsl:stylesheet>
