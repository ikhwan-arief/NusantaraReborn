<?php
/**
 * @file plugins/themes/nusantaraReborn/NusantaraRebornThemePlugin.inc.php
 *
 * Developed by Ikhwan Arief (ikhwan[at]unand.ac.id).
 * Copyright (c) 2026 Ikhwan Arief. Copyright held by the application developer.
 * Code license: GPL-3.0-or-later. See LICENSE and LICENSE-GPL-3.0.txt.
 *
 * NusantaraReborn: a general-purpose scholarly journal theme with an Indonesian visual character,
 * for OJS 3.3. It is a child of the OJS Default Theme, so navigation, forms, accessibility helpers,
 * metadata plugins and template hooks stay with OJS. Journal identity (name, logo, favicon,
 * publisher, policies) always comes from the journal's own OJS configuration.
 */

import('lib.pkp.classes.plugins.ThemePlugin');

class NusantaraRebornThemePlugin extends ThemePlugin
{
    /** Preset used when no or an unknown value is stored. */
    const DEFAULT_PRESET = 'zamrudKhatulistiwa';

    /** System font stacks; used alone in "system" mode and as fallbacks in "google" mode. */
    const SYSTEM_SERIF = 'ui-serif, Georgia, Cambria, "Times New Roman", "Noto Serif", serif';
    const SYSTEM_SANS = 'system-ui, -apple-system, "Segoe UI", Roboto, "Noto Sans", "Helvetica Neue", Arial, sans-serif';

    /**
     * Presets. Colour tokens live in styles/presets.css (generated from scripts/presets.json);
     * this list only holds what PHP needs. scripts/validate.sh checks both stay in sync.
     */
    protected $presets = [
        'zamrudKhatulistiwa' => ['metaColour' => '#0E4030', 'serif' => 'Source Serif 4', 'sans' => 'Source Sans 3', 'hero' => 'zamrud-khatulistiwa'],
        'bahariNusantara' => ['metaColour' => '#103553', 'serif' => 'Noto Serif', 'sans' => 'Noto Sans', 'hero' => 'bahari-nusantara'],
        'batikSogan' => ['metaColour' => '#4E2F1E', 'serif' => 'Source Serif 4', 'sans' => 'Source Sans 3', 'hero' => 'batik-sogan'],
        'merahPutih' => ['metaColour' => '#7A1C26', 'serif' => 'Noto Serif', 'sans' => 'Noto Sans', 'hero' => 'merah-putih'],
        'tenunSenja' => ['metaColour' => '#272C55', 'serif' => 'Source Serif 4', 'sans' => 'Source Sans 3', 'hero' => 'tenun-senja'],
    ];

    /** Google Fonts families per font name, weights limited to what the stylesheet uses. */
    protected $googleFamilies = [
        'Source Serif 4' => 'Source+Serif+4:opsz,wght@8..60,500;8..60,600;8..60,700',
        'Source Sans 3' => 'Source+Sans+3:wght@400;500;600;700',
        'Noto Serif' => 'Noto+Serif:wght@500;600;700',
        'Noto Sans' => 'Noto+Sans:wght@400;500;600;700',
    ];

