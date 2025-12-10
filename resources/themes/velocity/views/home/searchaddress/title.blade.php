@php
    $channel = core()->getCurrentChannel();
    $homeSEO = $channel->home_seo;

    if (isset($homeSEO)) {
        $homeSEO = json_decode($channel->home_seo);

        $metaTitle = $homeSEO->meta_title;
        $metaDescription = $homeSEO->meta_description;
        $metaKeywords = $homeSEO->meta_keywords;

        echo $metaTitle; // or use "print $metaTitle;"
    }
@endphp
