<?php

namespace Tests\Feature\RichEditor;

use App\Filament\Forms\Components\RichEditor\RichContentCustomBlocks\QuizBlock;
use App\Models\Cluster;
use App\Models\Content;
use Tests\FilamentTestCase;

class QuizBlockTest extends FilamentTestCase
{
    // -------------------------------------------------------------------------
    // Metadata
    // -------------------------------------------------------------------------

    public function test_has_correct_id(): void
    {
        $this->assertSame('quiz', QuizBlock::getId());
    }

    // -------------------------------------------------------------------------
    // Preview label
    // -------------------------------------------------------------------------

    public function test_preview_label_without_heading_has_no_colon(): void
    {
        $label = QuizBlock::getPreviewLabel(['questions' => []]);

        $this->assertStringNotContainsString(':', $label);
    }

    public function test_preview_label_with_heading_appends_heading(): void
    {
        $label = QuizBlock::getPreviewLabel(['heading' => 'My Quiz', 'questions' => []]);

        $this->assertStringContainsString('My Quiz', $label);
        $this->assertStringContainsString(':', $label);
    }

    public function test_preview_label_includes_question_count(): void
    {
        $questions = [
            ['question' => 'Q1', 'answers' => []],
            ['question' => 'Q2', 'answers' => []],
            ['question' => 'Q3', 'answers' => []],
        ];

        $label = QuizBlock::getPreviewLabel(['questions' => $questions]);

        $this->assertStringContainsString('(3)', $label);
    }

    public function test_preview_label_with_zero_questions_has_no_count(): void
    {
        $label = QuizBlock::getPreviewLabel(['questions' => []]);

        $this->assertStringNotContainsString('(', $label);
    }

    // -------------------------------------------------------------------------
    // toHtml – structure
    // -------------------------------------------------------------------------

    public function test_to_html_renders_heading_when_present(): void
    {
        $html = QuizBlock::toHtml(
            ['heading' => 'Main Heading', 'questions' => [], 'results' => []],
            []
        );

        $this->assertStringContainsString('Main Heading', $html);
        $this->assertStringContainsString('<h2', $html);
    }

    public function test_to_html_omits_h2_when_heading_absent(): void
    {
        $html = QuizBlock::toHtml(
            ['questions' => [], 'results' => []],
            []
        );

        $this->assertStringNotContainsString('<h2', $html);
    }

    public function test_to_html_renders_intro_when_present(): void
    {
        $html = QuizBlock::toHtml(
            ['intro' => 'Intro paragraph.', 'questions' => [], 'results' => []],
            []
        );

        $this->assertStringContainsString('Intro paragraph.', $html);
    }

    public function test_to_html_renders_question_text_and_answers(): void
    {
        $html = QuizBlock::toHtml(
            [
                'questions' => [[
                    'question' => 'Are you a morning person?',
                    'answers' => [
                        ['label' => 'Absolutely yes', 'points' => 5],
                        ['label' => 'Not at all', 'points' => 1],
                    ],
                ]],
                'results' => [],
            ],
            []
        );

        $this->assertStringContainsString('Are you a morning person?', $html);
        $this->assertStringContainsString('Absolutely yes', $html);
        $this->assertStringContainsString('Not at all', $html);
        $this->assertStringContainsString('5', $html);
        $this->assertStringContainsString('1', $html);
    }

    public function test_to_html_renders_result_heading_body_and_range(): void
    {
        $html = QuizBlock::toHtml(
            [
                'questions' => [],
                'results' => [[
                    'heading' => 'Klare Lerche',
                    'body' => 'Du bist morgens am aktivsten.',
                    'min_points' => 5,
                    'max_points' => 9,
                ]],
            ],
            []
        );

        $this->assertStringContainsString('Klare Lerche', $html);
        $this->assertStringContainsString('Du bist morgens am aktivsten.', $html);
        $this->assertStringContainsString('5', $html);
        $this->assertStringContainsString('9', $html);
    }

