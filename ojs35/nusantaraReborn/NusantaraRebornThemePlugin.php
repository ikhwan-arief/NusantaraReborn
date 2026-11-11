<?php
/**
 * @file plugins/themes/nusantaraReborn/NusantaraRebornThemePlugin.php
 *
 * Developed by Ikhwan Arief (ikhwan[at]unand.ac.id).
 * Copyright (c) 2026 Ikhwan Arief. Copyright held by the application developer.
 * Code license: GPL-3.0-or-later. See LICENSE and LICENSE-GPL-3.0.txt.
 *
 * NusantaraReborn: a general-purpose scholarly journal theme with an Indonesian visual character,
 * for OJS 3.5. It is a child of the OJS Default Theme, so navigation, forms, accessibility helpers,
 * metadata plugins, template hooks and the usage statistics chart stay with OJS. Journal identity
 * (name, logo, favicon, publisher, policies) always comes from the journal's own OJS configuration.
 */

namespace APP\plugins\themes\nusantaraReborn;

use APP\core\Application;
use APP\facades\Repo;
use APP\journal\Journal;
use PKP\config\Config;
use PKP\db\DAORegistry;
use PKP\decision\Decision;
use PKP\facades\Locale;
use PKP\plugins\Hook;
use PKP\plugins\ThemePlugin;
use PKP\submission\PKPSubmission;
use Throwable;

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

        // Parent options replaced by the NusantaraReborn design system. The parent's displayStats
        // option (usage statistics chart) is kept on purpose.
        $this->removeOption('baseColour');
        $this->removeOption('typography');
        $this->removeOption('useHomepageImageAsHeader');
        $this->removeOption('showDescriptionInJournalIndex');

        // The parent init() already ran with values saved while the Default Theme was active.
        // Drop the font and header-image styles those hidden options would still inject, so the
        // "system" font mode never loads an external font stylesheet.
        $this->removeStyle('font');
        $this->removeStyle('homepageImage');

        // Read-only guide block at the top of the theme settings (no value is stored).
        $this->addOption('nrSettingsGuide', 'FieldHTML', [
            'label' => __('plugins.themes.nusantaraReborn.option.guide.label'),
            'description' => $this->settingsGuideHtml(),
        ]);

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

        $this->addOption('showEditorialDates', 'FieldOptions', [
            'type' => 'radio',
            'label' => __('plugins.themes.nusantaraReborn.option.showEditorialDates.label'),
            'description' => __('plugins.themes.nusantaraReborn.option.showEditorialDates.description'),
            'options' => [
                ['value' => 'show', 'label' => __('plugins.themes.nusantaraReborn.option.show')],
                ['value' => 'hide', 'label' => __('plugins.themes.nusantaraReborn.option.hide')],
            ],
            'default' => 'show',
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

        Hook::add('TemplateManager::display', [$this, 'assignTemplateVariables']);
        Hook::add('TemplateManager::display', [$this, 'assignPageData']);
    }

    /**
     * Guide and Navigation warning shown at the top of the theme settings form, with the number
     * of items in the journal's Primary Navigation Menu.
     */
    protected function settingsGuideHtml()
    {
        $html = '<p>' . __('plugins.themes.nusantaraReborn.option.guide.intro') . '</p>'
            . '<div style="border-left:4px solid #B54708;background:#FFF6ED;padding:10px 14px;margin:10px 0;">'
            . '<p style="margin:0 0 6px;"><strong>⚠ ' . __('plugins.themes.nusantaraReborn.option.guide.navTitle') . '</strong></p>'
            . '<p style="margin:0;">' . __('plugins.themes.nusantaraReborn.option.guide.navBody') . '</p>';
        $count = $this->primaryMenuItemCount();
        if ($count === 0) {
            $html .= '<p style="margin:8px 0 0;color:#B42318;"><strong>' . __('plugins.themes.nusantaraReborn.option.guide.navEmpty') . '</strong></p>';
        } elseif ($count !== null) {
            $html .= '<p style="margin:8px 0 0;color:#067647;">' . __('plugins.themes.nusantaraReborn.option.guide.navStatus', ['count' => $count]) . '</p>';
        }
        return $html . '</div>';
    }

    /**
     * Items assigned to the Primary Navigation Menu of the current journal; null when unknown
     * (site level, frontend requests, or an unexpected error).
     */
    protected function primaryMenuItemCount()
    {
        try {
            $request = Application::get()->getRequest();
            $context = $request->getContext();
            if (!$context || $request->getRequestedPage() === null || in_array((string) $request->getRequestedPage(), ['', 'index', 'article', 'issue'], true)) {
                return null;
            }
            $menus = DAORegistry::getDAO('NavigationMenuDAO')->getByArea($context->getId(), 'primary')->toArray();
            if (!$menus) {
                return 0;
            }
            return count(DAORegistry::getDAO('NavigationMenuItemAssignmentDAO')->getByMenuId($menus[0]->getId())->toArray());
        } catch (Throwable $e) {
            return null;
        }
    }

    /**
     * Multilingual options saved under OJS 3.3 keep keys such as "en_US" or "id_ID" after an OJS
     * upgrade, while OJS 3.5 uses "en" and "id". Expose each old value under the new key as well,
     * so the settings form shows it and the next save stores it with the new key. Read-only:
     * nothing is written to the database here.
     */
    public function getOptionValues(?int $contextId)
    {
        $values = parent::getOptionValues($contextId);
        foreach ($this->options as $name => $field) {
            if (empty($field->isMultilingual) || !isset($values[$name]) || !is_array($values[$name])) {
                continue;
            }
            foreach ($values[$name] as $storedLocale => $text) {
                if (!is_string($storedLocale) || strpos($storedLocale, '_') === false) {
                    continue;
                }
                $language = substr($storedLocale, 0, strpos($storedLocale, '_'));
                if ($text !== null && trim((string) $text) !== '' && (!isset($values[$name][$language]) || trim((string) $values[$name][$language]) === '')) {
                    $values[$name][$language] = $text;
                }
            }
        }
        return $values;
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
        $locale = Locale::getLocale();
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
                'nrEditorInChiefPhoto' => $this->safeUrl($this->getOption('editorInChiefPhoto'), ['https']),
                'nrEditorInChiefBio' => $this->localizedOption('editorInChiefBio', $locale),
            ];
        }

        $templateMgr->assign($vars);

        $this->addHeadHints($templateMgr, $request, $heroUrl, $heroSrcset);
        $this->addSeoMetadata($templateMgr, $request, $heroUrl, $publicFilesDir);

        return false;
    }

    /**
     * Page-specific data for the homepage, article, archive and custom page templates.
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
                        // The issue TOC may include scheduled items for editors; cards show published ones only.
                        if ((int) $article->getData('status') !== PKPSubmission::STATUS_PUBLISHED) {
                            continue;
                        }
                        $items[] = $this->articleCard($article, $section['title'] ?? '', $templateMgr->getTemplateVars('issue'));
                    }
                }
                $templateMgr->assign([
                    'nrHomeArticles' => $items,
                    'nrAnnouncements' => $this->homepageAnnouncements($templateMgr, $context),
                ]);
                break;

            case 'frontend/pages/article.tpl':
                $templateMgr->assign('nrLicenseTerms', $this->richHtml((string) $context->getLocalizedData('licenseTerms')));
                $article = $templateMgr->getTemplateVars('article');
                $publication = $templateMgr->getTemplateVars('publication');
                if ($article && $publication) {
                    $templateMgr->assign($this->buildArticleData(
                        $context,
                        $article,
                        $publication,
                        $this->items($templateMgr->getTemplateVars('primaryGalleys')),
                        $templateMgr->getTemplateVars('issue')
                    ));
                }
                break;

            case 'frontend/pages/issueArchive.tpl':
                $issues = $templateMgr->getTemplateVars('issues');
                if ($issues) {
                    $templateMgr->assign($this->buildArchiveData(is_array($issues) ? $issues : iterator_to_array($issues, false), (int) $context->getId()));
                }
                break;

            case 'frontend/pages/navigationMenuItemViewContent.tpl':
                $templateMgr->assign('nrPageContent', $this->richHtml((string) $templateMgr->getTemplateVars('content')));
                break;
        }
        return false;
    }

    /**
     * Homepage announcements as plain arrays (OJS 3.5 Eloquent models). The type name is read
     * from the announcement type DAO; the model's fullTitle accessor is not used.
     */
    protected function homepageAnnouncements($templateMgr, $context)
    {
        $limit = (int) $templateMgr->getTemplateVars('numAnnouncementsHomepage');
        $announcements = $templateMgr->getTemplateVars('announcements');
        if (!$limit || !$announcements) {
            return [];
        }
        $typeDao = DAORegistry::getDAO('AnnouncementTypeDAO');
        $items = [];
        foreach ($announcements as $announcement) {
            if (count($items) >= $limit) {
                break;
            }
            $typeName = '';
            if (!empty($announcement->typeId)) {
                try {
                    $type = $typeDao->getById((int) $announcement->typeId, (int) $context->getId());
                    $typeName = $type ? trim((string) $type->getLocalizedData('name')) : '';
                } catch (Throwable $e) {
                    $typeName = '';
                }
            }
            $items[] = [
                'id' => (int) $announcement->id,
                'title' => trim((string) $announcement->getLocalizedData('title')),
                'summary' => $this->cleanText($announcement->getLocalizedData('descriptionShort'), 240),
                'date' => $announcement->datePosted ? $announcement->datePosted->format('Y-m-d') : '',
                'typeName' => $typeName,
            ];
        }
        return $items;
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
            'doi' => (string) $publication->getDoi(),
            'coverUrl' => $coverUrl,
        ];
    }

    /**
     * Authors with numbered affiliations (OJS 3.5 affiliation models, with ROR). Shared
     * affiliations share a number; flags come from the author's country.
     */
    protected function authorsWithAffiliations($publication)
    {
        $affiliations = [];
        $authors = [];
        $primaryId = (int) $publication->getData('primaryContactId');
        $userGroups = [];
        foreach ($this->items($publication->getData('authors')) as $author) {
            $code = strtoupper(trim((string) $author->getCountry()));
            $valid = (bool) preg_match('/^[A-Z]{2}$/', $code);
            $country = $valid ? ($author->getCountryLocalized() ?: $code) : '';
            $flag = $valid ? $this->countryCodeToFlag($code) : '';
            $indexes = [];
            $names = [];
            foreach ($this->items($author->getAffiliations()) as $affiliation) {
                $name = trim((string) $affiliation->getLocalizedName());
                $ror = $this->safeUrl($affiliation->getRor(), ['https']);
                if ($name === '' && $ror === '') {
                    continue;
                }
                $key = $ror !== '' ? $ror : $name;
                if (!isset($affiliations[$key])) {
                    $affiliations[$key] = [
                        'index' => count($affiliations) + 1,
                        // No local name and the ROR is not in the bundled registry: show the short ROR id.
                        'name' => $name !== '' ? $name : preg_replace('#^https://ror\.org/#', 'ROR ', $ror),
                        'ror' => $ror,
                        'flag' => $flag,
                        'country' => $country,
                    ];
                }
                $indexes[] = $affiliations[$key]['index'];
                $names[] = $affiliations[$key]['name'];
            }
            $groupName = '';
            if ($groupId = (int) $author->getData('userGroupId')) {
                if (!array_key_exists($groupId, $userGroups)) {
                    $userGroups[$groupId] = $this->authorUserGroupTitle($groupId);
                }
                $groupName = $userGroups[$groupId];
            }
            $orcid = $this->safeUrl($author->getData('orcid'), ['https', 'http']);
            $authors[] = [
                'name' => $author->getFullName(),
                'affIndexes' => $indexes,
                'affText' => '',
                'orcid' => $orcid,
                'orcidVerified' => $orcid !== '' && method_exists($author, 'hasVerifiedOrcid') ? (bool) $author->hasVerifiedOrcid() : false,
                'orcidDisplay' => $orcid !== '' && method_exists($author, 'getOrcidDisplayValue') ? (string) $author->getOrcidDisplayValue() : $orcid,
                'email' => (string) $author->getEmail(),
                'flag' => $flag,
                'country' => $country,
                'affiliation' => implode('; ', $names),
                'userGroup' => $groupName,
                'isPrimary' => $primaryId && (int) $author->getId() === $primaryId,
            ];
        }
        $numbered = count($affiliations) > 1;
        foreach ($authors as $i => $author) {
            $authors[$i]['affText'] = $numbered ? implode(',', $author['affIndexes']) : '';
        }
        return [$authors, array_values($affiliations)];
    }

    /**
     * Name of the author's user group when the journal shows it in author lists (showTitle).
     */
    protected function authorUserGroupTitle($groupId)
    {
        try {
            $group = \PKP\userGroup\UserGroup::find($groupId);
            if ($group && $group->showTitle) {
                return trim((string) $group->getLocalizedData('name'));
            }
        } catch (Throwable $e) {
        }
        return '';
    }

    /**
     * Article landing page data.
     */
    protected function buildArticleData($context, $article, $publication, $primaryGalleys, $issue)
    {
        list($authors, $affiliations) = $this->authorsWithAffiliations($publication);
        $isPublished = (int) $publication->getData('status') === PKPSubmission::STATUS_PUBLISHED;

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
        list($views, $downloads) = $isPublished ? $this->usageTotals($article, $context) : [null, null];

        $doi = trim((string) $publication->getDoi());
        $doiObject = $publication->getData('doiObject');
        $doiUrl = $doiObject ? $this->safeUrl($doiObject->getData('resolvingUrl')) : '';
        if ($doi !== '' && $doiUrl === '') {
            $doiUrl = 'https://doi.org/' . ltrim($doi, '/');
        }

        return [
            'nrAuthors' => $authors,
            'nrAffiliations' => $affiliations,
            'nrCorresponding' => $corresponding,
            'nrDates' => ($isPublished && $this->getOption('showEditorialDates') !== 'hide') ? $this->editorialDates($article, $publication) : [],
            'nrGalley' => $galley,
            'nrGalleySize' => $galley ? $this->galleyFileSize($galley) : '',
            'nrViews' => $views,
            'nrDownloads' => $downloads,
            'nrDoi' => $doi,
            'nrDoiUrl' => $doiUrl,
            'nrKeywords' => $this->keywordNames($publication->getLocalizedData('keywords')),
            'nrLicenseName' => $this->licenseName((string) $publication->getData('licenseUrl')),
            'nrRelated' => $isPublished ? $this->relatedArticles($context, $article, $issue) : [],
        ];
    }

    /**
     * Abstract views and file downloads from the OJS 3.5 publication statistics service.
     * Returns [null, null] when statistics are unavailable, so the rows are hidden.
     */
    protected function usageTotals($article, $context)
    {
        try {
            $totals = app()->get('publicationStats')->getTotalsByType((int) $article->getId(), (int) $context->getId(), null, null);
            $downloads = (int) ($totals['pdf'] ?? 0) + (int) ($totals['html'] ?? 0) + (int) ($totals['other'] ?? 0);
            return [number_format((int) ($totals['abstract'] ?? 0)), number_format($downloads)];
        } catch (Throwable $e) {
            return [null, null];
        }
    }

    /**
     * OJS 3.5 keywords are objects with a "name" (controlled vocabulary entries).
     */
    protected function keywordNames($keywords)
    {
        $out = [];
        foreach ((array) $keywords as $keyword) {
            $name = is_array($keyword) ? ($keyword['name'] ?? '') : (is_object($keyword) && isset($keyword->name) ? $keyword->name : $keyword);
            $name = trim((string) $name);
            if ($name !== '') {
                $out[] = $name;
            }
        }
        return array_values(array_unique($out));
    }

    /**
     * Received / revised / accepted / published dates. Only the type and date of editor decisions
     * are read through the OJS 3.5 decision repository; no reviewer, comment or editor identity is
     * exposed. Decision type constants are class constants (their numbers differ from OJS 3.3).
     */
    protected function editorialDates($article, $publication)
    {
        $dates = [
            'received' => $article->getData('dateSubmitted'),
            'revised' => null,
            'accepted' => null,
            'published' => $publication->getData('datePublished'),
        ];
        $revisionTypes = [Decision::PENDING_REVISIONS, Decision::RESUBMIT];
        try {
            $decisions = Repo::decision()->getCollector()
                ->filterBySubmissionIds([(int) $article->getId()])
                ->filterByDecisionTypes(array_merge($revisionTypes, [Decision::ACCEPT]))
                ->getMany();
            foreach ($decisions as $decision) {
                $type = (int) $decision->getData('decision');
                $date = (string) $decision->getData('dateDecided');
                if (in_array($type, $revisionTypes, true)) {
                    $dates['revised'] = max((string) $dates['revised'], $date);
                } elseif ($type === Decision::ACCEPT) {
                    $dates['accepted'] = $date;
                }
            }
        } catch (Throwable $e) {
            // Decisions unavailable: keep received/published only.
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
        $collectors = [];
        if ($issue && $issue->getPublished()) {
            $collectors[] = Repo::submission()->getCollector()
                ->filterByContextIds([(int) $context->getId()])
                ->filterByIssueIds([(int) $issue->getId()])
                ->filterByStatus([PKPSubmission::STATUS_PUBLISHED])
                ->orderBy(\APP\submission\Collector::ORDERBY_SEQUENCE, \APP\submission\Collector::ORDER_DIR_ASC)
                ->limit(10);
        }
        $collectors[] = Repo::submission()->getCollector()
            ->filterByContextIds([(int) $context->getId()])
            ->filterByStatus([PKPSubmission::STATUS_PUBLISHED])
            ->orderBy(\APP\submission\Collector::ORDERBY_DATE_PUBLISHED, \APP\submission\Collector::ORDER_DIR_DESC)
            ->limit(10);
        foreach ($collectors as $collector) {
            foreach ($collector->getMany() as $submission) {
                if (count($items) >= 3) {
                    break 2;
                }
                if (isset($seen[(int) $submission->getId()])) {
                    continue;
                }
                $seen[(int) $submission->getId()] = true;
                $publication = $submission->getCurrentPublication();
                if (!$publication || (int) $publication->getData('status') !== PKPSubmission::STATUS_PUBLISHED) {
                    continue;
                }
                $submissionIssue = $publication->getData('issueId') ? Repo::issue()->get((int) $publication->getData('issueId'), (int) $context->getId()) : null;
                if ($submissionIssue && !$submissionIssue->getPublished()) {
                    continue;
                }
                $section = $publication->getData('sectionId') ? Repo::section()->get((int) $publication->getData('sectionId'), (int) $context->getId()) : null;
                $card = $this->articleCard($submission, $section ? $section->getLocalizedTitle() : '', $submissionIssue);
                $card['issue'] = $submissionIssue;
                $items[] = $card;
            }
        }
        return $items;
    }

    /**
     * Human-readable size of a galley file, e.g. "1.2 MB" (OJS 3.5 file service, Flysystem 3).
     */
    protected function galleyFileSize($galley)
    {
        try {
            $submissionFile = $galley->getFile();
            if (!$submissionFile) {
                return '';
            }
            $fileService = app()->get('file');
            $file = $fileService->get($submissionFile->getData('fileId'));
            if (!$file) {
                return '';
            }
            $bytes = (int) $fileService->fs->fileSize($file->path);
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
        // Without a custom issue order the archive is unordered; show the newest issues first.
        if (!Repo::issue()->dao->customIssueOrderingExists($contextId)) {
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

        if ($issueIds) {
            $submissions = Repo::submission()->getCollector()
                ->filterByContextIds([$contextId])
                ->filterByIssueIds($issueIds)
                ->filterByStatus([PKPSubmission::STATUS_PUBLISHED])
                ->getMany();
            foreach ($submissions as $submission) {
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
        }

        return ['nrArchiveYears' => array_values($years), 'nrArchiveStats' => $stats];
    }

    /**
     * First PDF galley of an array or LazyCollection of galleys (OJS 3.5 returns collections).
     */
    protected function findPdfGalley($galleys)
    {
        if (!$galleys) {
            return null;
        }
        foreach ($galleys as $galley) {
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
        try {
            $names = Locale::getSubmissionLocaleDisplayNames((array) $context->getData('supportedSubmissionLocales'), $locale);
        } catch (Throwable $e) {
            $names = [];
        }
        return implode(', ', array_unique(array_map(function ($name) {
            return preg_replace('/\s*\(.*\)$/', '', (string) $name);
        }, array_values((array) $names))));
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
     * Installed OJS version as major.minor.revision (e.g. 3.5.0).
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
        $templateMgr->addHeader('nrOgLocale', '<meta property="og:locale" content="' . $this->html(Locale::getLocale()) . '">');

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
            if (!$article || !$publication || $galley || (int) $publication->getData('status') !== PKPSubmission::STATUS_PUBLISHED) {
                return;
            }

            $articleUrl = $request->url(null, 'article', 'view', [$article->getBestId()]);
            $title = trim(strip_tags((string) $publication->getLocalizedFullTitle(null, 'text')));
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
        if ((int) $context->getData('publishingMode') === Journal::PUBLISHING_MODE_OPEN) {
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
            'headline' => trim(strip_tags((string) $publication->getLocalizedFullTitle(null, 'text'))),
            'isPartOf' => [
                '@type' => 'Periodical',
                '@id' => $journalUrl . '#journal',
                'name' => $journalName,
                'url' => $journalUrl,
            ],
        ];
        if ($language = substr((string) ($publication->getData('locale') ?: $article->getData('locale')), 0, 2)) {
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
        foreach ($this->items($publication->getData('authors')) as $author) {
            $person = ['@type' => 'Person', 'name' => $author->getFullName(false)];
            $orgs = [];
            foreach ($this->items($author->getAffiliations()) as $affiliation) {
                $name = $this->cleanText($affiliation->getLocalizedName());
                $ror = $this->safeUrl($affiliation->getRor(), ['https']);
                if ($name === '' && $ror === '') {
                    continue;
                }
                $org = ['@type' => 'Organization', 'name' => $name !== '' ? $name : preg_replace('#^https://ror\.org/#', 'ROR ', $ror)];
                if ($ror !== '') {
                    $org['sameAs'] = $ror;
                }
                $orgs[] = $org;
            }
            if ($orgs) {
                $person['affiliation'] = count($orgs) === 1 ? $orgs[0] : $orgs;
            }
            if ($orcid = $this->safeUrl($author->getData('orcid'))) {
                $person['sameAs'] = $orcid;
            }
            $authors[] = $person;
        }
        if ($authors) {
            $data['author'] = $authors;
        }

        if ($keywords = $this->keywordNames($publication->getLocalizedData('keywords'))) {
            $data['keywords'] = $keywords;
        }

        if ($publication->getLocalizedData('coverImage') && ($imageUrl = $publication->getLocalizedCoverImageUrl($context->getId()))) {
            $data['image'] = $imageUrl;
        }

        if ($doi = trim((string) $publication->getDoi())) {
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

    /**
     * Arrays and Laravel (Lazy)Collections as a plain list. OJS 3.5 returns LazyCollections for
     * authors and galleys; an (array) cast would expose object internals instead of the items.
     */
    protected function items($value)
    {
        if (is_array($value)) {
            return array_values($value);
        }
        if ($value instanceof \Traversable) {
            return iterator_to_array($value, false);
        }
        return [];
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

    /**
     * Sanitise journal-manager HTML (Editorial Team, Custom Pages, license terms) with the
     * HTMLPurifier instance OJS ships. The allowlist is the server's [security] allowed_html plus
     * structural tags these pages rely on (headings, tables, blockquote). Scripts, iframes, style
     * and event attributes are always removed. Security audit T-1.
     */
    protected function richHtml($html)
    {
        static $purifier = null;
        $html = (string) $html;
        if (trim($html) === '') {
            return '';
        }
        if ($purifier === null) {
            $allowed = trim((string) Config::getVar('security', 'allowed_html'));
            $extra = 'h2,h3,h4,h5,h6,blockquote,hr,span,div,small,abbr[title],'
                . 'table,caption,thead,tbody,tfoot,tr,th[colspan|rowspan|scope],td[colspan|rowspan],'
                . 'a[href|target|title|rel],img[src|alt|width|height]';
            $config = \HTMLPurifier_Config::createDefault();
            $config->set('Core.Encoding', 'UTF-8');
            $config->set('HTML.Doctype', 'HTML 4.01 Transitional');
            $config->set('HTML.Allowed', ($allowed !== '' ? $allowed . ',' : '') . $extra);
            $config->set('Attr.AllowedFrameTargets', ['_blank']);
            $config->set('URI.AllowedSchemes', ['http' => true, 'https' => true, 'mailto' => true, 'tel' => true]);
            $config->set('Cache.SerializerPath', 'cache');
            $purifier = new \HTMLPurifier($config);
        }
        return $purifier->purify($html);
    }

    protected function html($value)
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Accept only absolute http(s) URLs; anything else (javascript:, data:, relative) is dropped.
     */
    protected function safeUrl($value, $schemes = ['http', 'https'])
    {
        $value = trim((string) $value);
        if ($value === '' || !filter_var($value, FILTER_VALIDATE_URL)) {
            return '';
        }
        return in_array(strtolower((string) parse_url($value, PHP_URL_SCHEME)), $schemes, true) ? $value : '';
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
            // Options saved under OJS 3.3 keep their old keys after an upgrade ("id_ID" for "id",
            // "en_US" for "en"). Read them as a fallback; the database is never rewritten.
            foreach ($value as $storedLocale => $candidate) {
                if (strpos((string) $storedLocale, $candidateLocale . '_') === 0 && trim((string) $candidate) !== '') {
                    return trim((string) $candidate);
                }
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
