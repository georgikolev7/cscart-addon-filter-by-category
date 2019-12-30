<?php

use Tygh\Registry;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

function fn_filter_by_categories_get_product_filter_fields(&$filters)
{
    $filters['C'] = array (
        'db_field' => 'category_id',
        'variant_name_field' => 'products_categories.category_id',
        'table' => 'products',
        'description' => 'categories',
        'condition_type' => 'F',
        'slider' => false,
        'conditions' => function($db_field, $join, $condition) {
            $db_field = 'products_categories.category_id';
            $join = '';
            $condition .= db_quote(" AND ?:categories.status = ?s ", 'A');
            return array($db_field, $join, $condition);
        },
    );
}

function fn_filter_by_categories_get_products_before_select(&$params, &$join, &$condition, $u_condition, $inventory_join_cond, &$sortings, $total, $items_per_page, $lang_code, $having)
{
    if (!empty($params['filter_params']) && !empty($params['filter_params']['category_id'])) {

        $_ids = db_get_fields(
            "SELECT a.category_id"."
             FROM ?:categories as a"."
             LEFT JOIN ?:categories as b"."
             ON b.category_id IN (?n)"."
             WHERE a.id_path LIKE CONCAT(b.id_path, '/%')",
            $params['filter_params']['category_id']
        );

        $cids = fn_array_merge($params['filter_params']['category_id'], $_ids, false);

        $condition .= db_quote(" AND ?:categories.category_id IN (?n)", $cids);
        $params['disable_searchanise'] = true;
        unset($params['filter_params']['category_id']);
    }
}

function fn_filter_by_categories_get_product_filters_before_select($fields, $join, &$condition, $group_by, $sorting, $limit, $params, $lang_code)
{
    if (isset($params['fbc'])) {
        if (AREA == 'A') {
            if (isset($params['feature_type'])) {
                $_condition = db_quote("?:product_features.feature_type IN (?a)", $params['feature_type']);

                $condition = str_replace($_condition, '(' . $_condition . ' OR ?:product_filters.field_type = "C" )', $condition);
            }
        }
    }
}

function fn_filter_by_categories_get_product_filters_post(&$filters, $params, $lang_code)
{
    if (isset($params['fbc']) && !empty($params['get_variants']) && AREA == 'C') {
        foreach ($filters as &$filter) {
            if ($filter['field_type'] == 'C') {
                $params = array (
                    'plain' => true,
                );

                list($categories, ) = fn_get_categories($params, $lang_code);

                $filter['variants'] = array();
                foreach ($categories as $c) {
                    $filter['variants'][] = array(
                        'variant' => $c['category'],
                        'variant_id' => $c['category_id']
                    );
                }
            }
        }
    }
}

function fn_filter_by_categories_get_filters_products_count_post($params, $lang_code, &$filters)
{
    if (!empty($filters)) {
        foreach ($filters as &$f) {
            if (empty($f['feature_id']) && $f['field_type'] == 'C') {
                if (!empty($f['variants'])) {
                    foreach ($f['variants'] as &$v) {
                        $v['variant'] = fn_get_category_name($v['variant_id'], $lang_code);
                    }
                    $f['variants'] = fn_sort_array_by_key($f['variants'], 'variant');
                    $active_variants = $disabled_variants = array();
                    foreach ($f['variants'] as $vid => $v) {
                        if (!empty($v['disabled'])) {
                            $disabled_variants[$vid] = $v;
                        } else {
                            $active_variants[$vid] = $v;
                        }
                    }
                    $f['variants'] = fn_array_merge($active_variants, $disabled_variants);
                }
                if (!empty($f['selected_variants'])) {
                    foreach ($f['selected_variants'] as &$v) {
                        $v['variant'] = fn_get_category_name($v['variant_id'], $lang_code);
                    }
                    $f['selected_variants'] = fn_sort_array_by_key($f['selected_variants'], 'variant');
                }
            }
        }
    }
}