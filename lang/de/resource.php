<?php

return [
    // Navigation Groups
    'navigation_groups' => [
        'user_management' => 'Benutzerverwaltung',
        'editorial' => 'Redaktion',
        'system' => 'System',
        'affiliate' => 'Affiliate',
    ],

    // Default Form Fields
    'default_form_fields' => [
        'name' => 'Name',
        'image' => 'Bild',
        'created_at' => 'Erstellt am',
        'updated_at' => 'Geändert am',
        'all' => 'Alle',
        'active' => 'Aktiv',
        'inactive' => 'Inaktiv',
        'type' => 'Typ',
        'signs' => 'Zeichen',
        'slug_reserved' => 'Dieser Slug ist reserviert und kann nicht verwendet werden.',
    ],

    // User Resource
    'user_resource' => [
        'navigation' => [
            'model_label' => 'Benutzer',
            'plural_model_label' => 'Benutzer',
        ],
        'form' => [
            'role' => 'Rolle',
            'role_description' => 'Wähle die Rolle für den Benutzer.',
            'roles' => 'Rollen',
            'personal_information' => 'Persönliche Informationen',
            'personal_information_description' => 'Verwalten Sie die Stammdaten und die E-Mail-Adresse des Benutzers.',
            'last_name' => 'Nachname',
            'email' => 'E-Mail',
            'password' => 'Passwort',
            'password_description' => 'Beim Bearbeiten leer lassen, um Passwort nicht zu ändern.',
            'password_confirmation' => 'Passwort wiederholen',
            'email_verified_at' => 'E-Mail Verifiziert am',
        ]
    ],

    // Category Resource
    'category_resource' => [
        'navigation' => [
            'model_label' => 'Kategorie',
            'plural_model_label' => 'Kategorien',
        ],
        'delete_blocked_title'          => 'Löschen nicht möglich',
        'delete_blocked_menu_body'      => 'Diese Kategorie ist noch in einem Menü verknüpft.',
        'delete_blocked_contents_body'  => 'Diese Kategorie enthält noch Artikel.',
        'form' => [
            'category_section_title' => 'Kategorie',
            'category_section_description' => 'Erstelle und verwalte deine Kategorie.',
            'active' => 'Aktiv',
            'active_helper_text' => 'Inaktive Kategorien sind im Frontend ausgeblendet.',
            'path' => 'URL',
            'path_hint' => 'Wird beim Erstellen automatisch aus dem Namen generiert',
            'path_tooltip' => 'Achtung: Das Ändern bestehender URL\'s kann SEO-Auswirkungen haben.',
            'description' => 'Beschreibung',
            'category_image_label' => 'Kategorie Bild',
            'category_image_button_label' => 'Bild auswählen',
        ],
        'tabs' => [
            'general' => 'Allgemein',
            'seo' => 'SEO',
        ],
    ],

    // Category Resource
    'content_resource' => [
        'navigation' => [
            'model_label' => 'Inhalt',
            'plural_model_label' => 'Inhalte',
        ],
        'form' => [
            'content_section_title' => 'Inhalt',
            'content_section_description' => 'Erstelle und verwalte deinen Inhalt.',
            'type' => 'Art des Inhalts',
            'status' => 'Status',
            'title' => 'Titel',
            'path' => 'URL',
            'path_tooltip' => 'Achtung: Das Ändern bestehender URL\'s kann SEO-Auswirkungen haben.',
            'preview_title' => 'Vorschau',
            'preview_description' => 'Kurzbeschreibung und Vorschaubild für Kartenansichten.',
            'feature_image' => 'Vorschaubild',
            'feature_image_hint' => 'Tipp: Card-Variante in Media anlegen für optimale Ladezeiten.',
            'feature_image_button_label' => 'Bild auswählen',
            'excerpt' => 'Kurzbeschreibung',
            'content' => 'Inhalt',
            'is_featured' => 'Hervorgehoben',
            'published_at' => 'Veröffentlichungsdatum',
            'revised_at'   => 'Überarbeitet am',
            'author' => 'Autor',

            'category' => 'Kategorie',
            'category_disabled_menu' => 'Dieser Inhalt ist direkt einem Menü zugewiesen und kann keiner Kategorie zugeordnet werden.',
            'cluster' => 'Cluster',
            'is_pillar' => 'Pilar',
            'cluster_position' => 'Position',
            'cluster_position_placeholder' => 'Nummer wählen',
            'published_from' => 'Veröffentlicht von',
            'published_until' => 'Veröffentlicht bis',
        ],
        'tabs' => [
            'general' => 'Allgemein',
            'seo'     => 'SEO',
            'links'   => 'Verlinkungen',
        ],
        'inbound_links' => [
            'count' => ':count Artikel verweisen auf diesen Inhalt',
            'empty' => 'Kein Artikel verweist auf diesen Inhalt.',
        ],
        'filters' => [
            'no_category' => 'Ohne Kategorie',
            'no_cluster' => 'Ohne Cluster',
        ],
        'validation' => [
            'pillar_exists' => 'Für diesen Cluster ist bereits ein Pilar festgelegt.',
            'url_exists'    => 'Die URL „:url" ist bereits vergeben.',
        ],
    ],
    // Cluster Resource
    'cluster_resource' => [
        'navigation' => [
            'model_label' => 'Cluster',
            'plural_model_label' => 'Cluster',
        ],
        'form' => [
            'section_title'           => 'Cluster',
            'section_description'     => 'Erstelle und verwalte deine Themen-Cluster.',
            'title'                   => 'Titel',
            'category'                => 'Kategorie',
            'related_clusters'        => 'Verwandte Cluster',
            'related_clusters_helper' => 'Bis zu 3 Cluster, die im Pillar-Artikel verlinkt werden.',
        ],
        'contents_widget' => [
            'heading' => 'Artikel in diesem Cluster',
            'is_pillar' => 'Pillar',
        ],
    ],

    // Affiliate Link Resource
    'affiliate_link_resource' => [
        'navigation' => [
            'model_label'        => 'Affiliate-Link',
            'plural_model_label' => 'Affiliate-Links',
        ],
        'form' => [
            'section_title' => 'Affiliate-Link',
            'network'       => 'Netzwerk',
            'name'          => 'Name',
            'slug'          => 'Slug',
            'slug_tooltip'  => 'Achtung: Das Ändern des Slugs legt einen Weiterleitungs-Eintrag an.',
            'url'           => 'Ziel-URL',
            'is_active'     => 'Aktiv',
        ],
        'table' => [
            'clicks'          => 'Klicks',
            'articles_count'  => 'Artikel',
            'last_status_code' => 'Letzter Status',
            'last_checked_at'  => 'Zuletzt geprüft',
        ],
        'filters' => [
            'unused' => 'Ungenutzte Links',
        ],
        'delete_blocked_title' => 'Löschen nicht möglich',
        'delete_blocked_body'  => 'Dieser Link wird noch in Artikeln verwendet.',
        'usage_widget' => [
            'heading'          => 'Verwendung in Artikeln',
            'clicks'           => 'Klicks',
            'last_clicked_at'  => 'Letzter Klick',
            'unassigned'       => 'Klicks ohne Artikel-Zuordnung: :count',
        ],
    ],

    // Affiliate Network Resource
    'affiliate_network_resource' => [
        'navigation' => [
            'model_label'        => 'Netzwerk',
            'plural_model_label' => 'Netzwerke',
        ],
        'form' => [
            'section_title' => 'Affiliate-Netzwerk',
        ],
        'table' => [
            'links_count' => 'Links',
        ],
        'delete_blocked_title' => 'Löschen nicht möglich',
        'delete_blocked_body'  => 'Diesem Netzwerk sind noch Affiliate-Links zugeordnet.',
        'links_widget' => [
            'heading' => 'Zugehörige Links',
        ],
    ],

    // Homepage Resource
    'homepage_resource' => [
        'navigation' => [
            'model_label'        => 'Startseite',
            'plural_model_label' => 'Startseiten',
        ],
        'form' => [
            'general_section_title'       => 'Allgemein',
            'general_section_description' => 'Interner Name und Metadaten dieser Startseiten-Variante.',
            'title'                       => 'Interner Name',
            'title_hint'                  => 'Nur in der Verwaltung sichtbar, nicht im Frontend.',
            'author'                      => 'Autor',
            'published_at'                => 'Veröffentlichungsdatum',
            'revised_at'                  => 'Überarbeitet am',
            'hero_section_title'          => 'Hero-Bereich',
            'hero_section_description'    => 'Der sichtbare Kopfbereich der Startseite.',
            'hero_kicker'                 => 'Kicker (kleine Vorzeile)',
            'hero_headline'               => 'Headline',
            'hero_headline_accent'        => 'Headline-Akzent (zweiter Teil, kursiv & farbig)',
            'hero_text'                   => 'Hero-Text',
            'hero_text_hint'              => 'Zeilenumbrüche werden zu Absätzen.',
            'hero_image'                  => 'Hero-Bild',
            'hero_image_button_label'     => 'Bild auswählen',
            'content_section_title'       => 'Inhalt unter dem Hero',
            'content_section_description' => 'Weitere Blöcke und Rich-Content, die unter dem Hero erscheinen.',
        ],
        'table' => [
            'active'       => 'Aktiv',
            'active_badge' => 'Live',
        ],
        'validation' => [
            'active_cannot_delete' => 'Diese Startseite ist aktuell aktiv. Bitte zuerst in den Einstellungen eine andere Startseite auswählen.',
        ],
    ],

    // Menu Resource
    'menu_resource' => [
        'navigation' => [
            'model_label' => 'Menü',
            'plural_model_label' => 'Menüs',
        ],
        'form' => [
            'section_title' => 'Menü',
            'section_description' => 'Erstelle und Verwalte dein Menü.',
        ],
        'widget' => [
            'tree_title' => 'Menüpunkte',
            'parent' => 'Untermenü von',
            'parent_helper' => 'Leer lassen wenn dies ein Hauptmenüpunkt sein soll',
            'model_type_label' => 'Typ',
            'mode_type_options' => [
                'page' => 'Inhalt: SEITE',
                'post' => 'Inhalt: POST',
                'category' => 'Kategorien',
            ],
            'model_id_label' => 'Inhalt',
            'model_id_hint' => 'Inhalte, die bereits einer Kategorie zugeordnet sind, werden hier nicht angezeigt.',
            'title_label' => 'Titel',
            'title_hint' => 'Wird automatisch aus dem gewählten Inhalt generiert',
            'title_tooltip' => 'Titel bearbeiten',
        ],
    ]
];
