<!doctype html>
<html amp>
	<head>
		<meta charset="utf-8">
		<title>
            {$meta.meta_title}
        </title>
        <link rel="canonical" href="{$canonical}">
		<meta name="viewport" content="width=device-width,minimum-scale=1,initial-scale=1">
        {literal}
			<style amp-boilerplate>body{-webkit-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-moz-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-ms-animation:-amp-start 8s steps(1,end) 0s 1 normal both;animation:-amp-start 8s steps(1,end) 0s 1 normal both}@-webkit-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-moz-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-ms-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-o-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}</style><noscript><style amp-boilerplate>body{-webkit-animation:none;-moz-animation:none;-ms-animation:none;animation:none}</style></noscript>
			<script async src="https://cdn.ampproject.org/v0.js"></script>
			<script async custom-element="amp-carousel" src="https://cdn.ampproject.org/v0/amp-carousel-0.1.js"></script>
	        <script async custom-element="amp-analytics" src="https://cdn.ampproject.org/v0/amp-analytics-0.1.js"></script>
			<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,700&display=swap" rel="stylesheet"> 

        {/literal}
		<style amp-custom>
            {$css nofilter}
        </style>
	</head>
	<body>
	{hook h='ampAnalytics' mod='mdamp'}
	<div class="page-body-amp">
		<div class="header-column-amp">
			<a href="{$urls.pages.index}">
				<amp-img src="{$shop.logo}"
					width="250"
					height="99"
					id="shop-logo-amp"
					alt="{l s='Shop logo' mod='mdamp'}">
				</amp-img>
			</a>
		</div>
		<div class="page-content-amp">
				{* {if $category->id_image}
					<div id="category-image-amp">
						<amp-img src="{url entity='categoryImage' id=$category->id_category name='category_default'}"
							width="141"
							height="180"
							layout="responsive"
							alt="{$categoryAMP.name}"></amp-img>
						</amp-carousel>
					</div>
				{/if} *}

			<h1 id="category-name-amp">
				<a href="{url entity='category' id=$category->id_category id_lang=$language.id}">
					{$categoryAMP.name}
				</a>
			</h1>
			<div class="rte width-full float-left">
				{$categoryAMP.clean_description nofilter}
			</div>
			<div class="width-full float-left">
		        <div class="float-left pagination-text">
					<span>
						{l s='Showing ' mod='mdamp'} {$currentStart|escape:'html':'UTF-8'} - {$currentStop|escape:'html':'UTF-8'} {l s=' of ' mod='mdamp'} {$nbProducts|escape:'html':'UTF-8'} {l s=' items.' mod='mdamp'}
					</span>
				</div>
                {if !isset($current_url)}
                    {assign var='requestPage' value=$link->getPaginationLink('category', $category, false, false, true, false)}
                {else}
                    {assign var='requestPage' value=$current_url}
                {/if}
                {if $start!=$stop}
					<div class="pagination-block">
						<ul class="pagination">
                            {if $p != 1}
                                {assign var='p_previous' value=$p-1}
								<li id="pagination_previous{if isset($paginationId)}_{$paginationId|escape:'quotes':'UTF-8'}{/if}" class="pagination_previous">
									<a href="{$link->goPage($requestPage, $p_previous)|escape:'quotes':'UTF-8'}" rel="prev">
										&lt; <b>{l s='Previous' mod='mdamp'}</b>
									</a>
								</li>
                            {/if}
                            {if $start==3}
								<li>
									<a href="{$link->goPage($requestPage, 1)|escape:'quotes':'UTF-8'}">
										<span>1</span>
									</a>
								</li>
								<li>
									<a href="{$link->goPage($requestPage, 2)|escape:'quotes':'UTF-8'}">
										<span>2</span>
									</a>
								</li>
                            {/if}
                            {if $start==2}
								<li>
									<a href="{$link->goPage($requestPage, 1)|escape:'quotes':'UTF-8'}">
										<span>1</span>
									</a>
								</li>
                            {/if}
                            {if $start>3}
								<li>
									<a href="{$link->goPage($requestPage, 1)|escape:'quotes':'UTF-8'}">
										<span>1</span>
									</a>
								</li>
								<li class="truncate">
									<span>
										<span>...</span>
									</span>
								</li>
                            {/if}
                            {section name=pagination start=$start loop=$stop+1 step=1}
                                {if $p == $smarty.section.pagination.index}
									<li class="active current">
										<span>
											<span>{$p|escape:'html':'UTF-8'}</span>
										</span>
									</li>
                                {else}
									<li>
										<a href="{$link->goPage($requestPage, $smarty.section.pagination.index)|escape:'quotes':'UTF-8'}">
											<span>{$smarty.section.pagination.index|escape:'html':'UTF-8'}</span>
										</a>
									</li>
                                {/if}
                            {/section}
                            {if $pages_nb>$stop+2}
								<li class="truncate">
									<span>
										<span>...</span>
									</span>
								</li>
								<li>
									<a href="{$link->goPage($requestPage, $pages_nb)|escape:'quotes':'UTF-8'}">
										<span>{$pages_nb|intval}</span>
									</a>
								</li>
                            {/if}
                            {if $pages_nb==$stop+1}
								<li>
									<a href="{$link->goPage($requestPage, $pages_nb)|escape:'quotes':'UTF-8'}">
										<span>{$pages_nb|intval}</span>
									</a>
								</li>
                            {/if}
                            {if $pages_nb==$stop+2}
								<li>
									<a href="{$link->goPage($requestPage, $pages_nb-1)|escape:'quotes':'UTF-8'}">
										<span>{$pages_nb-1|intval}</span>
									</a>
								</li>
								<li>
									<a href="{$link->goPage($requestPage, $pages_nb)|escape:'quotes':'UTF-8'}">
										<span>{$pages_nb|intval}</span>
									</a>
								</li>
                            {/if}
                            {if $pages_nb > 1 AND $p != $pages_nb}
                                {assign var='p_next' value=$p+1}
								<li id="pagination_next{if isset($paginationId)}_{$paginationId|escape:'html':'UTF-8'}{/if}" class="pagination_next">
									<a href="{$link->goPage($requestPage, $p_next)|escape:'quotes':'UTF-8'}" rel="next">
										<b>{l s='Next' mod='mdamp'}</b> &gt;
									</a>
								</li>
                            {/if}
						</ul>
					</div>
                {/if}
			</div>
            {foreach from=$catProducts item=product}
				<div class="product-header">
					<div>
						<amp-img src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'home_default')}"
								 width="80"
								 height="80"
								 layout="responsive"
								 class="product-image-amp"
								 alt="{$product.name}"></amp-img>
					</div>
					<h2 class="width-float product-name-amp">
						<a href="{$product.ampLink}" title="{$product.name}">
                            {$product.name|truncate:40:'...'}
						</a>
					</h2>
					<p class="product-price-amp">
						<span id="product-price-amp">{$product.price}</span>
						<span id="product-price-old-amp">{$product.price_old}</span>
					</p>
					<p class="product-add-to-cart-amp {if $product.quantity == 0} disabled {/if}">
						<a class="btn btn-primary" {if $product.quantity == 0} href="#" {else} href="{$product.addToCartLink}" {/if}>
							{l s='Add to cart' mod='mdamp'}
						</a>
					</p>
				</div>
            {/foreach}
		</div>
		<div id="full-version-link">
			<a href="{$canonical}" title="{l s='See full version' mod='mdamp'}">{l s='See full version' mod='mdamp'}</a>
		</div>
	</div>
	<footer>
		&copy;  {$shop.name} - {date('Y')}
	</footer>	
</body>
</html>