    /**
     * Initialize theme options, styles and scripts.
     */
    public function init()
    {
        $this->setParent('defaultthemeplugin');

        // Parent options replaced by the NusantaraReborn design system.
        $this->removeOption('baseColour');
        $this->removeOption('typography');
        $this->removeOption('useHomepageImageAsHeader');
        $this->removeOption('showDescriptionInJournalIndex');

        // The parent init() already ran with values saved while the Default Theme was active.
        // Drop the font and header-image styles those hidden options would still inject, so the
        // "system" font mode never loads an external font stylesheet.
        $this->removeStyle('font');
        $this->removeStyle('homepageImage');

        $presetOptions = [];
        foreach (array_keys($this->presets) as $id) {
            $presetOptions[] = ['value' => $id, 'label' => __('plugins.themes.nusantaraReborn.option.colourScheme.' . $id)];
        }
        $this->addOption('colourScheme', 'FieldOptions', [
            'type' => 'radio',
            'label' => __('plugins.themes.nusantaraReborn.option.colourScheme.label'),
            'description' => __('plugins.themes.nusantaraReborn.option.colourScheme.description'),
            'options' => $presetOptions,
            'default' => self::DEFAULT_PRESET,
        ]);

        $this->addOption('nusantaraOrnaments', 'FieldOptions', [
            'type' => 'radio',
            'label' => __('plugins.themes.nusantaraReborn.option.nusantaraOrnaments.label'),
            'description' => __('plugins.themes.nusantaraReborn.option.nusantaraOrnaments.description'),
            'options' => [
                ['value' => 'subtle', 'label' => __('plugins.themes.nusantaraReborn.option.nusantaraOrnaments.subtle')],
                ['value' => 'off', 'label' => __('plugins.themes.nusantaraReborn.option.nusantaraOrnaments.off')],
            ],
            'default' => 'subtle',
        ]);

        $this->addOption('fontSource', 'FieldOptions', [
            'type' => 'radio',
            'label' => __('plugins.themes.nusantaraReborn.option.fontSource.label'),
            'description' => __('plugins.themes.nusantaraReborn.option.fontSource.description'),
            'options' => [
                ['value' => 'system', 'label' => __('plugins.themes.nusantaraReborn.option.fontSource.system')],
                ['value' => 'google', 'label' => __('plugins.themes.nusantaraReborn.option.fontSource.google')],
            ],
            'default' => 'system',
        ]);

        foreach (['journalTagline', 'heroDescription', 'heroCaption', 'publisherLine'] as $name) {
            $this->addOption($name, 'FieldText', [
                'label' => __('plugins.themes.nusantaraReborn.option.' . $name . '.label'),
                'description' => __('plugins.themes.nusantaraReborn.option.' . $name . '.description'),
                'isMultilingual' => true,
            ]);
        }

        $this->addOption('publisherUrl', 'FieldText', [
            'label' => __('plugins.themes.nusantaraReborn.option.publisherUrl.label'),
            'description' => __('plugins.themes.nusantaraReborn.option.publisherUrl.description'),
        ]);

        $this->addOption('featureHighlights', 'FieldText', [
            'label' => __('plugins.themes.nusantaraReborn.option.featureHighlights.label'),
            'description' => __('plugins.themes.nusantaraReborn.option.featureHighlights.description'),
            'isMultilingual' => true,
        ]);

        foreach (['publicationFrequencyLabel', 'peerReviewLabel', 'apcLabel', 'languageLabel', 'indexingLabel', 'archivingLabel', 'subjectAreaLabel'] as $name) {
            $this->addOption($name, 'FieldText', [
                'label' => __('plugins.themes.nusantaraReborn.option.' . $name . '.label'),
                'description' => __('plugins.themes.nusantaraReborn.option.' . $name . '.description'),
                'isMultilingual' => true,
            ]);
        }

        $this->addOption('indexedIn', 'FieldTextarea', [
            'label' => __('plugins.themes.nusantaraReborn.option.indexedIn.label'),
            'description' => __('plugins.themes.nusantaraReborn.option.indexedIn.description'),
        ]);

        $this->addOption('editorInChiefName', 'FieldText', [
            'label' => __('plugins.themes.nusantaraReborn.option.editorInChiefName.label'),
            'description' => __('plugins.themes.nusantaraReborn.option.editorInChiefName.description'),
        ]);
        $this->addOption('editorInChiefRole', 'FieldText', [
            'label' => __('plugins.themes.nusantaraReborn.option.editorInChiefRole.label'),
            'description' => __('plugins.themes.nusantaraReborn.option.editorInChiefRole.description'),
            'isMultilingual' => true,
        ]);
        $this->addOption('editorInChiefAffiliation', 'FieldText', [
            'label' => __('plugins.themes.nusantaraReborn.option.editorInChiefAffiliation.label'),
            'isMultilingual' => true,
        ]);
        $this->addOption('editorInChiefPhoto', 'FieldText', [
            'label' => __('plugins.themes.nusantaraReborn.option.editorInChiefPhoto.label'),
            'description' => __('plugins.themes.nusantaraReborn.option.editorInChiefPhoto.description'),
        ]);
        $this->addOption('editorInChiefBio', 'FieldTextarea', [
            'label' => __('plugins.themes.nusantaraReborn.option.editorInChiefBio.label'),
            'isMultilingual' => true,
        ]);

        $this->addOption('showCorrespondingEmail', 'FieldOptions', [
            'type' => 'radio',
            'label' => __('plugins.themes.nusantaraReborn.option.showCorrespondingEmail.label'),
            'description' => __('plugins.themes.nusantaraReborn.option.showCorrespondingEmail.description'),
            'options' => [
                ['value' => 'hide', 'label' => __('plugins.themes.nusantaraReborn.option.hide')],
                ['value' => 'show', 'label' => __('plugins.themes.nusantaraReborn.option.show')],
            ],
            'default' => 'hide',
        ]);

        foreach (['socialWebsite', 'socialInstagram', 'socialYoutube', 'socialLinkedin', 'socialX', 'termsUrl'] as $name) {
            $this->addOption($name, 'FieldText', [
                'label' => __('plugins.themes.nusantaraReborn.option.' . $name . '.label'),
            ]);
        }

        // Styles: base design system, preset tokens, then the selected font mode.
        $this->addStyle('nusantaraReborn', 'styles/nusantara.css');
        $this->addStyle('nusantaraRebornPresets', 'styles/presets.css');
        $preset = $this->presetId();
        $this->addStyle('nusantaraRebornFontStack', $this->fontStackCss($preset), ['inline' => true]);
        if ($this->fontSource() === 'google') {
            $this->addStyle('nusantaraRebornFonts', $this->googleFontsUrl($preset), ['baseUrl' => '']);
        }

        // Progressive enhancement only; the parent Default Theme script still drives navigation.
        $this->addScript('nusantaraReborn', 'js/nusantara.js');

        HookRegistry::register('TemplateManager::display', [$this, 'assignTemplateVariables']);
        HookRegistry::register('TemplateManager::display', [$this, 'assignPageData']);
    }

    /**
     * @copydoc Plugin::getDisplayName()
     */
    public function getDisplayName()
    {
        return __('plugins.themes.nusantaraReborn.name');
    }

    /**
     * @copydoc Plugin::getDescription()
     */
    public function getDescription()
    {
        return __('plugins.themes.nusantaraReborn.description');
    }

