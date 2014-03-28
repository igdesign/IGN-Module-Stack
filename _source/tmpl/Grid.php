<?php // no direct acces
defined( '_JEXEC' ) or die( 'Restricted access');

$document = JFactory::getDocument();

if ($params->get('use_js', true)) {
  $document->addScript(JURI::base() . 'media/mod_stack/js/picturefill.js');
}

if ($params->get('use_css', true)) {
  $document->addStyleSheet(JURI::base() . 'media/mod_stack/css/mod_grid.css');
}




$template   = $params->get('template', 'Grid');
$moduleclass_sfx = $params->get('moduleclass_sfx', $template);


$breakpoint = $params->get('breakpoint', 480);
$breakpoint_images = $params->get('breakpoint_images', array(480, 768));
$item_count = count($list);

?>


<div class="<?php echo $moduleclass_sfx; ?>__container"><!--

  <?php foreach($list as $index=>$item) : ?>

  --><article class="<?php echo $moduleclass_sfx; ?>__item">
    <figure class="<?php echo trim($moduleclass_sfx); ?>__figure">
      <div class="<?php echo trim($moduleclass_sfx); ?>__img-wrapper">
        <img src="<?php echo $item->introimage['image']; ?>" alt="<?php echo $item->introimage['alt']; ?>"/>

      </div>

      <figcaption class="<?php echo trim($moduleclass_sfx); ?>__caption">
        <h1 class="<?php echo trim($moduleclass_sfx); ?>__title"><?php echo $item->content_title; ?></h1>
        <div class="<?php echo trim($moduleclass_sfx); ?>__text"><?php echo $item->content_introtext; ?></div>
        <a class="<?php echo trim($moduleclass_sfx); ?>__link" href="<?php echo $item->link; ?>">Read More&hellip;</a>
      </figcaption>
    </figure>
  </article><!--

  <?php endforeach; ?>



--></div>
