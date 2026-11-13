<?php

return [
    // Navigation Groups
    'navigation_groups' => [
        'user_management' => 'User Management',
        'editorial' => 'Editorial',
        'system' => 'System',
        'affiliate' => 'Affiliate',
    ],

    // Default Form Fields
    'default_form_fields' => [
        'name' => 'Name',
        'image' => 'Image',
        'created_at' => 'Created at',
        'updated_at' => 'Updated at',
        'all' => 'All',
        'active' => 'Active',
        'inactive' => 'Inactive',
        'type' => 'Type',
        'signs' => 'Signs',
        'slug_reserved' => 'This slug is reserved and cannot be used.',
    ],

    // User Resource
    'user_resource' => [
        'navigation' => [
            'model_label' => 'User',
            'plural_model_label' => 'Users',
        ],
        'form' => [
            'role' => 'Role',
            'role_description' => 'Select the role for the user.',
            'roles' => 'Roles',
            'personal_information' => 'Personal Information',
            'personal_information_description' => 'Manage the user basic details and email address.',
            'last_name' => 'Last Name',
            'email' => 'E-Mail',
            'password' => 'Password',
            'password_description' => 'Leave blank when editing to avoid changing the password',
            'password_confirmation' => 'Confirm Password',
            'email_verified_at' => 'E-Mail Verified at',
        ],
    ],

    // Category Resource
    'category_resource' => [
        'navigation' => [
            'model_label' => 'Category',
            'plural_model_label' => 'Categories',
        ],
        'delete_blocked_title' => 'Cannot delete',
        'delete_blocked_menu_body' => 'This category is still linked in a menu.',
        'delete_blocked_contents_body' => 'This category still has articles.',
        'form' => [
            'category_section_title' => 'Category',
            'category_section_description' => 'Create and manage your Category.',
            'active' => 'Active',
            'path' => 'URL',
            'path_hint' => 'Automatically generated from the name when creating.',
            'path_tooltip' => 'Warning: Changing existing urls can have SEO implications.',
            'description' => 'Description',
            'category_image_label' => 'Category Image',
            'category_image_button_label' => 'Image upload',
        ],
        'tabs' => [
            'general' => 'General',
            'seo' => 'SEO',
        ],
    ],
    // Category Resource
    'content_resource' => [
        'navigation' => [
            'model_label' => 'Content',
            'plural_model_label' => 'Content',
        ],
        'form' => [
            'content_section_title' => 'Content',
            'content_section_description' => 'Create and manage your Content.',
            'type' => 'Content Type',
            'status' => 'Status',
            'title' => 'Title',
            'path' => 'URL',
            'path_tooltip' => 'Warning: Changing existing urls can have SEO implications.',
            'preview_title' => 'Preview',
            'preview_description' => 'Short description and preview image for map views.',
            'feature_image' => 'Preview Image',
            'feature_image_button_label' => 'Image upload',
            'feature_image_hint' => 'Tip: Create a card variant in Media for optimal loading times',
            'excerpt' => 'Short description',
            'content' => 'Content',
            'is_featured' => 'Featured',
            'published_at' => 'Published At',
            'revised_at' => 'Revised At',
            'author' => 'Author',

            'category' => 'Category',
            'category_disabled_menu' => 'This content is directly assigned to a menu and cannot be assigned to a category.',
            'cluster' => 'Cluster',
            'is_pillar' => 'Pillar',
            'cluster_position' => 'Position',
            'cluster_position_placeholder' => 'Select number',
            'published_from' => 'Published from',
            'published_until' => 'Published until',
        ],
        'tabs' => [
            'general' => 'General',
            'seo' => 'SEO',
            'links' => 'Links',
        ],
        'inbound_links' => [
            'count' => ':count article(s) link to this content',
            'empty' => 'No articles link to this content.',
        ],
        'filters' => [
            'no_category' => 'Without Category',
            'no_cluster' => 'Without Cluster',
        ],
        'validation' => [
            'pillar_exists' => 'A pillar is already set for this cluster.',
            'url_exists' => 'The URL ":url" is already taken.',
        ],
    ],
    // Cluster Resource
    'cluster_resource' => [
        'navigation' => [
            'model_label' => 'Cluster',
            'plural_model_label' => 'Clusters',
        ],
        'form' => [
            'section_title' => 'Cluster',
            'section_description' => 'Create and manage your topic clusters.',
            'title' => 'Title',
            'related_clusters' => 'Related Clusters',
            'related_clusters_helper' => 'Up to 3 clusters that will be linked in the pillar article.',
        ],
        'contents_widget' => [
            'heading' => 'Articles in this Cluster',
            'is_pillar' => 'Pillar',
        ],
    ],

    // Affiliate Link Resource
    'affiliate_link_resource' => [
        'navigation' => [
            'model_label' => 'Affiliate Link',
            'plural_model_label' => 'Affiliate Links',
        ],
        'form' => [
            'section_title' => 'Affiliate Link',
            'network' => 'Network',
            'name' => 'Name',
            'slug' => 'Slug',
            'slug_tooltip' => 'Warning: Changing the slug creates a redirect entry.',
            'url' => 'Target URL',
            'is_active' => 'Active',
        ],
        'table' => [
            'clicks' => 'Clicks',
            'articles_count' => 'Articles',
            'last_status_code' => 'Last Status',
            'last_checked_at' => 'Last Checked',
        ],
        'filters' => [
            'unused' => 'Unused links',
        ],
        'delete_blocked_title' => 'Cannot delete',
        'delete_blocked_body' => 'This link is still used in articles.',
        'usage_widget' => [
            'heading' => 'Used in articles',
            'clicks' => 'Clicks',
            'last_clicked_at' => 'Last click',
            'unassigned' => 'Clicks without article attribution: :count',
        ],
    ],

    // Affiliate Network Resource
    'affiliate_network_resource' => [
        'navigation' => [
            'model_label' => 'Network',
            'plural_model_label' => 'Networks',
        ],
        'form' => [
            'section_title' => 'Affiliate Network',
        ],
        'table' => [
            'links_count' => 'Links',
        ],
        'delete_blocked_title' => 'Cannot delete',
        'delete_blocked_body' => 'This network still has affiliate links assigned to it.',
        'links_widget' => [
            'heading' => 'Assigned Links',
        ],
    ],

    // Homepage Resource
    'homepage_resource' => [
        'navigation' => [
            'model_label' => 'Homepage',
            'plural_model_label' => 'Homepages',
        ],
        'form' => [
            'general_section_title' => 'General',
            'general_section_description' => 'Internal name and metadata of this homepage variant.',
            'title' => 'Internal name',
            'title_hint' => 'Only visible in admin, not on the frontend.',
            'author' => 'Author',
            'published_at' => 'Published at',
            'revised_at' => 'Revised at',
            'hero_section_title' => 'Hero section',
            'hero_section_description' => 'The visible top area of the homepage.',
            'hero_kicker' => 'Kicker (small overline)',
            'hero_headline' => 'Headline',
            'hero_headline_accent' => 'Headline accent (second part, italic & accent color)',
            'hero_text' => 'Hero text',
            'hero_text_hint' => 'Line breaks become paragraphs.',
            'hero_image' => 'Hero image',
            'hero_image_button_label' => 'Select image',
            'content_section_title' => 'Content below hero',
            'content_section_description' => 'Additional blocks and rich content shown below the hero.',
        ],
        'table' => [
            'active' => 'Active',
            'active_badge' => 'Live',
        ],
        'validation' => [
            'active_cannot_delete' => 'This homepage is currently active. Please select a different homepage in the settings first.',
        ],
    ],

    // Menu Resource
    'menu_resource' => [
        'navigation' => [
            'model_label' => 'Menu',
            'plural_model_label' => 'Menus',
        ],
        'form' => [
            'section_title' => 'Menu',
            'section_description' => 'Create and manage your Menu.',
        ],
        'widget' => [
            'tree_title' => 'Menu Items',
            'parent' => 'Parent Item',
            'parent_helper' => 'Leave empty if this should be a top-level menu item',
            'model_type_label' => 'Type',
            'mode_type_options' => [
                'page' => 'Content: PAGE',
                'post' => 'Content: POST',
                'category' => 'Categories',
            ],
            'model_id_label' => 'Content',
            'model_id_hint' => 'Content already assigned to a category will not appear here.',
            'title_label' => 'Title',
            'title_hint' => 'Automatically generated from the selected content',
            'title_tooltip' => 'Edit title',
        ],
    ],
];