    public function test_to_html_embeds_config_json_for_js_rendering(): void
    {
        $html = QuizBlock::toHtml(
            [
                'heading' => 'Quiz Title',
                'questions' => [[
                    'question' => 'Config Question',
                    'answers' => [['label' => 'A', 'points' => 1]],
                ]],
                'results' => [],
            ],
            []
        );

        $this->assertStringContainsString('quiz-block__config', $html);
        $this->assertStringContainsString('Config Question', $html);
    }

    // -------------------------------------------------------------------------
    // toHtml – internal link
    // -------------------------------------------------------------------------

    public function test_to_html_renders_internal_link_when_post_selected(): void
    {
        $cluster = Cluster::factory()->create();
        $post = Content::factory()->published()->create([
            'cluster_id' => $cluster->id,
            'title' => 'Linked Article',
        ]);

        $html = QuizBlock::toHtml(
            [
                'questions' => [],
                'results' => [[
                    'heading' => 'A',
                    'body' => 'a',
                    'min_points' => 0,
                    'max_points' => 10,
                    'link_post_id' => $post->id,
                ]],
            ],
            []
        );

        $this->assertStringContainsString('Linked Article', $html);
        $this->assertStringContainsString('internal-link', $html);
    }

    public function test_to_html_omits_link_when_post_id_missing(): void
    {
        $html = QuizBlock::toHtml(
            [
                'questions' => [],
                'results' => [[
                    'heading' => 'A',
                    'body' => 'a',
                    'min_points' => 0,
                    'max_points' => 10,
                ]],
            ],
            []
        );

        $this->assertStringNotContainsString('internal-link', $html);
    }

    public function test_to_html_omits_link_for_unknown_post(): void
    {
        $html = QuizBlock::toHtml(
            [
                'questions' => [],
                'results' => [[
                    'heading' => 'A',
                    'body' => 'a',
                    'min_points' => 0,
                    'max_points' => 10,
                    'link_post_id' => 99999,
                ]],
            ],
            []
        );

        $this->assertStringNotContainsString('internal-link', $html);
    }

    public function test_to_html_link_with_arrow_class_when_enabled(): void
    {
        $cluster = Cluster::factory()->create();
        $post = Content::factory()->published()->create(['cluster_id' => $cluster->id]);

        $html = QuizBlock::toHtml(
            [
                'questions' => [],
                'results' => [[
                    'heading' => 'A',
                    'body' => 'a',
                    'min_points' => 0,
                    'max_points' => 10,
                    'link_post_id' => $post->id,
                    'link_with_arrow' => true,
                ]],
            ],
            []
        );

        $this->assertStringContainsString('internal-link--with-arrow', $html);
    }

    public function test_to_html_link_without_arrow_class_when_disabled(): void
    {
        $cluster = Cluster::factory()->create();
        $post = Content::factory()->published()->create(['cluster_id' => $cluster->id]);

        $html = QuizBlock::toHtml(
            [
                'questions' => [],
                'results' => [[
                    'heading' => 'A',
                    'body' => 'a',
                    'min_points' => 0,
                    'max_points' => 10,
                    'link_post_id' => $post->id,
                    'link_with_arrow' => false,
                ]],
            ],
            []
        );

        $this->assertStringNotContainsString('internal-link--with-arrow', $html);
    }

    // -------------------------------------------------------------------------
    // toPreviewHtml
    // -------------------------------------------------------------------------

    public function test_preview_html_renders_question_text(): void
    {
        $html = QuizBlock::toPreviewHtml(
            [
                'questions' => [['question' => 'Preview Question', 'answers' => []]],
                'results' => [],
            ]
        );

        $this->assertStringContainsString('Preview Question', $html);
    }

    public function test_preview_html_renders_result_heading(): void
    {
        $html = QuizBlock::toPreviewHtml(
            [
                'questions' => [],
                'results' => [[
                    'heading' => 'Preview Result',
                    'min_points' => 0,
                    'max_points' => 5,
                ]],
            ]
        );

        $this->assertStringContainsString('Preview Result', $html);
    }

    public function test_preview_html_shows_empty_state_when_no_content(): void
    {
        $html = QuizBlock::toPreviewHtml(['questions' => [], 'results' => []]);

        $this->assertStringContainsString('No quiz content yet', $html);
    }
}