    /**
     * Shared variables for every frontend page, plus head metadata.
     */
    public function assignTemplateVariables($hookName, $args)
    {
        // PKPThemeForm initialises every installed theme. Only the active one may change the page.
        if (!$this->isActive()) {
            return false;
        }

        $templateMgr = $args[0];
        $request = Application::get()->getRequest();
        $context = $request->getContext();
        $locale = AppLocale::getLocale();
        $themeUrl = rtrim($request->getBaseUrl(), '/') . '/' . $this->getPluginPath();
        $presetId = $this->presetId();
        $preset = $this->presets[$presetId];

        $hero1600 = $themeUrl . '/assets/heroes/' . $preset['hero'] . '-1600.webp';
        $hero960 = $themeUrl . '/assets/heroes/' . $preset['hero'] . '-960.webp';
        $heroUrl = $hero1600;
        $heroSrcset = $hero960 . ' 960w, ' . $hero1600 . ' 1600w';
        $usingCustomHero = false;

        // The journal's Homepage Image replaces the abstract preset background.
        $homepageImage = $context ? $context->getLocalizedData('homepageImage') : null;
        $publicFilesDir = $templateMgr->getTemplateVars('publicFilesDir');
        if (is_array($homepageImage) && !empty($homepageImage['uploadName']) && $publicFilesDir) {
            $heroUrl = rtrim($publicFilesDir, '/') . '/' . rawurlencode($homepageImage['uploadName']);
            $heroSrcset = '';
            $usingCustomHero = true;
        }

        $vars = [
            'nrColourScheme' => $presetId,
            'nrOrnaments' => $this->getOption('nusantaraOrnaments') === 'off' ? 'off' : 'subtle',
            'nrFontSource' => $this->fontSource(),
            'nrMetaColour' => $preset['metaColour'],
            'nrThemeUrl' => $themeUrl,
            'nrDefaultCoverUrl' => $themeUrl . '/assets/covers/' . $presetId . '.svg',
            'nrHeroImageUrl' => $heroUrl,
            'nrHeroImageSrcset' => $heroSrcset,
            'nrUsingCustomHero' => $usingCustomHero,
            'nrHeroCaption' => $this->localizedOption('heroCaption', $locale),
            'nrYear' => date('Y'),
            'nrOjsVersion' => $this->ojsVersion(),
            'nrIndexedIn' => $this->indexedInItems(),
            'nrTermsUrl' => $this->safeUrl($this->getOption('termsUrl')),
            'nrSocial' => array_filter([
                'website' => $this->safeUrl($this->getOption('socialWebsite')),
                'instagram' => $this->safeUrl($this->getOption('socialInstagram')),
                'youtube' => $this->safeUrl($this->getOption('socialYoutube')),
                'linkedin' => $this->safeUrl($this->getOption('socialLinkedin')),
                'x' => $this->safeUrl($this->getOption('socialX')),
            ]),
        ];

        if ($context) {
            $issnParts = [];
            if ($printIssn = trim((string) $context->getData('printIssn'))) {
                $issnParts[] = 'p-ISSN ' . $printIssn;
            }
            if ($onlineIssn = trim((string) $context->getData('onlineIssn'))) {
                $issnParts[] = 'e-ISSN ' . $onlineIssn;
            }
            $heroDescription = $this->localizedOption('heroDescription', $locale);
            if ($heroDescription === '') {
                $heroDescription = $this->cleanText($context->getLocalizedData('searchDescription'), 220);
            }
            $publisherLine = $this->localizedOption('publisherLine', $locale);
            if ($publisherLine === '') {
                $publisherLine = trim((string) $context->getData('publisherInstitution'));
            }
            $vars += [
                'nrTagline' => $this->localizedOption('journalTagline', $locale),
                'nrIssnLine' => implode('  |  ', $issnParts),
                'nrHeroDescription' => $heroDescription,
                'nrAboutExcerpt' => $this->cleanText($context->getLocalizedData('description') ?: $context->getLocalizedData('summary'), 560),
                'nrFeatureHighlights' => $this->featureHighlights($locale),
                'nrPublisherLine' => $publisherLine,
                'nrPublisherUrl' => $this->publisherUrl($context),
                'nrPublicationFrequencyLabel' => $this->localizedOption('publicationFrequencyLabel', $locale),
                'nrPeerReviewLabel' => $this->localizedOption('peerReviewLabel', $locale),
                'nrApcLabel' => $this->apcLabel($context, $locale),
                'nrLanguageLabel' => $this->languageLabel($context, $locale),
                'nrIndexingLabel' => $this->localizedOption('indexingLabel', $locale),
                'nrArchivingLabel' => $this->localizedOption('archivingLabel', $locale),
                'nrSubjectAreaLabel' => $this->localizedOption('subjectAreaLabel', $locale),
                'nrEditorInChiefName' => trim((string) $this->getOption('editorInChiefName')),
                'nrEditorInChiefRole' => $this->localizedOption('editorInChiefRole', $locale) ?: __('plugins.themes.nusantaraReborn.home.editorInChief'),
                'nrEditorInChiefAffiliation' => $this->localizedOption('editorInChiefAffiliation', $locale),
                'nrEditorInChiefPhoto' => $this->safeUrl($this->getOption('editorInChiefPhoto')),
                'nrEditorInChiefBio' => $this->localizedOption('editorInChiefBio', $locale),
            ];
        }

        $templateMgr->assign($vars);

        $this->addHeadHints($templateMgr, $request, $heroUrl, $heroSrcset);
        $this->addSeoMetadata($templateMgr, $request, $heroUrl, $publicFilesDir);

        return false;
    }

    /**
     * Page-specific data for the homepage, article, archive and editorial team templates.
     */
    public function assignPageData($hookName, $args)
    {
        if (!$this->isActive()) {
            return false;
        }
        $templateMgr = $args[0];
        $template = isset($args[1]) ? (string) $args[1] : '';
        $context = Application::get()->getRequest()->getContext();
        if (!$context) {
            return false;
        }

        switch ($template) {
            case 'frontend/pages/indexJournal.tpl':
                $items = [];
                foreach ((array) $templateMgr->getTemplateVars('publishedSubmissions') as $section) {
                    foreach ((array) ($section['articles'] ?? []) as $article) {
                        if (count($items) >= 3) {
                            break 2;
                        }
                        $items[] = $this->articleCard($article, $section['title'] ?? '', $templateMgr->getTemplateVars('issue'));
                    }
                }
                $templateMgr->assign('nrHomeArticles', $items);
                break;

            case 'frontend/pages/article.tpl':
                $article = $templateMgr->getTemplateVars('article');
                $publication = $templateMgr->getTemplateVars('publication');
                if ($article && $publication) {
                    $templateMgr->assign($this->buildArticleData(
                        $context,
                        $article,
                        $publication,
                        (array) $templateMgr->getTemplateVars('primaryGalleys'),
                        $templateMgr->getTemplateVars('issue')
                    ));
                }
                break;

            case 'frontend/pages/issueArchive.tpl':
                $issues = $templateMgr->getTemplateVars('issues');
                if (is_array($issues) && $issues) {
                    $templateMgr->assign($this->buildArchiveData($issues, (int) $context->getId()));
                }
                break;

            case 'frontend/pages/editorialTeam.tpl':
                $html = trim((string) $context->getLocalizedData('editorialTeam'));
                $intro = '';
                if (preg_match('/^\s*(<p\b[^>]*>.*?<\/p>)(.*)$/is', $html, $m)) {
                    $intro = $m[1];
                    $html = $m[2];
                }
                $templateMgr->assign([
                    'nrEditorialIntro' => $intro,
                    'nrEditorialBody' => $html,
                    'nrCountryMapJson' => json_encode($this->countryNameMap(), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE),
                ]);
                break;
        }
        return false;
    }

    /**
     * Data for one article card: authors with affiliation numbers and flags, PDF galley, DOI, image.
     */
    protected function articleCard($article, $sectionTitle, $issue)
    {
        $publication = $article->getCurrentPublication();
        list($authors) = $this->authorsWithAffiliations($publication);
        $coverUrl = '';
        if ($publication->getLocalizedData('coverImage')) {
            $coverUrl = $publication->getLocalizedCoverImageUrl($article->getData('contextId'));
        } elseif ($issue && $issue->getLocalizedCoverImageUrl()) {
            $coverUrl = $issue->getLocalizedCoverImageUrl();
        }
        return [
            'article' => $article,
            'publication' => $publication,
            'sectionTitle' => $sectionTitle,
            'authors' => $authors,
            'pdf' => $this->findPdfGalley($publication->getData('galleys')),
            'doi' => (string) $publication->getStoredPubId('doi'),
            'coverUrl' => $coverUrl,
        ];
    }

