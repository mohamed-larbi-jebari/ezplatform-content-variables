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
  <!-- Replace content-variable widget with its wrapped identifier -->
  <xsl:template match="docbook:eztemplateinline[docbook:ezattribute[docbook:ezvalue[@key='widget' and text()='content-variable']]]">
    <xsl:value-of select="concat('#', ./docbook:ezattribute/docbook:ezvalue[@key='identifier'], '#')" />
  </xsl:template>
</xsl:stylesheet>
