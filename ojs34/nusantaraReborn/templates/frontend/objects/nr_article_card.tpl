{**
 * NusantaraReborn theme for OJS 3.4 — article card used on the homepage (latest articles) and on the article page (related articles).
 * Expects $nrItem from NusantaraRebornThemePlugin::articleCard() (OJS 3.5); $nrCardFull adds flags, dates, PDF and DOI.
 * Developed by Ikhwan Arief (ikhwan[at]unand.ac.id).
 * Copyright (c) 2026 Ikhwan Arief. Copyright held by the application developer.
 *}
{assign var=nrCardArticle value=$nrItem.article}
{assign var=nrCardPublication value=$nrItem.publication}
{if $nrItem.issue}{assign var=nrCardIssue value=$nrItem.issue}{/if}
{capture assign=nrCardUrl}{url page="article" op="view" path=$nrCardArticle->getBestId()}{/capture}
<li class="nr_card">
    <a class="nr_card_media" href="{$nrCardUrl}" tabindex="-1" aria-hidden="true">
        {if $nrItem.coverUrl}
            <img src="{$nrItem.coverUrl|escape}" alt="" loading="lazy" decoding="async">
        {else}
            <span class="nr_card_placeholder"><span class="fa fa-file-text-o"></span></span>
        {/if}
        {if $nrItem.sectionTitle}<span class="nr_card_badge">{$nrItem.sectionTitle|escape}</span>{/if}
    </a>
    <div class="nr_card_body">
        <h3 class="nr_card_title"><a id="nr-card-{$nrCardArticle->getId()}" href="{$nrCardUrl}">{$nrCardPublication->getLocalizedTitle(null, 'html')|strip_unsafe_html}</a></h3>
        <div class="nr_card_authors">
            {foreach from=$nrItem.authors item=nrAuthor name=nrCardAuthors}
                <span class="nr_card_author">{$nrAuthor.name|escape}{if $nrCardFull}{if $nrAuthor.affText}<sup>{$nrAuthor.affText|escape}</sup>{/if}{if $nrAuthor.flag} <span class="nr_flag" title="{$nrAuthor.country|escape}" role="img" aria-label="{$nrAuthor.country|escape}">{$nrAuthor.flag}</span>{/if}{/if}</span>{if !$smarty.foreach.nrCardAuthors.last}, {/if}
            {/foreach}
        </div>
        <div class="nr_card_meta">
            {if $nrCardIssue}<span>{$nrCardIssue->getIssueIdentification()|strip_tags}</span>{/if}
            {if $nrCardPublication->getData('pages')}<span>{translate key="plugins.themes.nusantaraReborn.article.pagesShort" pages=$nrCardPublication->getData('pages')}</span>{/if}
            {if $nrCardFull && $nrCardPublication->getData('datePublished')}<span>{translate key="submissions.published"}: {$nrCardPublication->getData('datePublished')|date_format:$dateFormatLong}</span>{/if}
        </div>
        {if $nrCardFull && ($nrItem.pdf || $nrItem.doi)}
            <div class="nr_card_actions">
                {if $nrItem.pdf}<a class="nr_btn nr_btn_primary nr_btn_small" href="{url page="article" op="view" path=$nrCardArticle->getBestId()|to_array:$nrItem.pdf->getBestGalleyId()}" aria-describedby="nr-card-{$nrCardArticle->getId()}"><span class="fa fa-file-pdf-o" aria-hidden="true"></span>PDF<span class="fa fa-angle-right" aria-hidden="true"></span></a>{/if}
                {if $nrItem.doi}<a class="nr_card_doi" href="https://doi.org/{$nrItem.doi|escape}" rel="noopener"><span class="nr_doi_icon" aria-hidden="true">doi</span>{$nrItem.doi|escape}</a>{/if}
            </div>
        {/if}
    </div>
</li>