    /**
     * Authors with numbered affiliations (shared affiliations share a number) and country flags.
     */
    protected function authorsWithAffiliations($publication)
    {
        $affiliations = [];
        $authors = [];
        $primaryId = (int) $publication->getData('primaryContactId');
        foreach ((array) $publication->getData('authors') as $author) {
            $affiliation = trim((string) $author->getLocalizedData('affiliation'));
            $code = strtoupper(trim((string) $author->getCountry()));
            $valid = (bool) preg_match('/^[A-Z]{2}$/', $code);
            $country = $valid ? ($author->getCountryLocalized() ?: $code) : '';
            $flag = $valid ? $this->countryCodeToFlag($code) : '';
            $index = 0;
            if ($affiliation !== '') {
                if (!isset($affiliations[$affiliation])) {
                    $affiliations[$affiliation] = [
                        'index' => count($affiliations) + 1,
                        'name' => $affiliation,
                        'flag' => $flag,
                        'country' => $country,
                    ];
                }
                $index = $affiliations[$affiliation]['index'];
            }
            $authors[] = [
                'name' => $author->getFullName(),
                'affIndex' => $index,
                'orcid' => (string) $author->getData('orcid'),
                'email' => (string) $author->getEmail(),
                'flag' => $flag,
                'country' => $country,
                'affiliation' => $affiliation,
                'isPrimary' => $primaryId && (int) $author->getId() === $primaryId,
            ];
        }
        if (count($affiliations) < 2) {
            foreach ($authors as $i => $author) {
                $authors[$i]['affIndex'] = 0;
            }
        }
        return [$authors, array_values($affiliations)];
    }

    /**
     * Article landing page data.
     */
    protected function buildArticleData($context, $article, $publication, $primaryGalleys, $issue)
    {
        list($authors, $affiliations) = $this->authorsWithAffiliations($publication);
        $isPublished = (int) $publication->getData('status') === STATUS_PUBLISHED;

        $corresponding = null;
        foreach ($authors as $author) {
            if ($author['isPrimary']) {
                $corresponding = $author;
            }
        }
        if (!$corresponding && $authors) {
            $corresponding = $authors[0];
        }
        if ($corresponding && $this->getOption('showCorrespondingEmail') !== 'show') {
            $corresponding['email'] = '';
        }

        $pdf = $this->findPdfGalley($primaryGalleys);
        $galley = $pdf ?: ($primaryGalleys ? reset($primaryGalleys) : null);
        $downloads = 0;
        foreach ($primaryGalleys as $primaryGalley) {
            $downloads += (int) $primaryGalley->getViews();
        }

        return [
            'nrAuthors' => $authors,
            'nrAffiliations' => $affiliations,
            'nrCorresponding' => $corresponding,
            // Editorial milestone dates are shown for published versions only.
            'nrDates' => $isPublished ? $this->editorialDates($article, $publication) : [],
            'nrGalley' => $galley,
            'nrGalleySize' => $galley ? $this->galleyFileSize($galley) : '',
            'nrViews' => number_format((int) $article->getViews()),
            'nrDownloads' => number_format($downloads),
            'nrDoi' => (string) $publication->getStoredPubId('doi'),
            'nrLicenseName' => $this->licenseName((string) $publication->getData('licenseUrl')),
            'nrRelated' => $isPublished ? $this->relatedArticles($context, $article, $issue) : [],
        ];
    }

    /**
     * Received / revised / accepted / published dates. Only the date and type of the editor
     * decisions are read; no reviewer, comment or editor identity is exposed.
     */
    protected function editorialDates($article, $publication)
    {
        import('classes.workflow.EditorDecisionActionsManager'); // SUBMISSION_EDITOR_DECISION_* constants
        $dates = [
            'received' => $article->getData('dateSubmitted'),
            'revised' => null,
            'accepted' => null,
            'published' => $publication->getData('datePublished'),
        ];
        $revisionDecisions = [SUBMISSION_EDITOR_DECISION_PENDING_REVISIONS, SUBMISSION_EDITOR_DECISION_RESUBMIT];
        $decisions = DAORegistry::getDAO('EditDecisionDAO')->getEditorDecisions($article->getId());
        foreach ((array) $decisions as $decision) {
            $type = (int) $decision['decision'];
            if (in_array($type, $revisionDecisions, true)) {
                $dates['revised'] = max((string) $dates['revised'], (string) $decision['dateDecided']);
            } elseif ($type === SUBMISSION_EDITOR_DECISION_ACCEPT) {
                $dates['accepted'] = $decision['dateDecided'];
            }
        }
        return array_filter($dates);
    }

    /**
     * Up to three other published articles: same issue first, then the newest in the journal.
     */
    protected function relatedArticles($context, $article, $issue)
    {
        $items = [];
        $seen = [(int) $article->getId() => true];
        $queries = [];
        if ($issue && $issue->getPublished()) {
            $queries[] = ['contextId' => $context->getId(), 'issueIds' => [(int) $issue->getId()], 'status' => [STATUS_PUBLISHED], 'count' => 10, 'orderBy' => 'seq', 'orderDirection' => 'ASC'];
        }
        $queries[] = ['contextId' => $context->getId(), 'status' => [STATUS_PUBLISHED], 'count' => 10, 'orderBy' => 'datePublished', 'orderDirection' => 'DESC'];
        $sectionDao = DAORegistry::getDAO('SectionDAO');
        $issueDao = DAORegistry::getDAO('IssueDAO');
        foreach ($queries as $query) {
            foreach (Services::get('submission')->getMany($query) as $submission) {
                if (count($items) >= 3) {
                    break 2;
                }
                if (isset($seen[(int) $submission->getId()])) {
                    continue;
                }
                $seen[(int) $submission->getId()] = true;
                $publication = $submission->getCurrentPublication();
                if (!$publication || (int) $publication->getData('status') !== STATUS_PUBLISHED) {
                    continue;
                }
                $submissionIssue = $publication->getData('issueId') ? $issueDao->getById($publication->getData('issueId'), $context->getId()) : null;
                if ($submissionIssue && !$submissionIssue->getPublished()) {
                    continue;
                }
                $section = $sectionDao->getById($publication->getData('sectionId'));
                $card = $this->articleCard($submission, $section ? $section->getLocalizedTitle() : '', $submissionIssue);
                $card['issue'] = $submissionIssue;
                $items[] = $card;
            }
        }
        return $items;
    }

