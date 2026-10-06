<title>{{ $seo->title }}</title>
@if ($seo->description)
    <meta name="description" content="{{ $seo->description }}">
@endif
@if ($seo->noindex)
    <meta name="robots" content="noindex, follow">
@endif
<link rel="canonical" href="{{ $canonical }}">

<meta property="og:type" content="website">
<meta property="og:locale" content="ru_RU">
<meta property="og:site_name" content="{{ config('seo.site_name') }}">
<meta property="og:title" content="{{ $seo->title }}">
@if ($seo->description)
    <meta property="og:description" content="{{ $seo->description }}">
@endif
<meta property="og:url" content="{{ $canonical }}">
@if ($seo->image)
    <meta property="og:image" content="{{ $seo->image }}">
@endif
