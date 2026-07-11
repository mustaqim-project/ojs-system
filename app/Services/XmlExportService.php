<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Issue;

/**
 * XmlExportService
 *
 * Menghasilkan file XML yang kompatibel dengan OJS 3.4 Native XML Import/Export.
 * Memungkinkan migrasi artikel & issue ke sistem OJS standar eksternal.
 */
class XmlExportService
{
    /**
     * Export satu artikel menjadi PKP Native XML.
     */
    public function exportArticle(Article $article): string
    {
        $title     = htmlspecialchars($article->title, ENT_XML1);
        $abstract  = htmlspecialchars($article->abstract, ENT_XML1);
        $locale    = $article->language === 'id' ? 'id_ID' : 'en_US';
        $author    = $article->author;
        
        $authorName = htmlspecialchars($author->name ?? 'Author', ENT_XML1);
        $authorEmail = htmlspecialchars($author->email ?? 'author@site.com', ENT_XML1);
        $authorAffil = htmlspecialchars($author->affiliation ?? '', ENT_XML1);
        $authorOrcid = htmlspecialchars($author->orcid ?? '', ENT_XML1);

        $publishedDate = $article->published_at ? $article->published_at->format('Y-m-d') : date('Y-m-d');
        $doiAttr       = $article->doi ? '<id type="doi">' . htmlspecialchars($article->doi, ENT_XML1) . '</id>' : '';

        // Jurnal Info
        $journalName = htmlspecialchars($article->journal->title ?? 'OJS Journal', ENT_XML1);

        // Native XML output
        return <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<article xmlns="http://pkp.sfu.ca"
         xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:schemaLocation="http://pkp.sfu.ca native.xsd"
         locale="{$locale}"
         stage="production"
         date_published="{$publishedDate}">
         
  {$doiAttr}
  
  <title locale="{$locale}">{$title}</title>
  <abstract locale="{$locale}">{$abstract}</abstract>
  
  <authors xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
           xsi:schemaLocation="http://pkp.sfu.ca native.xsd">
    <author user_group_ref="Author" include_in_browse="true">
      <givenname locale="{$locale}">{$authorName}</givenname>
      <email>{$authorEmail}</email>
      @if($authorAffil)
      <affiliation locale="{$locale}">{$authorAffil}</affiliation>
      @endif
      @if($authorOrcid)
      <orcid>{$authorOrcid}</orcid>
      @endif
    </author>
  </authors>
  
  <publication_format>
    <name locale="{$locale}">PDF Format</name>
    <file>
      <name>{$article->manuscript_file}</name>
      <file_stage>proof</file_stage>
    </file>
  </publication_format>
  
  <subject locale="{$locale}">{$article->keywords}</subject>
</article>
XML;
    }

    /**
     * Export seluruh Issue beserta seluruh artikel di dalamnya.
     */
    public function exportIssue(Issue $issue): string
    {
        $title       = htmlspecialchars($issue->title, ENT_XML1);
        $description = htmlspecialchars($issue->description ?? '', ENT_XML1);
        $year        = $issue->year;
        $volume      = $issue->volume;
        $number      = $issue->number;

        $xml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<issue xmlns="http://pkp.sfu.ca"
       xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
       xsi:schemaLocation="http://pkp.sfu.ca native.xsd"
       published="true">
       
  <title locale="id_ID">{$title}</title>
  <description locale="id_ID">{$description}</description>
  <volume>{$volume}</volume>
  <number>{$number}</number>
  <year>{$year}</year>
  
  <sections>
    <section>
      <title locale="id_ID">Articles</title>
      <abbrev locale="id_ID">ART</abbrev>
    </section>
  </sections>
  
  <articles>

XML;

        // Load all published articles in this issue
        $articles = Article::where('issue_id', $issue->id)->published()->get();
        
        foreach ($articles as $article) {
            // Hilangkan XML tag declare dari exportArticle agar bisa nested
            $articleXml = $this->exportArticle($article);
            $cleanArticleXml = preg_replace('/<\?xml[^>]*\?>/', '', $articleXml);
            $xml .= trim($cleanArticleXml) . "\n";
        }

        $xml .= <<<XML
  </articles>
</issue>
XML;

        return $xml;
    }
}
