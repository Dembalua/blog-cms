<?php

return [
    'faq' => [
        'label'    => 'FAQ',
        'add_item' => 'Frage hinzufügen',
        'fields'   => [
            'heading'  => 'Überschrift (optional)',
            'items'    => 'Fragen & Antworten',
            'question' => 'Frage',
            'answer'   => 'Antwort',
        ],
    ],
    'quiz' => [
        'label'        => 'Quiz',
        'add_question' => 'Frage hinzufügen',
        'add_answer'   => 'Antwort hinzufügen',
        'add_result'   => 'Ergebnis hinzufügen',
        'defaults'     => [
            'cta_label' => 'Ergebnis anzeigen',
        ],
        'fields' => [
            'heading'        => 'Überschrift (optional)',
            'intro'          => 'Einleitung (optional)',
            'cta_label'      => 'Button-Text für Auswertung',
            'questions'      => 'Fragen',
            'question'       => 'Frage',
            'answers'        => 'Antworten',
            'answer_label'   => 'Antworttext',
            'answer_points'  => 'Punkte',
            'results'        => 'Ergebnisbereiche',
            'min_points'     => 'Punkte von',
            'max_points'     => 'Punkte bis',
            'result_heading' => 'Ergebnistitel',
            'result_body'    => 'Ergebnistext',
            'result_link'    => 'Weiterführender Link (optional)',
        ],
    ],
    'internal_link' => [
        'label'         => 'Interner Link',
        'modal_heading' => 'Internen Link einfügen',
        'fields'        => [
            'category'   => 'Kategorie',
            'cluster'    => 'Cluster',
            'article'    => 'Artikel',
            'with_arrow' => 'Pfeil anzeigen',
        ],
    ],
    'affiliate_link' => [
        'label'         => 'Affiliate-Link',
        'modal_heading' => 'Affiliate-Link einfügen',
        'fields'        => [
            'network' => 'Partnerprogramm',
            'link'    => 'Link',
        ],
    ],
    'contact_form' => [
        'label'               => 'Kontaktformular-Button',
        'default_button_text' => 'Kontakt aufnehmen',
        'fields'              => [
            'button_text' => 'Button-Text',
        ],
    ],
    'sections' => [
        'label'    => 'Wellenabschnitte',
        'add_item' => 'Abschnitt hinzufügen',
        'fields'   => [
            'heading'      => 'Überschrift (optional)',
            'intro'        => 'Einleitung (optional)',
            'items'        => 'Abschnitte',
            'item_heading' => 'Abschnittstitel',
            'item_body'    => 'Text',
            'item_link'    => 'Weiterführender Link (optional)',
        ],
    ],
    'image' => [
        'label'  => 'Bild',
        'fields' => [
            'image' => 'Bild',
        ],
    ],
    'image_text' => [
        'label'  => 'Bild & Text',
        'fields' => [
            'image'                => 'Bild',
            'heading'              => 'Überschrift',
            'body'                 => 'Text',
            'image_position'       => 'Bildposition',
            'image_position_right' => 'Rechts',
            'image_position_left'  => 'Links',
            'link_section'         => 'Interner Link (optional)',
            'body_link_hint'       => 'Schreibe {link} an die Stelle im Text wo der Link erscheinen soll',
            'link_page'            => 'Seite',
        ],
    ],
    'post_list' => [
        'label'    => 'Beitragsliste (Zeilen)',
        'add_item' => 'Beitrag hinzufügen',
        'fields'   => [
            'heading'  => 'Überschrift',
            'intro'    => 'Einleitung (optional)',
            'count'    => 'Anzahl Zeilen',
            'items'    => 'Beiträge',
            'category' => 'Kategorie',
            'cluster'  => 'Cluster',
            'post'     => 'Beitrag',
        ],
        'preview' => [
            'count_label' => 'Zeilen',
        ],
    ],
    'post_cards_small' => [
        'label'  => 'Beitragskarten (klein)',
        'fields' => [
            'heading'             => 'Überschrift',
            'intro'               => 'Einleitung (optional)',
            'count'               => 'Anzahl Beiträge',
            'source'              => 'Quelle',
            'source_latest'       => 'Neueste Beiträge',
            'source_category'     => 'Nach Kategorie',
            'source_cluster'      => 'Nach Cluster',
            'category'            => 'Kategorie',
            'cluster'             => 'Cluster',
            'show_category_link'  => 'Link zur Kategorie-Übersicht anzeigen',
        ],
        'preview' => [
            'source_latest'   => 'Neueste Beiträge',
            'source_category' => 'Beiträge einer Kategorie',
            'source_cluster'  => 'Beiträge eines Clusters',
            'count_label'     => 'Karten',
        ],
    ],
    'post_cards_big' => [
        'label'  => 'Beitragskarten (groß)',
        'fields' => [
            'heading'             => 'Überschrift',
            'intro'               => 'Einleitung (optional)',
            'count'               => 'Anzahl Beiträge',
            'source'              => 'Quelle',
            'source_latest'       => 'Neueste Beiträge',
            'source_category'     => 'Nach Kategorie',
            'source_cluster'      => 'Nach Cluster',
            'category'            => 'Kategorie',
            'cluster'             => 'Cluster',
            'show_category_link'  => 'Link zur Kategorie-Übersicht anzeigen',
        ],
        'preview' => [
            'source_latest'   => 'Neueste Beiträge',
            'source_category' => 'Beiträge einer Kategorie',
            'source_cluster'  => 'Beiträge eines Clusters',
            'count_label'     => 'Karten',
        ],
    ],
];
