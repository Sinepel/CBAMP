<!doctype html>
<html amp>
    <head>
        <meta charset="utf-8">
        <script async src="https://cdn.ampproject.org/v0.js"></script>
        <title>
            {$meta.meta_title}
        </title>
        <link rel="canonical" href="{$canonical}">
        <meta name="viewport" content="width=device-width,minimum-scale=1,initial-scale=1">
        {literal}
            <style amp-boilerplate>body{-webkit-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-moz-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-ms-animation:-amp-start 8s steps(1,end) 0s 1 normal both;animation:-amp-start 8s steps(1,end) 0s 1 normal both}@-webkit-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-moz-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-ms-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-o-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}</style>
            <noscript>
            <style amp-boilerplate>body{-webkit-animation:none;-moz-animation:none;-ms-animation:none;animation:none}</style>
        </noscript>
        {/literal}
        <script async custom-element="amp-carousel" src="https://cdn.ampproject.org/v0/amp-carousel-0.1.js"></script>
        <script async custom-element="amp-analytics" src="https://cdn.ampproject.org/v0/amp-analytics-0.1.js"></script>
        <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,700&display=swap" rel="stylesheet"> 

        <style amp-custom>
            {$css}
        </style>
    </head>
    <body>
        {hook h='ampAnalytics' mod='mdamp'}
        <div class="page-body-amp">
            <div class="header-column-amp">
                <a href="{$link->getPageLink('index')|escape:'html':'UTF-8'}">
                    <amp-img src="{$logo_url|escape:'html':'UTF-8'}"
                             width="250"
                             height="99"
                             id="shop-logo-amp"
                             alt="{l s='Shop logo' mod='mdamp'}">
                    </amp-img>
                </a>
            </div>
            <div class="page-content-amp">
                <div id="product-image-amp">
                    <amp-carousel width="400"
                            height="300"
                            layout="responsive"
                            type="slides"
                            autoplay
                            delay="2000">
                        <amp-img
                                src="//{$link->getImageLink($productAMP.id, $cover.id_image, 'large_default')|escape:'html':'UTF-8'}"
                                width="{$largeSize['width']}"
                                height="{$largeSize['height']}"
                                alt="{if !empty($cover.legend)}{$cover.legend|escape:'html':'UTF-8'}{else}{$productAMP.name|escape:'html':'UTF-8'}{/if}"
                                layout="responsive">
                        </amp-img>
                        {if isset($images) && count($images) > 0}
                            {foreach from=$images item=image name=thumbnails}
                                {assign var=imageIds value="`$productAMP.id`-`$image.id_image`"}
                                {if !empty($image.legend)}
                                    {assign var=imageTitle value=$image.legend|escape:'html':'UTF-8'}
                                {else}
                                    {assign var=imageTitle value=$productAMP.name|escape:'html':'UTF-8'}
                                {/if}
                                <amp-img
                                        src="//{$link->getImageLink($productAMP.link_rewrite, $imageIds, 'large_default')|escape:'html':'UTF-8'}"
                                        width="{$largeSize['width']}"
                                        height="{$largeSize['height']}"
                                        layout="responsive"
                                        alt="{$imageTitle}">
                                </amp-img>
                            {/foreach}
                        {/if}
                    </amp-carousel>
                </div>
                <h1 id="product-name-amp">
                    {$productAMP.name|escape:'html':'UTF-8'}
                </h1>
                <p id="amp-reference">{l s='Reference' mod='mdamp'}: {$productAMP.reference|escape:'html':'UTF-8'}</p>
                <p>
                    {$productAMP.clean_description|escape:'UTF-8'}
                </p>

                <!-- combinations -->
                {if (!empty($productAMP.combinations))}
                        <h2>{l s='Options' mod='amp'}</h2>
                        <select on="change:AMP.navigateTo(url=event.value)">
                        {foreach from=$productAMP.combinations item=comb}
                            <option data-id="{$comb.id_product}-{$comb.id_product_attribute}" value="{$comb.goLink}" {if $idpipa == "{$comb.id_product}-{$comb.id_product_attribute}"} selected {/if}>{$comb.attribute_designation}</option>
                        {/foreach}
                        </select>
					</div>
                {/if}
                <!--combinations -->

                <!-- Data sheet -->
                {if (!empty($productAMP.features))}
					<div id="amp-datasheet">
                        <h2>{l s='Features' mod='amp'}</h2>
						<dl class="table-data-sheet">
							{foreach from=$productAMP.features item=feature}
								{if isset($feature.value)}
							    	<dt>{$feature.name|escape:'html':'UTF-8'}</dt>
								    <dd>{$feature.value|escape:'html':'UTF-8'}</dd>
								{/if}
							{/foreach}
						</dl>
					</div>
                {/if}
                <!--end Data sheet -->

                <p>
                    <span id="product-price-amp">
                        {$productAMP.price}
                    </span>
                    <span id="product-price-old-amp">
                        {$productAMP.price_old}
                    </span>
                </p>
                <p id="product-add-to-cart-amp">
                    {capture}add=1&amp;id_product={$productAMP.id|intval}{if isset($static_token)}&amp;token={$static_token}{/if}{/capture}
                    <a href="{$link->getPageLink('cart', true, NULL, $smarty.capture.default, false)|escape:'html':'UTF-8'}" class="btn btn-primary">
                        {l s='Add to cart' mod='mdamp'}
                    </a>
                </p>
            </div>
            <div id="full-version-link">
			    <a href="{$canonical}" title="{l s='See full version' mod='mdamp'}">{l s='See full version' mod='mdamp'}</a>
		    </div>
        </div>
    </body>
</html>