<?= '<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL ?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:media="http://search.yahoo.com/mrss/">
    <channel>
        <atom:link href="{{ url($meta['link']) }}" rel="self" type="application/rss+xml" />
        <title>{!! \Spatie\Feed\Helpers\Cdata::out($meta['title']) !!}</title>
        <link>{!! \Spatie\Feed\Helpers\Cdata::out(url($meta['link'])) !!}</link>
        @if(!empty($meta['image']))
        <image>
            <url>{{ $meta['image'] }}</url>
            <title>{!! \Spatie\Feed\Helpers\Cdata::out($meta['title']) !!}</title>
            <link>{!! \Spatie\Feed\Helpers\Cdata::out(url($meta['link'])) !!}</link>
        </image>
        @endif
        <description>{!! \Spatie\Feed\Helpers\Cdata::out($meta['description']) !!}</description>
        <language>{{ $meta['language'] }}</language>
        <pubDate>{{ $meta['updated'] }}</pubDate>

        @foreach($items as $item)
            <item>
                <title>{!! \Spatie\Feed\Helpers\Cdata::out($item->title) !!}</title>
                <link>{{ url($item->link) }}</link>
                <description>{!! \Spatie\Feed\Helpers\Cdata::out($item->summary) !!}</description>
                <author>{!! \Spatie\Feed\Helpers\Cdata::out($item->authorName) !!}</author>
                <pubDate>{{ $item->timestamp() }}</pubDate>
                @if(isset($item->enclosure) && $item->enclosure)
                <enclosure url="{{ $item->enclosure }}" length="{{ isset($item->enclosureLength) ? $item->enclosureLength : 0 }}" type="{{ isset($item->enclosureType) ? $item->enclosureType : 'image/jpeg' }}" />
                <media:content url="{{ $item->enclosure }}" medium="image" />
                @endif
                @foreach($item->category as $cat)
                    <category>{{ $cat }}</category>
                @endforeach
            </item>
        @endforeach
    </channel>
</rss>
