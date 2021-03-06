<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:param name="repositoryName"></xsl:param>
    <xsl:param name="responseDate"></xsl:param>
    <xsl:param name="baseUrl"></xsl:param>
    <xsl:param name="adminEmail"></xsl:param>
    <xsl:param name="verbTemplate"></xsl:param>

    <xsl:param name="lastModifed"></xsl:param>

    <xsl:param name="title"></xsl:param>
    <xsl:param name="creator"></xsl:param>
    <xsl:param name="description"></xsl:param>
    <xsl:param name="identifier"></xsl:param>
    <xsl:param name="date"></xsl:param>
    <xsl:param name="format"></xsl:param>
    <xsl:param name="language"></xsl:param>
    <xsl:param name="publisher"></xsl:param>
    <xsl:param name="type"></xsl:param>
    <xsl:param name="recordDate"></xsl:param>
    <xsl:param name="url"></xsl:param>

    <xsl:output indent="yes" omit-xml-declaration="yes"/>

    <xsl:template match="/">
        <xsl:call-template name="singleRecord"></xsl:call-template>
    </xsl:template>

    <xsl:template name="singleRecord">
        <record>
            <header>
                <identifier><xsl:value-of select="$identifier"></xsl:value-of></identifier>
                <datestamp><xsl:value-of select="$recordDate"></xsl:value-of></datestamp>
            </header>
            <metadata>
                <oai_dc:dc xmlns:oai_dc="http://www.openarchives.org/OAI/2.0/oai_dc/"
                           xmlns:dc="http://purl.org/dc/elements/1.1/"
                           xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                           xsi:schemaLocation="http://www.openarchives.org/OAI/2.0/oai_dc/ http://www.openarchives.org/OAI/2.0/oai_dc.xsd">
                    <dc:title>
                        <xsl:value-of select="$title"></xsl:value-of>
                    </dc:title>
                    <dc:creator>
                        <xsl:value-of select="$creator"></xsl:value-of>
                    </dc:creator>
                    <dc:date>
                        <xsl:value-of select="$date"></xsl:value-of>
                    </dc:date>
                    <dc:format>
                        <xsl:value-of select="$format"></xsl:value-of>
                    </dc:format>
                    <dc:language>
                        <xsl:value-of select="$language"></xsl:value-of>
                    </dc:language>
                    <dc:publisher>
                        <xsl:value-of select="$publisher"></xsl:value-of>
                    </dc:publisher>
                    <dc:type>
                        <xsl:value-of select="$type"></xsl:value-of>
                    </dc:type>
                    <dc:description>
                        <xsl:value-of select="$description"></xsl:value-of>
                    </dc:description>
                    <dc:identifier>
                        <xsl:value-of select="$url"></xsl:value-of>
                    </dc:identifier>


                </oai_dc:dc>
            </metadata>
        </record>
    </xsl:template>

</xsl:stylesheet>