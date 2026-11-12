/**
 * NusantaraReborn — progressive enhancements (OJS 3.3 and 3.5 packages).
 * Developed by Ikhwan Arief (ikhwan[at]unand.ac.id).
 * Copyright (c) 2026 Ikhwan Arief. Copyright held by the application developer.
 * Code license: GPL-3.0-or-later.
 *
 * Navigation opening/closing stays with the OJS Default Theme script. Every function here only
 * enhances content that is already complete without JavaScript, and touches nr_ markup only.
 */
(function($) {
    'use strict';

    // Compare URLs without query string, hash or trailing slash.
    function normalise(href) {
        var link = document.createElement('a');
        link.href = href;
        return (link.origin + link.pathname).replace(/\/+$/, '');
    }

    $(function() {
        // Mark external links in rich journal-managed content without changing OJS routing.
        $('.nr_richtext a[href^="http"], .nr_additional_content a[href^="http"]').each(function() {
            if (this.hostname && this.hostname !== window.location.hostname) {
                $(this).attr('rel', 'noopener noreferrer');
            }
        });

        // Highlight the primary menu item that matches the current page. The Home
        // link is marked server-side, so it is skipped here.
        if (!$('.nr_nav_home.nr_current').length) {
            var here = normalise(window.location.href);
            $('#navigationPrimary a').each(function() {
                var href = $(this).attr('href');
                if (href && href !== '#' && normalise(this.href) === here) {
                    $(this).attr('aria-current', 'page')
                        .closest('#navigationPrimary > li').addClass('nr_current');
                    return false;
                }
            });
        }

        // Expose the Default Theme menu state to assistive technology.
        var toggle = document.querySelector('.nr_nav_toggle');
        var menu = document.getElementById('nrSiteNav');
        if (toggle && menu) {
            var syncToggle = function() {
                toggle.setAttribute('aria-expanded', menu.classList.contains('pkp_site_nav_menu--isOpen') ? 'true' : 'false');
            };
            syncToggle();
            toggle.addEventListener('click', function() { window.setTimeout(syncToggle, 0); });
        }

        // Close the mobile navigation after a normal menu link is selected.
        $('.nr_nav a').on('click', function() {
            if (window.innerWidth < 992 && $(this).attr('href') !== '#') {
                $('.pkp_site_nav_menu').removeClass('pkp_site_nav_menu--isOpen');
                $('.pkp_site_nav_toggle').removeClass('pkp_site_nav_toggle--transform').attr('aria-expanded', 'false');
            }
        });

        addParentBreadcrumb(normalise);
        bindCopyButtons();
        bindDropdowns();
        bindReferences();
        bindYearFilter();
        enhancePolicyPage();
        enhanceEditorialTeam();
    });

    function text(node) {
        return (node && node.textContent || '').replace(/\s+/g, ' ').trim();
    }

    // Custom pages under a primary-menu parent get the parent in the breadcrumb (Home / Policies / Page).
    function addParentBreadcrumb(normalise) {
        var crumbs = document.querySelector('.cmp_breadcrumbs ol');
        if (!crumbs || crumbs.children.length !== 2 || document.querySelector('.page_article, .page_issue_archive')) {
            return;
        }
        var here = normalise(window.location.href);
        var match = Array.prototype.filter.call(document.querySelectorAll('#navigationPrimary > li > ul a'), function(link) {
            return normalise(link.href) === here;
        })[0];
        if (!match) {
            return;
        }
        var parentLink = match.closest('#navigationPrimary > li').querySelector(':scope > a');
        var label = parentLink.cloneNode(true);
        Array.prototype.forEach.call(label.querySelectorAll('*'), function(child) { child.parentNode.removeChild(child); });
        var item = document.createElement('li');
        var node = parentLink.getAttribute('href') && parentLink.getAttribute('href') !== '#' ? document.createElement('a') : document.createElement('span');
        if (node.tagName === 'A') {
            node.href = parentLink.href;
        }
        node.textContent = text(label);
        item.appendChild(node);
        var separator = crumbs.children[0].querySelector('.separator');
        if (separator) {
            item.appendChild(document.createTextNode(' '));
            item.appendChild(separator.cloneNode(true));
        }
        crumbs.insertBefore(item, crumbs.children[1]);
    }

    function copyText(value, button) {
        var done = function() {
            button.classList.add('is-copied');
            setTimeout(function() { button.classList.remove('is-copied'); }, 1800);
        };
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(value).then(done);
            return;
        }
        var area = document.createElement('textarea');
        area.value = value;
        area.style.position = 'fixed';
        area.style.opacity = '0';
        document.body.appendChild(area);
        area.select();
        try { if (document.execCommand('copy')) { done(); } } catch (e) { /* copy unsupported */ }
        document.body.removeChild(area);
    }

    function bindCopyButtons() {
        document.addEventListener('click', function(event) {
            var button = event.target.closest && event.target.closest('[data-nr-copy], [data-nr-copy-target]');
            if (!button) {
                return;
            }
            event.preventDefault();
            var value = button.getAttribute('data-nr-copy');
            if (!value) {
                value = text(document.getElementById(button.getAttribute('data-nr-copy-target')));
            }
            if (value) {
                copyText(value, button);
            }
        });
    }

    function bindDropdowns() {
        var dropdowns = document.querySelectorAll('details.nr_dropdown');
        if (!dropdowns.length) {
            return;
        }
        document.addEventListener('click', function(event) {
            Array.prototype.forEach.call(dropdowns, function(dropdown) {
                if (dropdown.open && !dropdown.contains(event.target)) { dropdown.open = false; }
            });
        });
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                Array.prototype.forEach.call(dropdowns, function(dropdown) { dropdown.open = false; });
            }
        });
    }

    // References: the first three are shown; "View All References" reveals the rest.
    function bindReferences() {
        var list = document.getElementById('nrReferenceList');
        var toggles = document.querySelectorAll('[data-nr-references-toggle]');
        if (!list || !toggles.length) {
            return;
        }
        if (!list.classList.contains('is-collapsed') || list.children.length <= 3) {
            list.classList.remove('is-collapsed');
            Array.prototype.forEach.call(toggles, function(toggle) { toggle.hidden = true; });
            return;
        }
        Array.prototype.forEach.call(toggles, function(toggle) {
            toggle.addEventListener('click', function() {
                list.classList.remove('is-collapsed');
                Array.prototype.forEach.call(toggles, function(other) {
                    other.setAttribute('aria-expanded', 'true');
                    other.hidden = true;
                });
            });
        });
    }

    function bindYearFilter() {
        var select = document.querySelector('[data-nr-year-filter]');
        if (!select) {
            return;
        }
        select.addEventListener('change', function() {
            Array.prototype.forEach.call(document.querySelectorAll('.nr_archive_year'), function(section) {
                section.hidden = !!select.value && section.getAttribute('data-year') !== select.value;
            });
        });
    }

    var POLICY_ICONS = [
        [/focus|scope|aim/, 'fa-bullseye'],
        [/peer.?review|review process|reviewer/, 'fa-users'],
        [/ethic|malpractice|cope|misconduct/, 'fa-shield'],
        [/open access/, 'fa-unlock-alt'],
        [/charge|apc|fee|payment|biaya/, 'fa-database'],
        [/copyright|licen|hak cipta/, 'fa-copyright'],
        [/archiv|preserv|pengarsipan/, 'fa-archive'],
        [/plagiar|similarity|plagiasi/, 'fa-search'],
        [/contact|kontak/, 'fa-envelope-o'],
        [/author|guideline|penulis/, 'fa-file-text-o'],
        [/privacy|privasi/, 'fa-lock']
    ];

    // Policy pages: numbered sections with icons, "On this page" navigation and highlighted notes.
    function enhancePolicyPage() {
        var page = document.querySelector('.nr_policy_page');
        var toc = document.querySelector('.nr_toc');
        if (!page || !toc) {
            return;
        }
        var title = page.querySelector(':scope > h1');
        var headings = Array.prototype.slice.call(page.querySelectorAll(':scope > h2'));
        if (headings.length < 2) {
            return;
        }
        page.classList.add('is-structured');
        var list = toc.querySelector('ol');
        var addEntry = function(target, label) {
            var item = document.createElement('li');
            var link = document.createElement('a');
            link.href = '#' + target.id;
            link.textContent = label;
            item.appendChild(link);
            list.appendChild(item);
            return link;
        };
        title.id = title.id || 'nr-policy-top';
        var links = [addEntry(title, text(title))];
        var targets = [title];

        var intro = document.createElement('div');
        intro.className = 'nr_policy_intro';
        var node = title.nextElementSibling;
        while (node && node !== headings[0]) {
            var next = node.nextElementSibling;
            intro.appendChild(node);
            node = next;
        }
        title.insertAdjacentElement('afterend', intro);

        headings.forEach(function(heading, index) {
            var label = text(heading);
            var key = label.toLowerCase();
            var icon = 'fa-file-text-o';
            POLICY_ICONS.some(function(rule) {
                if (rule[0].test(key)) { icon = rule[1]; return true; }
                return false;
            });
            heading.id = heading.id || 'nr-' + (key.replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '') || 'section-' + (index + 1));
            var section = document.createElement('section');
            section.className = 'nr_policy_section';
            section.innerHTML = '<span class="nr_policy_icon fa ' + icon + '" aria-hidden="true"></span><div class="nr_policy_body"></div>';
            heading.parentNode.insertBefore(section, heading);
            var body = section.querySelector('.nr_policy_body');
            var current = heading;
            while (current && current !== headings[index + 1]) {
                var following = current.nextElementSibling;
                body.appendChild(current);
                current = following;
            }
            heading.insertBefore(document.createTextNode((index + 1) + '. '), heading.firstChild);
            links.push(addEntry(heading, label));
            targets.push(heading);

            Array.prototype.forEach.call(body.querySelectorAll('ul'), function(ul) {
                if (ul.children.length >= 6) { ul.classList.add('nr_two_cols'); }
            });
            Array.prototype.forEach.call(body.querySelectorAll('blockquote'), function(quote) {
                var content = text(quote);
                var kind = /(IDR|Rp|USD|US\$|€|£)\s?[0-9]/.test(content) ? 'money' : (/@|phone|telepon|email/i.test(content) ? 'contact' : 'info');
                quote.classList.add('nr_callout', 'is-' + kind);
                quote.insertAdjacentHTML('afterbegin', '<span class="nr_callout_icon fa ' + (kind === 'money' ? 'fa-usd' : (kind === 'contact' ? 'fa-envelope' : 'fa-info')) + '" aria-hidden="true"></span>');
            });
            Array.prototype.forEach.call(body.querySelectorAll('p'), function(paragraph) {
                var only = paragraph.children.length === 1 && paragraph.firstElementChild.tagName === 'A' && text(paragraph) === text(paragraph.firstElementChild);
                if (only) { paragraph.classList.add('nr_more_link'); }
            });
        });
        toc.hidden = false;

        // Highlight the section currently in view.
        if ('IntersectionObserver' in window) {
            var setActive = function(index) {
                links.forEach(function(link, i) { link.parentNode.classList.toggle('is-active', i === index); });
            };
            setActive(0);
            var observer = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) { setActive(targets.indexOf(entry.target)); }
                });
            }, { rootMargin: '0px 0px -70% 0px' });
            targets.forEach(function(target) { observer.observe(target); });
        }
    }

    // Editorial Team: editor lists become profile cards (Editor-in-Chief, Managing Editors, or any
    // list with photos) or tables (other boards), with country flags from the last line of each entry.
    function enhanceEditorialTeam() {
        var masthead = document.querySelector('.nr_masthead');
        var mapNode = document.querySelector('script.nr-country-map');
        if (!masthead) {
            return;
        }
        var countries = {};
        try { countries = JSON.parse(mapNode ? mapNode.textContent : '{}') || {}; } catch (e) { countries = {}; }

        var parseEntry = function(li) {
            var image = li.querySelector('img');
            var nameNode = li.querySelector('strong, b');
            var nameAnchor = nameNode ? nameNode.closest('a') : null;
            if (!nameAnchor && nameNode) {
                nameAnchor = nameNode.querySelector('a');
            }
            var nameUrl = nameAnchor ? nameAnchor.getAttribute('href') : '';
            var name = text(nameNode);
            var clone = li.cloneNode(true);
            Array.prototype.forEach.call(clone.querySelectorAll('img, strong, b, a'), function(el) { el.parentNode.removeChild(el); });
            var parts = clone.innerHTML.split(/<br\s*\/?>|\s[–—-]\s/i).map(function(part) {
                var holder = document.createElement('div');
                holder.innerHTML = part;
                return text(holder).replace(/^[,;–—\-\[\]()\s]+/, '').replace(/[,;–—\-\[\]()\s]+$/, '');
            }).filter(Boolean);
            var country = null;
            if (parts.length && countries[parts[parts.length - 1].toLowerCase()]) {
                var last = parts.pop();
                country = { name: last, flag: countries[last.toLowerCase()].flag };
            }
            var links = Array.prototype.filter.call(li.querySelectorAll('a[href]'), function(a) {
                return a !== nameAnchor && (!nameNode || !a.contains(nameNode));
            });
            var role = parts.length >= 2 ? parts.shift() : '';
            return { name: name || parts.shift() || '', nameUrl: nameUrl, role: role, affiliation: parts.join(', '), country: country, image: image ? image.getAttribute('src') : '', links: links };
        };
        var flagHtml = function(country) {
            if (!country) { return ''; }
            var span = document.createElement('span');
            span.className = 'nr_country';
            span.innerHTML = '<span class="nr_flag" aria-hidden="true"></span><span></span>';
            span.firstChild.textContent = country.flag;
            span.lastChild.textContent = country.name;
            return span.outerHTML;
        };
        var escapeHtml = function(value) {
            var div = document.createElement('div');
            div.textContent = value;
            return div.innerHTML;
        };
        var formatName = function(entry) {
            var escaped = escapeHtml(entry.name);
            if (entry.nameUrl) {
                return '<a href="' + escapeHtml(entry.nameUrl) + '" target="_blank" rel="noopener noreferrer">' + escaped + '</a>';
            }
            return escaped;
        };
        var initials = function(name) {
            return name.replace(/\b(Prof|Dr|Ir|Eng|rer|nat)\.?\s*/gi, '').split(/[\s,]+/).filter(function(w) { return /^[A-Z]/.test(w); }).slice(0, 2).map(function(w) { return w[0]; }).join('');
        };

        Array.prototype.forEach.call(masthead.querySelectorAll('h2'), function(heading) {
            heading.classList.add('nr_title');
            var list = heading.nextElementSibling;
            while (list && list.tagName !== 'UL' && list.tagName !== 'H2') { list = list.nextElementSibling; }
            if (!list || list.tagName !== 'UL') {
                return;
            }
            var entries = Array.prototype.map.call(list.children, parseEntry);
            var key = text(heading).toLowerCase();
            var asCards = /chief|managing|pemimpin|pelaksana/.test(key) || entries.some(function(entry) { return entry.image; });
            var wrapper = document.createElement('div');
            if (asCards) {
                wrapper.className = 'nr_people' + (entries.length === 1 ? ' is-single' : '');
                entries.forEach(function(entry) {
                    var card = document.createElement('div');
                    card.className = 'nr_person';
                    card.innerHTML = (entry.image
                        ? '<img class="nr_person_photo" src="' + escapeHtml(entry.image) + '" alt="" loading="lazy">'
                        : '<span class="nr_person_photo nr_person_initials" aria-hidden="true">' + escapeHtml(initials(entry.name)) + '</span>') +
                        '<div class="nr_person_text"><strong>' + formatName(entry) + '</strong>' +
                        (entry.role ? '<span class="nr_role">' + escapeHtml(entry.role) + '</span>' : '') +
                        (entry.affiliation ? '<span>' + escapeHtml(entry.affiliation) + '</span>' : '') +
                        flagHtml(entry.country) + '</div>';
                    entry.links.forEach(function(link) {
                        var copy = link.cloneNode(true);
                        copy.classList.add('nr_person_link');
                        copy.setAttribute('target', '_blank');
                        copy.setAttribute('rel', 'noopener noreferrer');
                        card.querySelector('.nr_person_text').appendChild(copy);
                    });
                    wrapper.appendChild(card);
                });
            } else {
                var hasRole = entries.some(function(entry) { return entry.role; });
                wrapper.className = 'nr_people_table' + (hasRole ? ' has-role' : '');
                entries.forEach(function(entry) {
                    var row = document.createElement('div');
                    row.className = 'nr_people_row';
                    var nameCol = document.createElement('div');
                    nameCol.className = 'nr_person_name_col';
                    nameCol.innerHTML = '<strong>' + formatName(entry) + '</strong>';
                    if (entry.links && entry.links.length) {
                        var linksWrap = document.createElement('div');
                        linksWrap.className = 'nr_person_links';
                        entry.links.forEach(function(link) {
                            var copy = link.cloneNode(true);
                            copy.classList.add('nr_person_link');
                            copy.setAttribute('target', '_blank');
                            copy.setAttribute('rel', 'noopener noreferrer');
                            linksWrap.appendChild(copy);
                        });
                        nameCol.appendChild(linksWrap);
                    }

                    row.appendChild(nameCol);
                    if (hasRole) {
                        var roleCol = document.createElement('span');
                        roleCol.className = 'nr_role';
                        roleCol.textContent = entry.role || '';
                        row.appendChild(roleCol);
                    }
                    var affCol = document.createElement('span');
                    affCol.textContent = entry.affiliation || '';
                    row.appendChild(affCol);

                    var countryCol = document.createElement('div');
                    countryCol.innerHTML = flagHtml(entry.country) || '<span></span>';
                    row.appendChild(countryCol.firstChild);

                    wrapper.appendChild(row);
                });
            }
            list.parentNode.replaceChild(wrapper, list);
        });
    }
})(jQuery);
