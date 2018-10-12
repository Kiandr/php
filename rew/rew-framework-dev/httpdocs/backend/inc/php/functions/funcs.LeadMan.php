<?php

/**
 *
 * Return pagination information
 *
 * @param int $total_results
 * @param int $page
 * @param int $page_limit
 * @param string $url
 * @param string $anchor
 * @return string
 */
if (!function_exists('generate_pagination')) {
    function generate_pagination($total_results, $page, $page_limit, $query_string = false, $url = '', $anchor = '')
    {

        $url .= '?';
        $page = (!empty($page) && is_numeric($page)) ? $page : 1;
        $numofpages = ceil($total_results / $page_limit);
        $startpage = ($numofpages > 3) ? $page - 4 : "0";
        $startpage = ($startpage < 0) ? "0" : $startpage;
        $endpage = $numofpages;

        $endpage = ($numofpages > 3) ? $page + 3 : 3;
        $endpage = ($endpage > $numofpages) ? $numofpages : $endpage;

        /* Query String */
        if (!is_array($query_string)) {
            list(, $query) = explode('?', $_SERVER['REQUEST_URI'], 2);
            parse_str($query, $query_string);
            unset($query_string['p']);
        }

        /* Pagination Collection */
        $pagination = array();

        /* Pagination Information */
        $pagination['pages']  = $numofpages;
        $pagination['page']   = $page;

        /* Showing Results */
        $pagination['start']  = (($page * $page_limit) - $page_limit) + 1;
        $pagination['end']    = ($page * $page_limit);

        if ($numofpages > 1) {
            if ($page != 1) {
                $pageprev = $page - 1;
                // $pageprev
                $pagination['prev'] = array(
                   'url' => $url . http_build_query(array_merge($query_string, array('p' => $pageprev))) . $anchor,
                   'link' => '&lt;&lt;'
                   );
                if (1 <= $startpage) {
                    $pagination['links'][] = array(
                       'url' => $url . http_build_query(array_merge($query_string, array('p' => '1'))) . $anchor,
                       'link' => '1'
                       );
                }
            }
            for ($i = $startpage; $i < $endpage; $i++) {
                $real_page = $i + 1;
                if ($real_page != $page) {
                    $pagination['links'][] = array(
                       'url' => $url . htmlspecialchars(http_build_query(array_merge($query_string, array('p' => $real_page)))) . $anchor,
                       'link' => $real_page
                       );
                } else {
                    $pagination['links'][] = array(
                       'url' => $url . htmlspecialchars(http_build_query(array_merge($query_string, array('p' => $real_page)))) . $anchor,
                       'link' => $real_page,
                       'active' => true
                       );
                }
            }
            if (($total_results - ($page * $page_limit)) > 0) {
                $pagenext = $page + 1;
                if ($numofpages > $endpage) {
                    $pagination['links'][] = array(
                       'url' => $url . htmlspecialchars(http_build_query(array_merge($query_string, array('p' => ceil($numofpages))))) . $anchor,
                       'link' => ceil($numofpages)
                       );
                }
                $pagination['next'] = array(
                       'url' => $url . htmlspecialchars(http_build_query(array_merge($query_string, array('p' => $pagenext)))) . $anchor,
                       'link' => '&gt;&gt;'
                       );
            }
        }
        return $pagination;
    }
}
