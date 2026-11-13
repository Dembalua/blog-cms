@php
    $config = [
        'heading'   => $heading,
        'intro'     => $intro,
        'cta_label' => $cta_label,
        'questions' => $questions,
        'results'   => $results,
    ];
@endphp

<section class="quiz-block" data-type="quiz">
    <span class="quiz-block__config" style="display:none">{{ json_encode($config, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</span>

    @if($heading)
        <h2>{{ $heading }}</h2>
    @endif

    @if($intro)
        <p>{!! nl2br(e($intro)) !!}</p>
    @endif

    <ol class="quiz-block__static-questions">
        @foreach($questions as $question)
            <li>
                <p class="quiz-block__static-question">{{ $question['question'] }}</p>
                <ul>
                    @foreach($question['answers'] ?? [] as $answer)
                        @php $points = (int) ($answer['points'] ?? 0); @endphp
                        <li>{{ $answer['label'] }} <span class="quiz-block__static-points">&rarr; {{ $points }} {{ $points === 1 ? 'point' : 'points' }}</span></li>
                    @endforeach
                </ul>
            </li>
        @endforeach
    </ol>

    <div class="quiz-block__static-results">
        @foreach($results as $result)
            @php
                $min = (int) ($result['min_points'] ?? 0);
                $max = (int) ($result['max_points'] ?? 0);
            @endphp
            <article>
                <h3>{{ $result['heading'] }} <span class="quiz-block__result-range">({{ $min }}&ndash;{{ $max }} points)</span></h3>
                <p>{!! nl2br(e($result['body'])) !!}</p>
                @if(!empty($result['link_html']))
                    <p class="quiz-block__result-link">{!! $result['link_html'] !!}</p>
                @endif
            </article>
        @endforeach
    </div>
</section>
