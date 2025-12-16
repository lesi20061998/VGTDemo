<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="2.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:sitemap="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">
<xsl:output method="html" version="1.0" encoding="UTF-8" indent="yes"/>
<xsl:template match="/">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>XML Sitemap</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<style>
body{font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,"Helvetica Neue",sans-serif;color:#333;max-width:1200px;margin:0 auto;padding:20px;background:#f5f5f5}
h1{color:#1e40af;font-size:24px;margin-bottom:10px}
#intro{background:#fff;padding:20px;border-radius:8px;margin-bottom:20px;box-shadow:0 1px 3px rgba(0,0,0,.1)}
#intro p{margin:8px 0;line-height:1.6}
#content{background:#fff;border-radius:8px;box-shadow:0 1px 3px rgba(0,0,0,.1);overflow:hidden}
table{width:100%;border-collapse:collapse}
th{background:#1e40af;color:#fff;text-align:left;padding:12px 15px;font-weight:600;font-size:14px}
td{padding:12px 15px;border-bottom:1px solid #e5e7eb;font-size:14px}
tr:hover{background:#f9fafb}
tr:last-child td{border-bottom:none}
a{color:#2563eb;text-decoration:none}
a:hover{text-decoration:underline}
.url{color:#2563eb;word-break:break-all}
.date{color:#6b7280;font-size:13px}
.priority{color:#059669;font-weight:500}
.freq{color:#7c3aed}
</style>
</head>
<body>
<div id="intro">
<h1>XML Sitemap</h1>
<p>Đây là sitemap XML được tạo tự động để giúp các công cụ tìm kiếm như Google, Bing tìm và lập chỉ mục nội dung tốt hơn.</p>
<p>Tổng số URL trong sitemap này: <strong><xsl:value-of select="count(sitemap:urlset/sitemap:url)"/></strong></p>
</div>
<div id="content">
<table>
<tr>
<th style="width:60%">URL</th>
<th>Cập nhật</th>
<th>Tần suất</th>
<th>Ưu tiên</th>
</tr>
<xsl:for-each select="sitemap:urlset/sitemap:url">
<tr>
<td><a class="url" href="{sitemap:loc}"><xsl:value-of select="sitemap:loc"/></a></td>
<td class="date"><xsl:value-of select="substring(sitemap:lastmod,0,11)"/></td>
<td class="freq"><xsl:value-of select="sitemap:changefreq"/></td>
<td class="priority"><xsl:value-of select="sitemap:priority"/></td>
</tr>
</xsl:for-each>
</table>
</div>
</body>
</html>
</xsl:template>
</xsl:stylesheet>
