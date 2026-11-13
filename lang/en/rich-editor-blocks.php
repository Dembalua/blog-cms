<?php

return [
    'faq' => [
        'label'    => 'FAQ',
        'add_item' => 'Add question',
        'fields'   => [
            'heading'  => 'Heading (optional)',
            'items'    => 'Questions & Answers',
            'question' => 'Question',
            'answer'   => 'Answer',
        ],
    ],
    'quiz' => [
        'label'        => 'Quiz',
        'add_question' => 'Add question',
        'add_answer'   => 'Add answer',
        'add_result'   => 'Add result',
        'defaults'     => [
            'cta_label' => 'Show result',
        ],
        'fields' => [
            'heading'        => 'Heading (optional)',
            'intro'          => 'Intro (optional)',
            'cta_label'      => 'Button label for result',
            'questions'      => 'Questions',
            'question'       => 'Question',
            'answers'        => 'Answers',
            'answer_label'   => 'Answer text',
            'answer_points'  => 'Points',
            'results'        => 'Result ranges',
            'min_points'     => 'Points from',
            'max_points'     => 'Points to',
            'result_heading' => 'Result heading',
            'result_body'    => 'Result text',
            'result_link'    => 'Related link (optional)',
        ],
    ],
    'internal_link' => [
        'label'         => 'Internal Link',
        'modal_heading' => 'Insert Internal Link',
        'fields'        => [
            'category'   => 'Category',
            'cluster'    => 'Cluster',
            'article'    => 'Article',
            'with_arrow' => 'Show arrow',
        ],
    ],
    'affiliate_link' => [
        'label'         => 'Affiliate Link',
        'modal_heading' => 'Insert Affiliate Link',
        'fields'        => [
            'network' => 'Partner program',
            'link'    => 'Link',
        ],
    ],
    'contact_form' => [
        'label'               => 'Contact Form Button',
        'default_button_text' => 'Get in touch',
        'fields'              => [
            'button_text' => 'Button text',
        ],
    ],
    'sections' => [
        'label'    => 'Wave sections',
        'add_item' => 'Add section',
        'fields'   => [
            'heading'      => 'Heading (optional)',
            'intro'        => 'Intro (optional)',
            'items'        => 'Sections',
            'item_heading' => 'Section heading',
            'item_body'    => 'Text',
            'item_link'    => 'Related link (optional)',
        ],
    ],
    'image' => [
        'label'  => 'Image',
        'fields' => [
            'image' => 'Image',
        ],
    ],
    'image_text' => [
        'label'  => 'Image & Text',
        'fields' => [
            'image'                => 'Image',
            'heading'              => 'Heading',
            'body'                 => 'Text',
            'image_position'       => 'Image position',
            'image_position_right' => 'Right',
            'image_position_left'  => 'Left',
            'link_section'         => 'Internal link (optional)',
            'body_link_hint'       => 'Write {link} in the text where the link should appear',
            'link_page'            => 'Page',
        ],
    ],
    'post_list' => [
        'label'    => 'Post list (rows)',
        'add_item' => 'Add post',
        'fields'   => [
            'heading'  => 'Heading',
            'intro'    => 'Intro (optional)',
            'count'    => 'Number of rows',
            'items'    => 'Posts',
            'category' => 'Category',
            'cluster'  => 'Cluster',
            'post'     => 'Post',
        ],
        'preview' => [
            'count_label' => 'rows',
        ],
    ],
    'post_cards_small' => [
        'label'  => 'Post cards (small)',
        'fields' => [
            'heading'             => 'Heading',
            'intro'               => 'Intro (optional)',
            'count'               => 'Number of posts',
            'source'              => 'Source',
            'source_latest'       => 'Latest posts',
            'source_category'     => 'By category',
            'source_cluster'      => 'By cluster',
            'category'            => 'Category',
            'cluster'             => 'Cluster',
            'show_category_link'  => 'Show link to category overview',
        ],
        'preview' => [
            'source_latest'   => 'Latest posts',
            'source_category' => 'Posts from a category',
            'source_cluster'  => 'Posts from a cluster',
            'count_label'     => 'cards',
        ],
    ],
    'post_cards_big' => [
        'label'  => 'Post cards (big)',
        'fields' => [
            'heading'             => 'Heading',
            'intro'               => 'Intro (optional)',
            'count'               => 'Number of posts',
            'source'              => 'Source',
            'source_latest'       => 'Latest posts',
            'source_category'     => 'By category',
            'source_cluster'      => 'By cluster',
            'category'            => 'Category',
            'cluster'             => 'Cluster',
            'show_category_link'  => 'Show link to category overview',
        ],
        'preview' => [
            'source_latest'   => 'Latest posts',
            'source_category' => 'Posts from a category',
            'source_cluster'  => 'Posts from a cluster',
            'count_label'     => 'cards',
        ],
    ],
];
