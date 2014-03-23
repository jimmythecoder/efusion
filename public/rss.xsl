<?xml version="1.0" encoding="UTF-8" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:template match="/">
    <html>
      <head>
        <title><xsl:value-of select="/rss/channel/title" /></title>
		<link rel="stylesheet" type="text/css" href="/stylesheets/rss.css" />
      	</head>
      <body>
 		<div id="container">
			<div id="wrapper">
				<div id="content">
		            <h1>Latest Products</h1>
		            <p id="rss-description">This page is an RSS syndication feed for <b><xsl:value-of select="/rss/channel/title" /></b>.  You can subscribe
		        	to this feed using an aggregator program such as the <a href="http://firefox.com">Firefox Web Browser</a>.</p>
		            <ol id="rss-items">
		            <xsl:for-each select="/rss/channel/item">
              		<li>
		                <h2><a href="{link}"><xsl:value-of select="title" /></a></h2>
		                <p><xsl:value-of select="description" disable-output-escaping="yes" /></p>
		                <span>Listed on: <xsl:value-of select="pubDate" /></span>
		          	</li>            
            		</xsl:for-each>
            		</ol>
		        </div>
			</div>
       	</div>
      </body>
    </html>
  </xsl:template>
</xsl:stylesheet>