    /**
     * Human-readable size of a galley file, e.g. "1.2 MB" (OJS 3.3 file service, Flysystem 1).
     */
    protected function galleyFileSize($galley)
    {
        try {
            $submissionFile = $galley->getFile();
            if (!$submissionFile) {
                return '';
            }
            $fileService = Services::get('file');
            $file = $fileService->get($submissionFile->getData('fileId'));
            if (!$file) {
                return '';
            }
            $bytes = (int) $fileService->fs->getSize($file->path);
        } catch (Throwable $e) {
            return '';
        }
        if ($bytes <= 0) {
            return '';
        }
        $units = ['B', 'KB', 'MB', 'GB'];
        $power = min((int) floor(log($bytes, 1024)), count($units) - 1);
        return round($bytes / pow(1024, $power), $power > 1 ? 1 : 0) . ' ' . $units[$power];
    }

    /**
     * Readable name for Creative Commons license URLs; empty for any other license.
     */
    protected function licenseName($url)
    {
        if (!preg_match('#creativecommons\.org/licenses/([a-z-]+)/([0-9.]+)#i', $url, $m)) {
            return '';
        }
        $names = [
            'by' => 'Attribution',
            'by-sa' => 'Attribution-ShareAlike',
            'by-nd' => 'Attribution-NoDerivatives',
            'by-nc' => 'Attribution-NonCommercial',
            'by-nc-sa' => 'Attribution-NonCommercial-ShareAlike',
            'by-nc-nd' => 'Attribution-NonCommercial-NoDerivatives',
        ];
        $code = strtolower($m[1]);
        if (!isset($names[$code])) {
            return '';
        }
        return 'Creative Commons ' . $names[$code] . ' ' . $m[2] . ' International (CC ' . strtoupper($code) . ' ' . $m[2] . ')';
    }

    /**
     * Archive data: issues grouped by year with article counts and page ranges.
     */
    protected function buildArchiveData($issues, $contextId)
    {
        // Without a custom issue order OJS returns the archive unordered; show the newest first.
        if (!DAORegistry::getDAO('IssueDAO')->customIssueOrderingExists($contextId)) {
            usort($issues, function ($a, $b) {
                $byDate = strcmp((string) $b->getDatePublished(), (string) $a->getDatePublished());
                if ($byDate !== 0) {
                    return $byDate;
                }
                return [(int) $b->getVolume(), (string) $b->getNumber()] <=> [(int) $a->getVolume(), (string) $a->getNumber()];
            });
        }

        $years = [];
        $stats = [];
        $issueIds = [];
        foreach ($issues as $issue) {
            $issueId = (int) $issue->getId();
            $issueIds[] = $issueId;
            $stats[$issueId] = ['articles' => 0, 'firstPage' => null, 'lastPage' => null];
            $year = (string) ($issue->getYear() ?: ($issue->getDatePublished() ? date('Y', strtotime($issue->getDatePublished())) : ''));
            if (!isset($years[$year])) {
                $years[$year] = ['year' => $year, 'issues' => []];
            }
            $years[$year]['issues'][] = $issue;
        }

        foreach (Services::get('submission')->getMany(['contextId' => $contextId, 'issueIds' => $issueIds, 'status' => [STATUS_PUBLISHED]]) as $submission) {
            $publication = $submission->getCurrentPublication();
            $issueId = $publication ? (int) $publication->getData('issueId') : 0;
            if (!isset($stats[$issueId])) {
                continue;
            }
            $stats[$issueId]['articles']++;
            if (preg_match_all('/\d+/', (string) $publication->getData('pages'), $m) && $m[0]) {
                $first = (int) $m[0][0];
                $last = (int) end($m[0]);
                $stats[$issueId]['firstPage'] = $stats[$issueId]['firstPage'] === null ? $first : min($stats[$issueId]['firstPage'], $first);
                $stats[$issueId]['lastPage'] = $stats[$issueId]['lastPage'] === null ? $last : max($stats[$issueId]['lastPage'], $last);
            }
        }

        return ['nrArchiveYears' => array_values($years), 'nrArchiveStats' => $stats];
    }

    /**
     * Lower-case country names (English and local) mapped to ISO alpha-2 codes and flags.
     */
    protected function countryNameMap()
    {
        $map = [];
        $lower = function ($value) {
            return function_exists('mb_strtolower') ? mb_strtolower($value) : strtolower($value);
        };
        $isoCodes = new \Sokil\IsoCodes\IsoCodesFactory();
        foreach ($isoCodes->getCountries() as $country) {
            $code = $country->getAlpha2();
            $entry = ['code' => $code, 'flag' => $this->countryCodeToFlag($code)];
            $map[$lower($country->getName())] = $entry;
            $map[$lower($country->getLocalName())] = $entry;
        }
        $aliases = ['usa' => 'US', 'united states of america' => 'US', 'uk' => 'GB', 'england' => 'GB', 'south korea' => 'KR', 'korea' => 'KR', 'republic of korea' => 'KR', 'russia' => 'RU', 'vietnam' => 'VN', 'iran' => 'IR', 'taiwan' => 'TW', 'turkey' => 'TR', 'czech republic' => 'CZ', 'hong kong' => 'HK'];
        foreach ($aliases as $name => $code) {
            $map[$name] = ['code' => $code, 'flag' => $this->countryCodeToFlag($code)];
        }
        return $map;
    }

    protected function findPdfGalley($galleys)
    {
        foreach ((array) $galleys as $galley) {
            if ($galley && $galley->isPdfGalley()) {
                return $galley;
            }
        }
        return null;
    }

