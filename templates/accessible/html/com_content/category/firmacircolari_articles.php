<?php
/**
 * @version     $Id: default_articles.php 21700 2011-06-28 04:32:41Z dextercowley $
 * @package     Joomla.Site
 * @subpackage  com_content
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');

// Create some shortcuts.
$params     = &$this->item->params;
$n          = count($this->items);
$listOrder  = $this->escape($this->state->get('list.ordering'));
$listDirn   = $this->escape($this->state->get('list.direction'));

?>

<?php //ABP: FAP override ?>
<script type="text/javascript">
    function submitform(pressbutton) {
        if (pressbutton) {
            $('adminForm').task.value = pressbutton;
        }
        if (typeof $('adminForm').onsubmit == "function") {
            $('adminForm').onsubmit();
        }
        if (typeof $('adminForm').fireEvent == "function") {
            $('adminForm').fireEvent('submit');
        }
        $('adminForm').submit();
    }

    var tableOrdering = function(order, dir, task) {
        var form = $('adminForm');
        form.filter_order.value = order;
        form.filter_order_Dir.value = dir;
        submitform(task);
    }

</script>

<?php if (empty($this->items)) : ?>

    <?php if ($this->params->get('show_no_articles',1)) : ?>
    <p><?php echo JText::_('COM_CONTENT_NO_ARTICLES'); ?></p>
    <?php endif; ?>

<?php else : ?>

<form action="<?php echo htmlspecialchars(JFactory::getURI()->toString()); ?>" method="post" id="adminForm">
    <?php if ($this->params->get('filter_field') != 'hide' || $this->params->get('show_pagination_limit')) :?>
    <fieldset class="filters">
        <?php if ($this->params->get('filter_field') != 'hide') :?>
        <legend class="hidelabeltxt">
            <?php echo JText::_('JGLOBAL_FILTER_LABEL'); ?>
        </legend>

        <div class="filter-search">
            <label class="filter-search-lbl" for="filter-search"><?php echo JText::_('COM_CONTENT_'.$this->params->get('filter_field').'_FILTER_LABEL').'&#160;'; ?></label>
            <input type="text" name="filter-search" id="filter-search" value="<?php echo $this->escape($this->state->get('list.filter')); ?>" class="inputbox" onchange="document.adminForm.submit();" title="<?php echo JText::_('COM_CONTENT_FILTER_SEARCH_DESC'); ?>" />
        </div>
        <?php endif; ?>

        <?php if ($this->params->get('show_pagination_limit')) : ?>
        <div class="display-limit">
            <label class="limit-lbl" for="limit"><?php echo JText::_('JGLOBAL_DISPLAY_NUM'); ?>&#160;</label>
            <?php echo $this->pagination->getLimitBox(); ?>
        </div>
        <?php endif; ?>

        <div class="fap-submit"><button type="submit" class="button btn btn-primary"><?php echo JText::_('FAP_FORM_SUBMIT'); ?></button></div>
    </fieldset>
    <?php endif; ?>

    <table class="category">
        <?php if ($this->params->get('show_headings')) :?>
        <thead>
            <tr>
                <th scope="col" class="list-title" id="tableOrdering">
                    <?php  echo JHtml::_('grid.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder) ; ?>
                </th>

                <?php if ($date = $this->params->get('list_show_date')) : ?>
                <th scope="col" class="list-date" id="tableOrdering2">
                    <?php echo JHtml::_('grid.sort', 'COM_CONTENT_'.$date.'_DATE', 'a.created', $listDirn, $listOrder); ?>
                </th>
                <?php endif; ?>

                <?php if ($this->params->get('list_show_author',1)) : ?>
                <th scope="col" class="list-author" id="tableOrdering3">
                    <?php echo JHtml::_('grid.sort', 'JAUTHOR', 'author', $listDirn, $listOrder); ?>
                </th>
                <?php endif; ?>

                <?php if ($this->params->get('list_show_hits',1)) : ?>
                <th scope="col" class="list-hits" id="tableOrdering4">
                    <?php echo JHtml::_('grid.sort', 'JGLOBAL_HITS', 'a.hits', $listDirn, $listOrder); ?>
                </th>
                <?php endif; ?>

                <th></th>
            </tr>
        </thead>
        <?php endif; ?>

        <tbody>

        <?php foreach ($this->items as $i => $article) : ?>
            <?php if ($this->items[$i]->state == 0) : ?>
                <tr class="system-unpublished cat-list-row<?php echo $i % 2; ?>">
            <?php else: ?>
                <tr class="cat-list-row<?php echo $i % 2; ?>" >
            <?php endif; ?>
                <?php if (in_array($article->access, $this->user->getAuthorisedViewLevels())) : ?>

                    <td class="list-title">
                        <a href="<?php echo JRoute::_(ContentHelperRoute::getArticleRoute($article->slug, $article->catid)); ?>">
                            <?php echo $this->escape($article->title); ?></a>

                        <?php if ($article->params->get('access-edit')) : ?>
                        <ul class="actions">
                            <li class="edit-icon">
                                <?php echo JHtml::_('icon.edit',$article, $params); ?>
                            </li>
                        </ul>
                        <?php endif; ?>
                    </td>

                    <?php if ($this->params->get('list_show_date')) : ?>
                    <td class="list-date">
                        <?php echo JHtml::_('date',$article->displayDate, $this->escape(
                        $this->params->get('date_format', JText::_('DATE_FORMAT_LC3')))); ?>
                    </td>
                    <?php endif; ?>

                    <?php if ($this->params->get('list_show_author',1) && !empty($article->author )) : ?>
                    <td class="list-author">
                        <?php $author =  $article->author ?>
                        <?php $author = ($article->created_by_alias ? $article->created_by_alias : $author);?>

                        <?php if (!empty($article->contactid ) &&  $this->params->get('link_author') == true):?>
                            <?php echo JHtml::_(
                                    'link',
                                    JRoute::_('index.php?option=com_contact&view=contact&id='.$article->contactid),
                                    $author
                            ); ?>

                        <?php else :?>
                            <?php echo JText::sprintf('COM_CONTENT_WRITTEN_BY', $author); ?>
                        <?php endif; ?>
                    </td>
                    <?php endif; ?>

                    <?php if ($this->params->get('list_show_hits',1)) : ?>
                    <td class="list-hits">
                        <?php echo $article->hits; ?>
                    </td>
                    <?php endif; ?>

                <?php else : // Show unauth links. ?>
                    <td>
                        <?php
                            echo $this->escape($article->title).' : ';
                            $menu       = JFactory::getApplication()->getMenu();
                            $active     = $menu->getActive();
                            $itemId     = $active->id;
                            $link = JRoute::_('index.php?option=com_users&view=login&Itemid='.$itemId);
                            $returnURL = JRoute::_(ContentHelperRoute::getArticleRoute($article->slug));
                            $fullURL = new JURI($link);
                            $fullURL->setVar('return', base64_encode($returnURL));
                        ?>
                        <a href="<?php echo $fullURL; ?>" class="register">
                            <?php echo JText::_( 'COM_CONTENT_REGISTER_TO_READ_MORE' ); ?></a>
                    </td>

                <?php endif; ?>

                    <td>
				<?php
					$buttons_style = (in_array('firmacircolari-buttons', explode(' ', $this->pageclass_sfx)) ? 'buttons' : 'text');

					if (!isset($article->asset_id))
					{
						$a = JTable::getInstance('Content');
						$a->load($article->id);
						$article->asset_id = $a->asset_id;
					}

					$buttons = array();
					foreach(ButtonsHelper::getToolbars($article, 'both') as $article->catid)
					{
						$toolbar = JTable::getInstance('Category');
						$toolbar->load($article->catid);
						$tparams = new JRegistry;
						$tparams->loadString($toolbar->params);
						$cparams = clone($article->params);
						$cparams->merge($tparams);
						$buttons[] = ButtonsHelper::getToolbar($article->catid, $article->asset_id, JFactory::getUser()->id, false, $buttons_style);
					}
					echo implode (', ', array_filter($buttons));
				 ?>
					</td>
                </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <div class="hidden">
        <input type="hidden" name="filter_order" value="" />
        <input type="hidden" name="filter_order_Dir" value="" />
        <input type="hidden" name="limitstart" value="" />
        <input type="hidden" name="task" value="" />
    </div>

<?php endif; ?>

<?php // Code to add a link to submit an article. ?>
<?php if ($this->category->getParams()->get('access-create')) : ?>
    <?php echo JHtml::_('icon.create', $this->category, $this->category->params); ?>
<?php  endif; ?>

<?php // Add pagination links ?>
<?php if (!empty($this->items)) : ?>
    <?php if (($this->params->def('show_pagination', 2) == 1  || ($this->params->get('show_pagination') == 2)) && ($this->pagination->get('pages.total') > 1)) : ?>
    <div class="pagination">

        <?php if ($this->params->def('show_pagination_results', 1)) : ?>
            <p class="counter">
                <?php echo $this->pagination->getPagesCounter(); ?>
            </p>
        <?php endif; ?>

        <?php echo $this->pagination->getPagesLinks(); ?>
    </div>
    <?php endif; ?>
</form>
<?php  endif; ?>
