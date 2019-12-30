<?php

if (!defined('BOOTSTRAP')) { die('Access denied'); }

fn_register_hooks(
    array('get_products_before_select', 1100),
    'get_product_filter_fields',
    'get_product_filters_before_select',
    'get_product_filters_post',
    'get_filters_products_count_post'
);