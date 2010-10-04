<?php
    $gitwikiScript = "
        $(document).ready(function(){
            $('.wiki-menu ul ul').hide();
            $('.wiki-menu ul li a.selected').parents('ul').show();
            $('.wiki-menu a.selected').next().show();
        });";
    $html->scriptBlock($gitwikiScript, array('inline' => false));
?>
<div class="wiki index">
    <div class="breadcrumb">
    <?php
        echo $html->link('Home', '/');
        echo ' &rarr; ';
        echo $html->link(ucfirst(Configure::read('Gitwiki.route_prefix')), '/' . Configure::read('Gitwiki.route_prefix'), array('class' => (strlen($route) == 0) ? 'selected' : ''));
        if (strlen($route) > 0) {
            foreach ($breadcrumbs AS $r => $t) {
                echo ' &rarr; ';
                echo $html->link($t, '/' . Configure::read('Gitwiki.route_prefix') . '/' . $r, array('class' => ($route == $r) ? 'selected' : false));
            }
        }
    ?>
    </div>

    <h1><?php echo $title; ?></h1>

    <div class="grid_11">
        <div class="wiki-content">
        <?php echo $this->Gitwiki->transform($body); ?>
        </div>

        <?php if (is_array($children) && count($children) > 0) { ?>
        <div class="wiki-children">
            <h3><?php __('Content'); ?></h3>
            <ul>
            <?php
                foreach ($children AS $child) {
                    echo '<li>';
                    echo $html->link($child['title'], '/'. Configure::read('Gitwiki.route_prefix') . '/' . $child['route']);
                    echo '</li>';
                }
            ?>
            </ul>
        </div>
        <?php } ?>
    </div>

    <div class="grid_5">
        <div class="wiki-menu">
        <?php echo $gitwiki->menu($thread, $route); ?>
        </div>
    </div>

    <div class="clear"></div>
</div>