    /**
     * Convert an ISO 3166-1 alpha-2 code into a Unicode flag.
     */
    protected function countryCodeToFlag($code)
    {
        $code = strtoupper(trim((string) $code));
        if (!preg_match('/^[A-Z]{2}$/', $code)) {
            return '';
        }
        $flag = '';
        foreach (str_split($code) as $letter) {
            // Regional indicator symbol (4-byte UTF-8), built without mbstring.
            $cp = 127397 + ord($letter);
            $flag .= chr(0xF0 | ($cp >> 18)) . chr(0x80 | (($cp >> 12) & 0x3F)) . chr(0x80 | (($cp >> 6) & 0x3F)) . chr(0x80 | ($cp & 0x3F));
        }
        return $flag;
    }

    /**
     * Language row: theme option, otherwise the journal's submission languages.
     */
    protected function languageLabel($context, $locale)
    {
        $label = $this->localizedOption('languageLabel', $locale);
        if ($label !== '') {
            return $label;
        }
        $names = [];
        $all = AppLocale::getAllLocales();
        foreach ((array) $context->getData('supportedSubmissionLocales') as $submissionLocale) {
            if (isset($all[$submissionLocale])) {
                $names[] = preg_replace('/\s*\(.*\)$/', '', $all[$submissionLocale]);
            }
        }
        return implode(', ', array_unique($names));
    }

    /**
     * APC row: theme option, otherwise the OJS publication fee when payments are enabled.
     */
    protected function apcLabel($context, $locale)
    {
        $label = $this->localizedOption('apcLabel', $locale);
        if ($label !== '') {
            return $label;
        }
        $fee = (float) $context->getData('publicationFee');
        if ($context->getData('paymentsEnabled') && $fee > 0) {
            $currency = trim((string) $context->getData('currency'));
            return trim($currency . ' ' . number_format($fee, ($fee == floor($fee)) ? 0 : 2));
        }
        return '';
    }

    /**
     * Publisher URL: theme option only. No institutional default.
     */
    protected function publisherUrl($context)
    {
        return $this->safeUrl($this->getOption('publisherUrl'));
    }

    /**
     * Installed OJS version as major.minor.revision (e.g. 3.3.0).
     */
    protected function ojsVersion()
    {
        $version = Application::get()->getCurrentVersion();
        return $version ? $version->getMajor() . '.' . $version->getMinor() . '.' . $version->getRevision() : '';
    }

    /**
     * Up to four short highlights, separated by semicolons. Hidden when empty.
     */
    protected function featureHighlights($locale)
    {
        $icons = ['fa-globe', 'fa-leaf', 'fa-users', 'fa-bar-chart'];
        $items = [];
        foreach (preg_split('/\s*;\s*/', $this->localizedOption('featureHighlights', $locale)) as $label) {
            $label = trim($label);
            if ($label === '' || count($items) >= count($icons)) {
                continue;
            }
            $items[] = ['label' => $label, 'icon' => $icons[count($items)]];
        }
        return $items;
    }

    /**
     * "Indexed in" footer entries, one per line: "Name | https://url" (URL optional).
     */
    protected function indexedInItems()
    {
        $items = [];
        foreach (preg_split('/\r\n|\r|\n/', (string) $this->getOption('indexedIn')) as $line) {
            $parts = array_map('trim', explode('|', $line, 2));
            if ($parts[0] === '') {
                continue;
            }
            $items[] = [
                'name' => $this->cleanText($parts[0], 80),
                'url' => isset($parts[1]) ? $this->safeUrl($parts[1]) : '',
            ];
        }
        return $items;
    }

    /**
     * Font preconnect (Google mode only) and the homepage hero preload.
     */
    protected function addHeadHints($templateMgr, $request, $heroUrl, $heroSrcset)
    {
        if ($this->fontSource() === 'google') {
            $templateMgr->addHeader('nrPreconnectFonts', '<link rel="preconnect" href="https://fonts.googleapis.com">');
            $templateMgr->addHeader('nrPreconnectFontsStatic', '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>');
        }
        if ($request->getContext() && in_array((string) $request->getRequestedPage(), ['', 'index'], true)) {
            $preload = '<link rel="preload" as="image" href="' . $this->html($heroUrl) . '"';
            if ($heroSrcset) {
                $preload .= ' imagesrcset="' . $this->html($heroSrcset) . '" imagesizes="100vw"';
            }
            $templateMgr->addHeader('nrHeroPreload', $preload . ' fetchpriority="high">');
        }
    }

