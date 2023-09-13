<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet
        xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
        xmlns:ezxhtml5="http://ez.no/namespaces/ezpublish5/xhtml5/edit"
        xmlns:ezxhtml="http://ez.no/xmlns/ezpublish/docbook/xhtml"
        xmlns:xlink="http://www.w3.org/1999/xlink"
        xmlns="http://docbook.org/ns/docbook"
        exclude-result-prefixes="ezxhtml5"
        version="1.0">
  <xsl:output indent="yes" encoding="UTF-8"/>

  <xsl:template name="ezattribute">
    <xsl:if test="@*[starts-with(name(), 'data-ezattribute-')]">
      <xsl:element name="ezattribute" namespace="http://docbook.org/ns/docbook">
        <xsl:for-each select="@*[starts-with(name(), 'data-ezattribute-')]">
          <xsl:element name="ezvalue" namespace="http://docbook.org/ns/docbook">
            <xsl:attribute name="key">
              <xsl:value-of select="substring-after(name(), 'data-ezattribute-')"/>
            </xsl:attribute>
            <xsl:value-of select="."/>
          </xsl:element>
        </xsl:for-each>
      </xsl:element>
    </xsl:if>
  </xsl:template>

  <xsl:template match="ezxhtml5:span[@data-ezattribute-widget='content-variable']">
    <xsl:element name="eztemplateinline" namespace="http://docbook.org/ns/docbook">
      <xsl:attribute name="name">
        <xsl:value-of select="'content-variable'"/>
      </xsl:attribute>
      <xsl:call-template name="ezattribute"/>
      <xsl:element name="ezcontent" namespace="http://docbook.org/ns/docbook">
        <xsl:value-of select="concat('#', ./@data-ezattribute-identifier, '#')" />
      </xsl:element>
    </xsl:element>
  </xsl:template>

</xsl:stylesheet>