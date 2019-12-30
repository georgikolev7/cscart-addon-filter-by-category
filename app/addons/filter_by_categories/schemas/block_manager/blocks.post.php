<?php

$schema['product_filters_home']['content']['items']['fillings']['manually']['params']['fbc'] = true;
$schema['product_filters_home']['content']['items']['fillings']['manually']['picker_params']['extra_url'] .= '&fbc=true';

$schema['product_filters']['content']['items']['fillings']['manually']['params']['request']['pcode_from_q'] = '%PCODE_FROM_Q%';
$schema['product_filters']['content']['items']['fillings']['manually']['params']['disable_searchanise'] = true;

return $schema;