    /**
     * Canonical, Open Graph and JSON-LD for the journal homepage, published article landing pages
     * and issue pages. Google Scholar / Dublin Core tags stay with their plugins; nothing here
     * replaces or disables them. Empty values are omitted, never filled with defaults.
     */
    protected function addSeoMetadata($templateMgr, $request, $heroUrl, $publicFilesDir)
    {
        $context = $request->getContext();
        if (!$context) {
            return;
        }

        $page = (string) $request->getRequestedPage();
        $op = (string) $request->getRequestedOp();
        $journalName = trim((string) $context->getLocalizedName());
        $journalUrl = $request->url(null, 'index');
        $searchDescription = trim((string) $context->getLocalizedData('searchDescription'));
        $journalDescription = $searchDescription !== ''
            ? $this->cleanText($searchDescription, 300)
            : $this->cleanText($context->getLocalizedData('description') ?: $context->getLocalizedData('summary'), 300);

        $templateMgr->addHeader('nrOgSiteName', '<meta property="og:site_name" content="' . $this->html($journalName) . '">');
        $templateMgr->addHeader('nrOgLocale', '<meta property="og:locale" content="' . $this->html(AppLocale::getLocale()) . '">');

        if ($page === 'index' || $page === '') {
            $templateMgr->addHeader('nrCanonical', '<link rel="canonical" href="' . $this->html($journalUrl) . '">');
            // OJS prints the search description itself; only fill the gap when it is empty.
            if ($searchDescription === '' && $journalDescription !== '') {
                $templateMgr->addHeader('nrDescription', '<meta name="description" content="' . $this->html($journalDescription) . '">');
            }
            $this->addOpenGraph($templateMgr, [
                'type' => 'website',
                'title' => $journalName,
                'description' => $journalDescription,
                'url' => $journalUrl,
                'image' => $heroUrl,
            ]);
            $logo = $context->getLocalizedData('pageHeaderLogoImage');
            $logoUrl = (is_array($logo) && !empty($logo['uploadName']) && $publicFilesDir)
                ? rtrim($publicFilesDir, '/') . '/' . rawurlencode($logo['uploadName'])
                : '';
            $this->addJsonLd($templateMgr, 'nrJournalJsonLd', $this->buildJournalJsonLd($context, $journalUrl, $journalName, $journalDescription, $logoUrl));
            return;
        }

        if ($page === 'article' && $op === 'view') {
            // Historical versions are noindexed by OJS; galley views are not landing pages.
            $requestArgs = $request->getRequestedArgs();
            if (count($requestArgs) > 1 && $requestArgs[1] === 'version') {
                return;
            }
            $article = $templateMgr->getTemplateVars('article');
            $publication = $templateMgr->getTemplateVars('publication');
            $galley = $templateMgr->getTemplateVars('galley');
            if (!$article || !$publication || $galley || (int) $publication->getData('status') !== STATUS_PUBLISHED) {
                return;
            }

            $articleUrl = $request->url(null, 'article', 'view', [$article->getBestId()]);
            $title = trim(strip_tags((string) $publication->getLocalizedFullTitle()));
            $abstract = $this->cleanText($publication->getLocalizedData('abstract'), 320);
            $articleImage = $publication->getLocalizedData('coverImage') ? (string) $publication->getLocalizedCoverImageUrl($context->getId()) : '';

            $templateMgr->addHeader('nrCanonical', '<link rel="canonical" href="' . $this->html($articleUrl) . '">');
            if ($abstract !== '') {
                $templateMgr->addHeader('nrDescription', '<meta name="description" content="' . $this->html($abstract) . '">');
            }
            $this->addOpenGraph($templateMgr, [
                'type' => 'article',
                'title' => $title,
                'description' => $abstract,
                'url' => $articleUrl,
                'image' => $articleImage,
            ]);
            $this->addJsonLd($templateMgr, 'nrArticleJsonLd', $this->buildArticleJsonLd(
                $article,
                $publication,
                $templateMgr->getTemplateVars('issue'),
                $context,
                $articleUrl,
                $journalUrl,
                $journalName
            ));
            return;
        }

        if ($page === 'issue' && $op === 'view') {
            $issue = $templateMgr->getTemplateVars('issue');
            if ($issue && $issue->getPublished()) {
                $issueUrl = $request->url(null, 'issue', 'view', [$issue->getBestIssueId()]);
                $templateMgr->addHeader('nrCanonical', '<link rel="canonical" href="' . $this->html($issueUrl) . '">');
                $this->addOpenGraph($templateMgr, [
                    'type' => 'website',
                    'title' => trim(strip_tags($issue->getIssueIdentification())) . ' | ' . $journalName,
                    'description' => $this->cleanText($issue->getLocalizedDescription(), 300),
                    'url' => $issueUrl,
                    'image' => (string) $issue->getLocalizedCoverImageUrl(),
                ]);
            }
        }
    }

    protected function addOpenGraph($templateMgr, $data)
    {
        foreach (['type', 'title', 'description', 'url', 'image'] as $key) {
            if (!empty($data[$key])) {
                $templateMgr->addHeader('nrOg' . ucfirst($key), '<meta property="og:' . $key . '" content="' . $this->html($data[$key]) . '">');
            }
        }
        $templateMgr->addHeader('nrTwitterCard', '<meta name="twitter:card" content="' . (!empty($data['image']) ? 'summary_large_image' : 'summary') . '">');
    }

    protected function addJsonLd($templateMgr, $key, $data)
    {
        if (!$data) {
            return;
        }
        $json = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
        if ($json) {
            $templateMgr->addHeader($key, '<script type="application/ld+json">' . $json . '</script>');
        }
    }

    /**
     * Periodical + publisher + website graph. The publisher node exists only when the journal
     * names its publisher in OJS (Settings > Journal > Publisher).
     */
    protected function buildJournalJsonLd($context, $journalUrl, $journalName, $description, $logoUrl)
    {
        $periodical = [
            '@type' => 'Periodical',
            '@id' => $journalUrl . '#journal',
            'name' => $journalName,
            'url' => $journalUrl,
        ];
        if ($description !== '') {
            $periodical['description'] = $description;
        }
        if ($issns = $this->issns($context)) {
            $periodical['issn'] = count($issns) === 1 ? $issns[0] : $issns;
        }
        if ((int) $context->getData('publishingMode') === PUBLISHING_MODE_OPEN) {
            $periodical['isAccessibleForFree'] = true;
        }

        $graph = [$periodical];
        $publisherName = trim((string) $context->getData('publisherInstitution'));
        if ($publisherName !== '') {
            $publisher = ['@type' => 'Organization', '@id' => $journalUrl . '#publisher', 'name' => $publisherName];
            if ($publisherUrl = $this->safeUrl($this->getOption('publisherUrl'))) {
                $publisher['url'] = $publisherUrl;
            }
            if ($logoUrl) {
                $publisher['logo'] = ['@type' => 'ImageObject', 'url' => $logoUrl];
            }
            $graph[0]['publisher'] = ['@id' => $journalUrl . '#publisher'];
            $graph[] = $publisher;
        }
        $graph[] = ['@type' => 'WebSite', '@id' => $journalUrl . '#website', 'name' => $journalName, 'url' => $journalUrl];

        return ['@context' => 'https://schema.org', '@graph' => $graph];
    }

