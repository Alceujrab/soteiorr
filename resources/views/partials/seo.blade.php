<meta name="description" content="{{ $seoDescription }}">
<meta name="keywords" content="{{ $seoKeywords }}">
<meta name="robots" content="index, follow, max-image-preview:large">
<link rel="canonical" href="{{ $seoCanonical }}">

<meta property="og:locale" content="pt_BR">
<meta property="og:type" content="website">
<meta property="og:site_name" content="{{ $seoSiteName }}">
<meta property="og:title" content="{{ $seoTitle }}">
<meta property="og:description" content="{{ $seoDescription }}">
<meta property="og:url" content="{{ $seoCanonical }}">
<meta property="og:image" content="{{ $seoImage }}">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $seoTitle }}">
<meta name="twitter:description" content="{{ $seoDescription }}">
<meta name="twitter:image" content="{{ $seoImage }}">

@if ($seoVerification)
    <meta name="google-site-verification" content="{{ $seoVerification }}">
@endif

<script type="application/ld+json">{!! json_encode($seoOrganization, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
<script type="application/ld+json">{!! json_encode($seoWebsite, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
