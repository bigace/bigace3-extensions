<?php
/**
 * File-Listing for Bigace 3
 *
 * The module displays all files linked to a choosen category.
 *
 * Copyright (C) Kevin Papst
 *
 * For further information go to:
 * https://www.keleo.de/service/bigace-cms/freebies.html
 *
 * @version $Id: modul.php,v 1.18 2009/05/26 23:04:03 kpapst Exp $
 * @author Kevin Papst
 * @package bigace.modul
 */

// ----------------------------------------------------------
// Module code starts here
import('classes.util.LinkHelper');
import('classes.file.File');
import('classes.category.Category');
import('classes.category.CategoryService');
import('classes.util.IOHelper');

define('FL_PRJ_NUM_CATEGORY', 'filelisting_category');
define('FL_PRJ_NUM_DESCRIPTION', 'filelisting_show_description');
define('FL_PRJ_NUM_ORDER_NAME', 'filelisting_sort_name');
define('FL_PRJ_NUM_ORDER_POS', 'filelisting_sort_position');
define('FL_PRJ_NUM_ORDER_REVERSE', 'filelisting_sort_reverse');
define('FL_PRJ_NUM_CSS', 'filelisting_use_css');

$menu           = $this->MENU;
$modul          = new Modul($menu->getModulID());
$catService     = new CategoryService();
$projectService = new Bigace_Item_Project_Numeric(_BIGACE_ITEM_MENU);

$this->t()->load('filelisting', $menu->getLanguageID());

/* #########################################################################
 * ############################  Show Admin Link  ##########################
 * #########################################################################
 */
if ($modul->isModulAdmin()) {
    $this->adminBar()->addModuleConfigLink();
}


/* #########################################################################
 * ##################  Show List of all categorized Images  ################
 * #########################################################################
 */

// get image category
$curCat = $projectService->get($menu, FL_PRJ_NUM_CATEGORY);

if ($curCat === null) {
    echo '<br><b>'.getTranslation('gallery_unconfigured').'</b><br>';
    return;
}

$useCSS = $projectService->get($menu, FL_PRJ_NUM_CSS);
if ($useCSS === null) {
    $useCSS = false;
}

// show description of images?
$showDesc = $projectService->get($menu, FL_PRJ_NUM_DESCRIPTION);
if ($showDesc === null) {
    $showDesc = false;
}

// order by name
$orderByName = $projectService->get($menu, FL_PRJ_NUM_ORDER_NAME);
if ($orderByName === null) {
    $orderByName = false;
}

// order by position
$orderByPosition = $projectService->get($menu, FL_PRJ_NUM_ORDER_POS);
if ($orderByPosition === null) {
    $orderByPosition = false;
}

// order by position
$orderReverse = $projectService->get($menu, FL_PRJ_NUM_ORDER_REVERSE);
if ($orderReverse === null) {
    $orderReverse = false;
}

// ... and now fetch all linked images
$search = $catService->getItemsForCategory(_BIGACE_ITEM_FILE, $curCat);

if ($useCSS) {
    echo '<link rel="stylesheet" href="'.$this->directory().'filelisting/styles.css" type="text/css" media="screen" />'."\n";
}

// TODO add option to choose whether to display content or not
echo $this->content()->withDefaultContent();

if ($search->count() < 1) {
    echo '<b>'.getTranslation('gallery_empty').'</b>';
    return;
}

$allFiles = array();

// ---------------------------------
if ($orderByName) {
    while ($search->hasNext()) {
        $temp = $search->next();
        $temp = new File($temp['itemid']);
        $allFiles[$temp->getName()] = $temp;
    }
    if($orderReverse)
        krsort($allFiles, SORT_STRING);
    else
        ksort($allFiles, SORT_STRING);

} else if ($orderByPosition) {
    while ($search->hasNext()) {
        $temp = $search->next();
        $temp = new File($temp['itemid']);
        $allFiles[$temp->getPosition()] = $temp;
    }
    if($orderReverse)
        krsort($allFiles, SORT_NUMERIC);
    else
        ksort($allFiles, SORT_NUMERIC);

} else {
    while ($search->hasNext()) {
        $temp = $search->next();
        $temp = new File($temp['itemid']);
        $allFiles[$temp->getID()] = $temp;
    }
    if($orderReverse)
        krsort($allFiles, SORT_NUMERIC);
    else
        ksort($allFiles, SORT_NUMERIC);
}
// ---------------------------------

echo '<div id="fileListing">';
foreach ($allFiles AS $key => $temp) {
    $link     = LinkHelper::getCMSLinkFromItem($temp);
    $fileURL  = LinkHelper::getUrlFromCMSLink($link);
    $mimetype = " " . IOHelper::getFileExtension($temp->getOriginalName());
    ?>
    <div class="entry">
        <a href="<?php echo $fileURL; ?>" class="download<?php echo $mimetype; ?>"><?php echo $temp->getName(); ?></a>
        <?php
        if ($showDesc) {
            echo '<span class="caption">'.$temp->getDescription().'</span>';
        }
        echo '<span class="stats">'.getTranslation('gallery_date').': '.date('d.m.Y H:i', $temp->getLastDate()).'</span>';
        ?>
    </div>
    <?php
}
echo '</div><div class="fileListingFooter"><a href="http://www.keleo.de/service/bigace-cms/freebies.html" title="BIGACE Freebies" target="_blank">File-Listing</a> is powered by <a href="http://www.keleo.de" target="_blank" title="Webdesign Bonn">Keleo</a></div>';