    protected function buildArticleJsonLd($article, $publication, $issue, $context, $articleUrl, $journalUrl, $journalName)
    {
        $data = [
            '@context' => 'https://schema.org',
            '@type' => 'ScholarlyArticle',
            '@id' => $articleUrl . '#article',
            'mainEntityOfPage' => $articleUrl,
            'url' => $articleUrl,
            'headline' => trim(strip_tags((string) $publication->getLocalizedFullTitle())),
            'isPartOf' => [
                '@type' => 'Periodical',
                '@id' => $journalUrl . '#journal',
                'name' => $journalName,
                'url' => $journalUrl,
            ],
        ];
        if ($language = substr((string) ($publication->getData('locale') ?: $article->getLocale()), 0, 2)) {
            $data['inLanguage'] = $language;
        }
        if ($issns = $this->issns($context)) {
            $data['isPartOf']['issn'] = count($issns) === 1 ? $issns[0] : $issns;
        }
        $publisherName = trim((string) $context->getData('publisherInstitution'));
        if ($publisherName !== '') {
            $data['publisher'] = ['@type' => 'Organization', 'name' => $publisherName];
            if ($publisherUrl = $this->safeUrl($this->getOption('publisherUrl'))) {
                $data['publisher']['url'] = $publisherUrl;
            }
        }

        $abstract = $this->cleanText($publication->getLocalizedData('abstract'));
        if ($abstract !== '') {
            $data['abstract'] = $abstract;
            $data['description'] = $this->cleanText($abstract, 320);
        }
        if ($datePublished = $publication->getData('datePublished')) {
            $data['datePublished'] = date('Y-m-d', strtotime($datePublished));
        }
        if ($dateModified = $publication->getData('lastModified')) {
            $data['dateModified'] = date('c', strtotime($dateModified));
        }
        if ($license = $this->safeUrl($publication->getData('licenseUrl'))) {
            $data['license'] = $license;
        }
        if ($pages = trim((string) $publication->getData('pages'))) {
            $data['pagination'] = $pages;
        }
        if ($issue && $issue->getPublished()) {
            if ($issue->getShowVolume() && $issue->getVolume()) {
                $data['volumeNumber'] = (string) $issue->getVolume();
            }
            if ($issue->getShowNumber() && $issue->getNumber()) {
                $data['issueNumber'] = (string) $issue->getNumber();
            }
        }

        $authors = [];
        foreach ((array) $publication->getData('authors') as $author) {
            $person = ['@type' => 'Person', 'name' => $author->getFullName(false)];
            if ($affiliation = trim((string) $author->getLocalizedData('affiliation'))) {
                $person['affiliation'] = ['@type' => 'Organization', 'name' => $this->cleanText($affiliation)];
            }
            if ($orcid = $this->safeUrl($author->getData('orcid'))) {
                $person['sameAs'] = $orcid;
            }
            $authors[] = $person;
        }
        if ($authors) {
            $data['author'] = $authors;
        }

        // OJS 3.3 keeps keywords in the controlled-vocabulary DAO; read the current locale.
        $keywordDao = DAORegistry::getDAO('SubmissionKeywordDAO');
        $keywords = $keywordDao ? $keywordDao->getKeywords($publication->getId(), [AppLocale::getLocale()]) : [];
        if ($keywords = $this->flattenLocalizedValues($keywords)) {
            $data['keywords'] = $keywords;
        }

        if ($publication->getLocalizedData('coverImage') && ($imageUrl = $publication->getLocalizedCoverImageUrl($context->getId()))) {
            $data['image'] = $imageUrl;
        }

        if ($doi = trim((string) $publication->getStoredPubId('doi'))) {
            $data['identifier'] = ['@type' => 'PropertyValue', 'propertyID' => 'DOI', 'value' => $doi];
            $data['sameAs'] = 'https://doi.org/' . ltrim($doi, '/');
        }

        return $data;
    }

    protected function issns($context)
    {
        return array_values(array_filter([
            trim((string) $context->getData('printIssn')),
            trim((string) $context->getData('onlineIssn')),
        ]));
    }

    protected function flattenLocalizedValues($values)
    {
        $out = [];
        foreach ((array) $values as $localeValues) {
            foreach ((array) $localeValues as $value) {
                $value = trim((string) $value);
                if ($value !== '') {
                    $out[] = $value;
                }
            }
        }
        return array_values(array_unique($out));
    }

    protected function cleanText($value, $limit = 0)
    {
        $text = trim(preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags((string) $value), ENT_QUOTES | ENT_HTML5, 'UTF-8')));
        if (!$limit || $text === '') {
            return $text;
        }
        if (function_exists('mb_strlen')) {
            return mb_strlen($text, 'UTF-8') > $limit ? rtrim(mb_substr($text, 0, $limit - 1, 'UTF-8')) . '…' : $text;
        }
        return strlen($text) > $limit ? rtrim(substr($text, 0, $limit - 3)) . '...' : $text;
    }

    protected function html($value)
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Accept only absolute http(s) URLs; anything else (javascript:, data:, relative) is dropped.
     */
    protected function safeUrl($value)
    {
        $value = trim((string) $value);
        if ($value === '' || !filter_var($value, FILTER_VALIDATE_URL)) {
            return '';
        }
        return in_array(strtolower((string) parse_url($value, PHP_URL_SCHEME)), ['http', 'https'], true) ? $value : '';
    }

    /**
     * A theme option in the current locale, then the journal primary locale, then any locale.
     */
    protected function localizedOption($name, $locale)
    {
        $value = $this->getOption($name);
        if (!is_array($value)) {
            return trim((string) $value);
        }
        $context = Application::get()->getRequest()->getContext();
        $order = [$locale];
        if ($context) {
            $order[] = $context->getPrimaryLocale();
        }
        foreach ($order as $candidateLocale) {
            if (!empty($value[$candidateLocale]) && trim((string) $value[$candidateLocale]) !== '') {
                return trim((string) $value[$candidateLocale]);
            }
        }
        foreach ($value as $candidate) {
            if (trim((string) $candidate) !== '') {
                return trim((string) $candidate);
            }
        }
        return '';
    }

    protected function presetId()
    {
        $id = (string) $this->getOption('colourScheme');
        return isset($this->presets[$id]) ? $id : self::DEFAULT_PRESET;
    }

    protected function fontSource()
    {
        return $this->getOption('fontSource') === 'google' ? 'google' : 'system';
    }

    /**
     * Font stacks for the selected mode. System mode references no web font at all.
     */
    protected function fontStackCss($presetId)
    {
        $serif = self::SYSTEM_SERIF;
        $sans = self::SYSTEM_SANS;
        if ($this->fontSource() === 'google') {
            $preset = $this->presets[$presetId];
            $serif = '"' . $preset['serif'] . '", ' . $serif;
            $sans = '"' . $preset['sans'] . '", ' . $sans;
        }
        return 'body.nr_theme{--nr-font-serif:' . $serif . ';--nr-font-ui:' . $sans . ';}';
    }

    protected function googleFontsUrl($presetId)
    {
        $preset = $this->presets[$presetId];
        return 'https://fonts.googleapis.com/css2?family=' . $this->googleFamilies[$preset['serif']]
            . '&family=' . $this->googleFamilies[$preset['sans']] . '&display=swap';
    }
}
